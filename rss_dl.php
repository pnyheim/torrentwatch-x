#!/usr/bin/php-cgi -qd register_argc_argv=1
<?php
// rss_dl.php
// By Erik Bernhardson
//
// This program is a command line interface to torrentwatch
// 

ini_set('include_path', '.:'.dirname(realpath($argv[0])).'/php');
ini_set("precision", 4);
   
// These are our extra functions
require_once('rss_dl_utils.php');

$config_values;
$test_run = 0;
$verbosity = 0;
$func_timer = 0;

function usage() {
	global $argv;
	_debug( "$argv[0] <options> - CLI Interface to Torrent Watch\n",0);
	_debug( "           -c <dir> : Enable Cache\n",0);
	_debug( "           -C : Disable Cache\n",0);
	_debug( "           -d : skip watch folder\n",0);
	_debug( "           -D : Start torrents in watch folder\n",0);
	_debug( "           -f <file> : cron script to hook\n",0);
	_debug( "           -h : show this help\n",0);
	_debug( "           -i : install cron hook and setup default config\n",0);
	_debug( "           -nv: not verbose (default)\n",0);
	_debug( "           -q : quiet (no output)\n",0);
	_debug( "           -u : uninstall cron hook\n",0);
	_debug( "           -v : verbose output\n",0);
	_debug( "           -vv: verbose output(even more)\n",0);
	_debug( "    Note: This interface only writes to the config file when using the -i option\n",0);
}

function parse_args() {
	global $config_values, $argc, $argv, $test_run, $verbosity;
	for($i=1;$i<$argc;$i++) {
		switch($argv[$i]) {
			case '-c':
				$i++;
				$config_values['Settings']['Cache Dir'] = $argv[$i];
				break;
			case '-C':
				unset($config_values['Settings']['Cache Dir']);
				break;
			case '-d':
				$config_values['Settings']['Run Torrentwatch'] = 0;
				break;
			case '-D':
				$config_values['Settings']['Run Torrentwatch'] = 1;
				break;
			case '-f':
				$i++;
				$config_values['Settings']['Cron'] = $argv[$i];
				break;
			case '-h':
				usage();
				exit(1);
			case '-i':
				$config_values['Global']['Install'] = 1;
				break;
			case '-nv':
				$verbosity = 0;
				break;
			case '-q':
				$verbosity = -1;
				break;
			case '-t':
				$test_run = 1;
				break;
			case '-u':
				$config_values['Global']['Install'] = 2;
				break;
			case '-v':
				$verbosity = 1;
				break;
			case '-vv':
				$verbosity = 2;
				break;
			default:
				_debug("Unknown command line argument: $argv[$i]\n",0);
				break;
		}
	}
}
function setup_cron_hook() {
	global $config_values, $argv;

	_debug("Preparing to modify cron hook ...\n");
	if(!(isset($config_values['Settings']['Cron']) || !file_exists($config_values['Settings']['Cron']))) {
		_debug("No Cron file Selected\n",0);
		exit(1);
	}
	$cron = $config_values['Settings']['Cron'];
	// Check if we are already in the cron.hourly file
	// $return = 0 : already in the file
	// $return > 0 : not in file
	exec("/bin/grep -q rss_dl.php $cron", $output, $return);
	switch($config_values['Global']['Install']) {
		case 1:
			// install hook
			if($return == 0) {
				_debug("Cron hook already installed in $cron\n");
			} else {
				file_put_contents($cron, "\n".realpath($argv[0])." -D >> /var/rss_dl.log\n", FILE_APPEND);
				_debug("Cron hook installed to $cron\n",0);
			}
			break;
		case 2:
			//uninstall hook
			if($return > 0) {
				_debug("Cron hook not installed in $cron\n");
			} else {
				exec("grep -v rss_dl.php $cron > /tmp/rss_dl.tmp");
				copy('/tmp/rss_dl.tmp', $cron);
				_debug("Cron hook removed from $cron\n",0);
			}
			break;
		default:
			_debug("Unknown option ".$config_values['Setings']['Install']." passed to setup_cron_hook()\n",0);
			exit(1);
	}
	exit(0);
}

//
// Begin Main Function
//
//

	$main_timer = timer_init();
	if(file_exists(platform_getConfigFile()))
		read_config_file();
	else
		setup_default_config();

	if(isset($config_values['Settings']['Verbose']))
		$verbosity = $config_values['Settings']['Verbose'];
	parse_args();
	_debug(date("F j, Y, g:i a")."\n",0);

	if(isset($config_values['Feeds'])) {
		load_feeds($config_values['Feeds']);
		feeds_perform_matching($config_values['Feeds']);
	}

	if(_isset($config_values['Settings'], 'Run Torrentwatch', FALSE) and !$test_run) {
		global $hit;
		$hit = 0;
		check_for_torrents($config_values['Settings']['Watch Dir'], $config_values['Settings']['Download Dir']);
		if(!$hit)
			_debug("No New Torrents to add from watch folder\n", 0);
	} else {
		_debug("Skipping Watch Folder\n");
	}

	unlink_temp_files();

	_debug($func_timer."s\n",0);

	_debug(timer_get_time($main_timer)."s\n",0);
?>

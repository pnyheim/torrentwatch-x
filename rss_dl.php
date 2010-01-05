#!/usr/bin/php-cgi -q
<?php
// rss_dl.php
// By Erik Bernhardson
//
// This program is a command line interface to torrentwatch
// 

ini_set('include_path', '.:'.dirname(realpath($_SERVER['argv'][0])).'/php');
ini_set("precision", 4);
   
// These are our extra functions
require_once('rss_dl_utils.php');

$config_values;
$test_run = 0;
$verbosity = 0;
$func_timer = 0;

function usage() {
	_debug( $_SERVER['argv'][0] . "<options> - CLI Interface to Torrent Watch\n",0);
	_debug( "           -c <dir> : Enable Cache\n",0);
	_debug( "           -C : Disable Cache\n",0);
	_debug( "           -d : skip watch folder\n",0);
	_debug( "           -D : Start torrents in watch folder\n",0);
	_debug( "           -h : show this help\n",0);
	_debug( "           -nv: not verbose (default)\n",0);
	_debug( "           -q : quiet (no output)\n",0);
	_debug( "           -v : verbose output\n",0);
	_debug( "           -vv: verbose output(even more)\n",0);
	_debug( "    Note: This interface only writes to the config file when using the -i option\n",0);
}

function parse_args() {
	global $config_values, $argc, $argv, $test_run, $verbosity;
	for($i=1;$i<$argc;$i++) {
		switch( $_SERVER['argv'][$i]) {
			case '-c':
				$i++;
				$config_values['Settings']['Cache Dir'] =  $_SERVER['argv'][$i];
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
			case '-h':
				usage();
				exit(1);
			case '-nv':
				$verbosity = 0;
				break;
			case '-q':
				$verbosity = -1;
				break;
			case '-t':
				$test_run = 1;
				break;
			case '-v':
				$verbosity = 1;
				break;
			case '-vv':
				$verbosity = 2;
				break;
			default:
				_debug("Unknown command line argument:  " . $_SERVER['argv'][$i] . "\n",0);
				break;
		}
	}
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

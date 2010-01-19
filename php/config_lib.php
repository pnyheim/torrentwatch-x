<?php

function setup_default_config() {
  global $config_values;
  function _default($a, $b) {
    global $config_values;
    if(!isset($config_values['Settings'][$a])) {
      $config_values['Settings'][$a] = $b;
    }
  }

  if(!isset($config_values['Settings']))
    $config_values['Settings'] = array();
  // Sensible Defaults 
  $basedir = get_base_dir();
  _default('Transmission Login', '');
  _default('Transmission Password', '');
  _default('Transmission Host', 'localhost');
  _default('Transmission Port', '9091');
  _default('Transmission URI', '/transmission/rpc');
  _default('Watch Dir', '');
  _default('Download Dir', '/mnt/Media/Downloads');
  _default('Cache Dir', $basedir."/rss_cache/");
  _default('Save Torrents', "0");
  _default('Run Torrentwatch', "True");
  _default('Cron', "/etc/cron.hourly");
  _default('Client', "");
  _default('Verify Episode', "1");
  _default('Only Newer', "1");
  _default('Default Feed All', "1");
  _default('Deep Directories', "0");
  _default('Combine Feeds', '0');
  _default('Require Episode Info', '0');
  _default('History', $basedir."/rss_cache/rss_dl.history");
  _default('MatchStyle',"simple");
  _default('FirstRun',"1");
  _default('Extension',"torrent");
  _default('verbosity','0');
  _default('Default Seed Ratio', '-1');
}

if(!(function_exists(get_base_dir))) {
    function get_base_dir() {
        return dirname(dirname(__FILE__));
    }
}
    
function get_curl_defaults(&$curlopt) {
    $curlopt[CURLOPT_CONNECTTIMEOUT] = 10;
    $curlopt[CURLOPT_TIMEOUT] = 20;
    $curlopt[CURLOPT_RETURNTRANSFER] = true;
}

// This function is from
// http://www.codewalkers.com/c/a/Miscellaneous/Configuration-File-Processing-with-PHP/2/
// It has been modified to support multidimensional arrays in the form of
// group[] = key => data as equivilent of group[key] => data


function read_config_file() {
  global $config_values;
  $config_file = platform_getConfigFile();

  $comment = ";";
  $group = "NONE";

  if(!file_exists($config_file)) {
    _debug("No Config File Found\n", 0);
    return FALSE;
  }
  if(!($fp = fopen($config_file, "r"))) {
    _debug("read_config_file: Could not open $config_file\n", 0);
    exit(1);
  }
    
  while (!feof($fp)) {
    $line = trim(fgets($fp));
    if ($line && !ereg("^$comment", $line)) {
      if (ereg("^\[", $line) && ereg("\]$", $line)) {
        $line = trim($line,"[");
        $line = trim($line, "]");
        $group = trim($line);
      } else {
        $pieces = explode("=", $line, 2);
        $pieces[0] = trim($pieces[0] , "\"");
        $pieces[1] = trim($pieces[1] , "\"");
        $option = trim($pieces[0]);
        $value = trim($pieces[1]);
        if(ereg("\[\]$", $option)) {
          $option = substr($option, 0, strlen($option)-2);
          $pieces = explode("=>", $value, 2);
          if(isset($pieces[1])) {
            $config_values[$group][$option][trim($pieces[0])] = trim($pieces[1]);
          } else {
            $config_values[$group][$option][] = $value;
          }
        } else {
          $config_values[$group][$option] = $value;
        }        
      }
    }
  }
  
  fclose($fp);
  // Create the base arrays if not already
     
  if(!isset($config_values['Favorites']))
    $config_values['Favorites'] = array();  
  if(!isset($config_values['Hidden']))
    $config_values['Hidden'] = array();
  if(!isset($config_values['Feeds']))
    $config_values['Feeds'] = array();
  return true;
}

function get_client_passwd() {
    global $config_values;
    return base64_decode(preg_replace('/^\$%&(.*)\$%&$/', '$1', $config_values['Settings']['Transmission Password']));
}

function write_config_file() {
  global $config_values, $config_out;
  $config_file = platform_getConfigFile();

  _debug("Preparing to write config file to $config_file\n");

  if(!(preg_match('/^\$%&(.*)\$%&$/', $config_values['Settings']['Transmission Password']))) {
        if($config_values['Settings']['Transmission Password']) {
            $config_values['Settings']['Transmission Password'] = preg_replace('/^(.*)$/', '\$%&$1\$%&',
             base64_encode($config_values['Settings']['Transmission Password']));
        } else { 
            $config_values['Settings']['Transmission Password'] = "";
        }
  } 

  $config_out = ";;\n;; torrentwatch config file\n;;\n\n";
  if(!function_exists('group_callback')) {
    function group_callback($group, $key) {
      global $config_values, $config_out;
      if($key == 'Global')
        return;
      $config_out .= "[$key]\n";
      array_walk($config_values[$key], 'key_callback');
      $config_out .= "\n\n";
    }
  }

  if(!function_exists('key_callback')) {
    function key_callback($group, $key, $subkey = NULL) {
      global $config_values, $config_out;
      if(is_array($group)) {
        array_walk($group, 'key_callback', $key.'[]');
      } else {
        if($subkey) {
          if(!is_numeric($key)) {  // What does this do?
            $group = "$key => $group";
          }
          $key = $subkey;
        }
        $config_out .= "$key = $group\n";
      }
    }
  }
  array_walk($config_values, 'group_callback');
  $dir = dirname($config_file);
  if(!is_dir($dir)) {
    _debug("Creating configuration directory\n", 1);
    if(file_exists($dir))
      unlink($dir);
    if(!mkdir($dir)) {
      _debug("Unable to create config directory\n", 0);
      return FALSE;
    }
  }
  file_put_contents($config_file, $config_out);
  chmod($config_file, 0600);
  unset($config_out);
}

function update_global_config() {
  global $config_values;
  $input = array('Email Address'      => 'emailAddress',
                 'Transmission Login' => 'truser',
                 'Transmission Password' => 'trpass',
                 'Transmission Host'  => 'trhost',
                 'Transmission Port'  => 'trport',
                 'Transmission URI'   => 'truri',
                 'Download Dir'       => 'downdir',
                 'Watch Dir'          => 'watchdir',
                 'Deep Directories'   => 'deepdir',
                 'Default Seed Ratio' => 'defaultratio',
                 'Combine Feeds'      => 'combinefeeds',
                 'Require Episode Info' => 'require_epi_info',
                 'Client'             => 'client',
                 'MatchStyle'         => 'matchstyle',
                 'Only Newer'         => 'onlynewer',
                 'Default Feed All'   => 'favdefaultall',
                 'Extension'          => 'extension');
                 
  $checkboxs = array('Combine Feeds' => 'combinefeeds',
                     'Require Episode Info' => 'require_epi_info',
                     'Verify Episode' => 'verifyepisodes',
                     'Save Torrents'  => 'savetorrents',
                     'Only Newer'     => 'onlynewer',
                     'Default Feed All' => 'favdefaultall');
                     
  foreach($input as $key => $data)
    if(isset($_GET[$data]))
      $config_values['Settings'][$key] = $_GET[$data];

  foreach($checkboxs as $key => $data) 
    $config_values['Settings'][$key] = isset($_GET[$data]);

  return;
}
      
function update_favorite() {
  global $test_run;
  if(!isset($_GET['button']))
    return;
  switch($_GET['button']) {
    case 'Add':
    case 'Update':
      $response = add_favorite();
      $test_run = TRUE;
      break;
    case 'Delete':
      del_favorite();
      break;
  }
  write_config_file();
  return $response;
}

function update_feed() {
  if($_GET['button'] == "Delete") {
      del_feed();
  } else if($_GET['button'] == "Update") {
      update_feedData();
  } else {
      $link = $_GET['link'];
      add_feed($link);
  }
  write_config_file();
}

function add_hidden($name) {
    global $config_values;
    $guess = guess_match($name);
    if($guess) {
        $name = ucwords(trim(strtr($guess['key'], "._", "  ")));
    
        foreach($config_values['Favorites'] as $fav) {
            if($name == ucwords($fav['Name'])) return("$name exists in favorites. Not adding to hide list.");
        }
          
        if(isset($name)) {
            $config_values['Hidden'][$name] = 'hidden';
        } else {
            return("Bad form data, not added to favorites"); // Bad form data
        }
          
        write_config_file();
    } else {
        return("Unable to add $name to the hide list.");
    }
}

function del_hidden($list) {
  global $config_values;
  foreach($list as $item) {
      if(isset($config_values['Hidden'][$item])) {
        unset($config_values['Hidden'][$item]);
      }
  }
  
  write_config_file(); 
}


function add_favorite() {
  global $config_values;
  
  if(!isset($_GET['idx']) || $_GET['idx'] == 'new' ) {
      foreach($config_values['Favorites'] as $fav) {
          if($_GET['name'] == $fav['Name']) return("\"" . $_GET['name'] . "\" Allready exists in favorites");
      }
  }
  
  if(isset($_GET['idx']) && $_GET['idx'] != 'new') {
    $idx = $_GET['idx'];
  } else if(isset($_GET['name'])) {
    $config_values['Favorites'][]['Name'] = $_GET['name'];
    $idx = end(array_keys($config_values['Favorites']));
    $_GET['idx'] = $idx; // So display_favorite_info() can see it
  } else
    return("Bad form data, not added to favorites"); // Bad form data

  $list = array("name"      => "Name",
                "filter"    => "Filter",
                "not"       => "Not",
                "savein"    => "Save In",
                "episodes"  => "Episodes",
                "feed"      => "Feed",
                "quality"   => "Quality",
                "seedratio" => "seedRatio",
                "season"    => "Season",
                "episode"   => "Episode");
   
  foreach($list as $key => $data) {
    if(isset($_GET[$key]))
      $config_values['Favorites'][$idx][$data] = urldecode($_GET[$key]);
    else
      $config_values['Favorites'][$idx][$data] = "";
  }
}

function del_favorite() {
  global $config_values;
  if(isset($_GET['idx']) AND isset($config_values['Favorites'][$_GET['idx']])) {
    unset($config_values['Favorites'][$_GET['idx']]);
  }
}

function updateFavoriteEpisode(&$fav, $title) {
  global $config_values;
  
  if(!$guess = guess_match($title, TRUE))
    return;
    
  if(!preg_match('/(\d+)x(\d+)/i', $guess['episode'], $regs))
    return;
    
  $curEpisode = preg_replace('/(\d+)x/i', "", $guess['episode']);
  $curSeason = preg_replace('/x(\d+)/i', "", $guess['episode']);
  $expectedEpisode = sprintf('%02d', $fav['Episode'] + 1);
  $emailAddress = $config_values['Settings']['Email Address'];
  if($fav['Episode'] && $curEpisode > $expectedEpisode && $emailAddress) {
      $show = $guess['key'];
      $episode = $guess['episode'];
      $expected = $curSeason . "x" . $expectedEpisode;
      $oldEpisode = $fav['Episode'];
      $oldSeason = $fav['Season'];
      $newEpisode = $curEpisode + 1;
      $newSeason = $curSeason + 1;
      $mail = <<<END
Hi,

This is an automated warning from TorrentWatch-X.

Matched "$show $episode" but expected "$expected".
This usualy means that a double episode is downloaded before this one.
But it could mean that you missed an episode or that "$episode" is a special episode.
If this is the case you need to reset the "Last Downloaded Episode" setting to "$oldSeason x $oldEpisode" in the Favorites menu.
If you don't, the next match wil be "Season: $curSeason Episode: $newEpisode" or "Season $newSeason Episode: 1".

END;

  $subject = "TorrentWatch-X: got $show $episode, expected $expected";
  mail($emailAddress, $subject, $mail, 'From: TorrentWatch-X' );
  }
  if(!isset($fav['Season'],$fav['Episode']) || $regs[1] > $fav['Season']) {
    $fav['Season'] = $regs[1];
    $fav['Episode'] = $regs[2];
  } else if($regs[1] == $fav['Season'] && $regs[2] > $fav['Episode'])
    $fav['Episode'] = $regs[2];
  write_config_file();
} 

function add_feed($link) {
  global $config_values;
  _debug('adding feed: ' . $link);
  
  if(isset($link) AND ($tmp = guess_feedtype($link)) != 'Unknown') {
        _debug('really adding feed');
        $config_values['Feeds'][]['Link'] = $link;
        $idx = end(array_keys($config_values['Feeds']));
        $config_values['Feeds'][$idx]['Type'] = $tmp;
        $config_values['Feeds'][$idx]['seedRatio'] = $config_values['Settings']['Default Seed Ratio'];
        load_feeds(array(0 => array('Type' => $tmp, 'Link' => $link)));
        switch($tmp) {
          case 'RSS':
            $config_values['Feeds'][$idx]['Name'] = $config_values['Global']['Feeds'][$link]['title'];
            break;
          case 'Atom':
            $config_values['Feeds'][$idx]['Name'] = $config_values['Global']['Feeds'][$link]['FEED']['TITLE'];
            break;
        }
      } else {
        _debug("Could not connect to Feed/guess Feed Type", -1);
      }
}

function update_feedData() {
    global $config_values;
    _debug('updating feed: ' . $idx);
    if(isset($_GET['idx']) AND isset($config_values['Feeds'][$_GET['idx']])) {
        if(!($_GET['feed_name']) || !($_GET['feed_link'])) return;
        $config_values['Feeds'][$_GET['idx']]['Name'] = $_GET['feed_name'];
        $config_values['Feeds'][$_GET['idx']]['Link'] = $_GET['feed_link'];
        $config_values['Feeds'][$_GET['idx']]['seedRatio'] = $_GET['seed_ratio'];
    }
}

function del_feed() {
  global $config_values;
  if(isset($_GET['idx']) AND isset($config_values['Feeds'][$_GET['idx']])) {
    unset($config_values['Feeds'][$_GET['idx']]);
  }
}

?>

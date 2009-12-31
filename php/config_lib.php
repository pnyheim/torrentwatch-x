<?php

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
  if(!isset($config_values['Feeds']))
    $config_values['Feeds'] = array();
  return true;
}

  // I wrote the reverse function of the above, please note if you use any
  // command line options those will be written as well
function write_config_file() {
  global $config_values, $config_out;
  $config_file = platform_getConfigFile();

  _debug("Preparing to write config file to $config_file\n");

  if(isset($config_values['Settings']['Transmission Password']) && (!(preg_match('/.*==$/', $config_values['Settings']['Transmission Password'])))) {
    $config_values['Settings']['Transmission Password'] = base64_encode($config_values['Settings']['Transmission Password']);
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
  _debug("Finalized Config\n");
  _debug($config_out,2);
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
  $input = array('Transmission Login' => 'truser',
                 'Transmission Password' => 'trpass',
		         'Transmission Host' => 'trhost',
        		 'Transmission Port' => 'trport',
        		 'Transmission URI' => 'truri',
        		 'Download Dir'     => 'downdir',
                 'Watch Dir'        => 'watchdir',
                 'Deep Directories' => 'deepdir',
                 'Combine Feeds'    => 'combinefeeds',
                 'Client'           => 'client',
                 'MatchStyle'       => 'matchstyle',
                 'Only Newer'       => 'onlynewer',
                 'Extension'        => 'extension');
  $checkboxs = array('Combine Feeds' => 'combinefeeds',
                     'Verify Episode' => 'verifyepisodes',
                     'Save Torrents'  => 'savetorrents',
                     'Only Newer'     => 'onlynewer',);
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
    Case 'Update':
      add_favorite();
      $test_run = TRUE;
      break;
    case 'Delete':
      del_favorite();
      break;
  }
  write_config_file();
}

function update_feed() {
  if($_GET['button'] == "Delete") {
      del_feed();
  } else if($_GET['button'] == "Update") {
      update_feedData();
  } else {
      add_feed();
  }
  write_config_file();
}

function add_favorite() {
  global $config_values;
  if(isset($_GET['idx']) && $_GET['idx'] != 'new') {
    $idx = $_GET['idx'];
  } else if(isset($_GET['name'])) {
    $config_values['Favorites'][]['Name'] = $_GET['name'];
    $idx = end(array_keys($config_values['Favorites']));
    $_GET['idx'] = $idx; // So display_favorite_info() can see it
  } else
    return; // Bad form data

  $list = array("name"      => "Name",
                "filter"    => "Filter",
                "not"       => "Not",
                "savein"    => "Save In",
                "episodes"  => "Episodes",
                "feed"      => "Feed",
                "quality"   => "Quality",
                "seedratio" => "seedRatio");
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
  if(!$guess = guess_match($title, TRUE))
    return;
  if(!preg_match('/(\d+)x(\d+)/i', $guess['episode'], $regs))
    return;
  if(!isset($fav['Season'],$fav['Episode']) || $regs[1] > $fav['Season']) {
    $fav['Season'] = $regs[1];
    $fav['Episode'] = $regs[2];
  } else if($regs[1] == $fav['Season'] && $regs[2] > $fav['Episode'])
    $fav['Episode'] = $regs[2];
  write_config_file();
} 

function add_feed() {
  global $config_values;
  _debug('adding feed');
  if(isset($_GET['link']) AND ($tmp = guess_feedtype($_GET['link'])) != 'Unknown') {
    _debug('really adding feed');
    $link = $_GET['link'];
    $config_values['Feeds'][]['Link'] = $link;
    $idx = end(array_keys($config_values['Feeds']));
    $config_values['Feeds'][$idx]['Type'] = $tmp;
    load_feeds(array(0 => array('Type' => $tmp, 'Link' => $link)));
    switch($tmp) {
      case 'RSS':
        $config_values['Feeds'][$idx]['Name'] = $config_values['Global']['Feeds'][$link]['title'];
        break;
      case 'Atom':
        $config_values['Feeds'][$idx]['Name'] = $config_values['Global']['Feeds'][$link]['Name'];
        break;
    }
  } else
    _debug("Could not connect to Feed/guess Feed Type", -1);
}

function update_feedData() {
    global $config_values;
    _debug('updating feed: ' . $idx);
    if(isset($_GET['idx']) AND isset($config_values['Feeds'][$_GET['idx']])) {
        if(!($_GET['feed_name'])) return;
        $config_values['Feeds'][$_GET['idx']]['Name'] = $_GET['feed_name'];
    }
}

function del_feed() {
  global $config_values;
  if(isset($_GET['idx']) AND isset($config_values['Feeds'][$_GET['idx']])) {
    unset($config_values['Feeds'][$_GET['idx']]);
  }
}

?>

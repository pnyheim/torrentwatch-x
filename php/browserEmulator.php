<?php
/***************************************************************************

Browser Emulating file functions v2.0.1-torrentwatch
(c) Kai Blankenhorn
www.bitfolge.de/browseremulator
kaib@bitfolge.de


This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.

****************************************************************************

Changelog:

v2.0.1-torrentwatch by Erik Bernhardson
  converted file() to file_get_contents()
  converted lastResponse to string from array to mimic file_get_contents
  added gzip compression support

v2.0.1
  fixed authentication bug
  added global debug switch

v2.0   03-09-03
  added a wrapper class; this has the advantage that you no longer need
    to specify a lot of parameters, just call the methods to set
    each option
  added option to use a special port number, may be given by setPort or
    as part of the URL (e.g. server.com:80)
  added getLastResponseHeaders()

v1.5
  added Basic HTTP user authorization
  minor optimizations

v1.0
  initial release



***************************************************************************/

/**
* Hack to enable gzip support without php help(for example, on the NMT)
* This differs from gzinflate() in that it wants the magic numbers that 
* are usually stripped.
**/
function my_gzinflate($string) {
	$tmp = tempnam("", "browserEmulator");
	file_put_contents($tmp, $string);
	$cmd = "cat $tmp | ".platform_getGunzip();	
	exec($cmd, $output, $return);
	return implode($output, "\n");
}

/**
* BrowserEmulator class. Provides methods for opening urls and emulating
* a web browser request.
**/
class BrowserEmulator {
 
  var $headerLines = Array();
  var $postData = Array();
  var $authUser = "";
  var $authPass = "";
  var $port;
  var $lastResponse = '';
  var $debug = false;
 
  function BrowserEmulator() {
    $this->resetHeaderLines();
    $this->resetPort();
  }
    /**
  * Adds a single header field to the HTTP request header. The resulting header
  * line will have the format
  * $name: $value\n
  **/
  function addHeaderLine($name, $value) {
    $this->headerLines[$name] = $value;
  }
 
  /**
  * Deletes all custom header lines. This will not remove the User-Agent header field,
  * which is necessary for correct operation.
  **/
  function resetHeaderLines() {
    $this->headerLines = Array();
   
    /*******************************************************************************/
    /**************   YOU MAX SET THE USER AGENT STRING HERE   *******************/
    /*                                                   */
    /* default is "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)",         */
    /* which means Internet Explorer 6.0 on WinXP                       */
   
    $this->headerLines["User-Agent"] = "UniversalFeedParser/4.01 +http://feedparser.org";

    /*******************************************************************************/
    /**
    * Set default to accept gzip encoded files
    */
    $this->headerLines["Accept-Encoding"] = "*/*";
    if(platform_getGunzip())
      $this->headerLines["Accept-Encoding"] .= ",gzip,deflate";
  }
 
  /**
  * Add a post parameter. Post parameters are sent in the body of an HTTP POST request.
  **/
  function addPostData($name, $value) {
    $this->postData[$name] = $value;
  }
 
  /**
  * Deletes all custom post parameters.
  **/
  function resetPostData() {
    $this->postData = Array();
  }
 
  /**
  * Sets an auth user and password to use for the request.
  * Set both as empty strings to disable authentication.
  **/
  function setAuth($user, $pass) {
    $this->authUser = $user;
    $this->authPass = $pass;
  }
  /**
  * Selects a custom port to use for the request.
  **/
  function setPort($portNumber) {
    $this->port = $portNumber;
  }
 
  /**
  * Resets the port used for request to the HTTP default (80).
  **/
  function resetPort() {
    $this->port = 80;
  }

  /**
   * Parse any cookies set in the URL, and return the trimed string
   **/
  function preparseURL($url) {
    if($cookies = stristr($url, ':COOKIE:')) {
      $url = rtrim(substr($url, 0, -strlen($cookies)), '&');
      $this->addHeaderLine("Cookie", '$Version=1; '.strtr(substr($cookies, 8), '&', ';'));
    }
    return $url;
  }

  /**
  * Make an fopen call to $url with the parameters set by previous member
  * method calls. Send all set headers, post data and user authentication data.
  * Returns a file handle on success, or false on failure.
  **/
  function fopen($url) {
    $url = $this->preparseURL($url);
    $this->lastResponse = Array();
   
/*    preg_match("~([a-z]*://)?([^:^/]*)(:([0-9]{1,5}))?(/.*)?~i", $url, $matches);
   
    $protocol = $matches[1];
    $server = $matches[2];
    $port = $matches[4];
    $path = $matches[5]; */
    $parts = parse_url($url);
    $protocol = $parts['scheme'];
    $server = $parts['host'];
    $port = $parts['port'];
    $path = $parts['path'];
    if(isset($parts['query'])) {
      $path .= '?'.$parts['query'];
    }

    if($protocol == 'https') {
      $server = 'ssl://'.$server;
      $this->setPort(443);
    } elseif ($port!="") {
        $this->setPort($port);
    }
    if ($path=="") $path = "/";
    $socket = false;
    @$socket = fsockopen($server, $this->port, $errno, $errstr, 3);
    if ($socket) {
        $this->headerLines["Host"] = $parts['host'];
       
        if ($this->authUser!="" && $this->authPass!="") {
          $this->headerLines["Authorization"] = "Basic ".base64_encode($this->authUser.":".$this->authPass);
        }
       
        if (count($this->postData)==0) {
          $request = "GET $path HTTP/1.0\r\n";
        } else {
          $request = "POST $path HTTP/1.0\r\n";
        }
       
        if ($this->debug) echo $request;
        fputs ($socket, $request);
	stream_set_timeout($socket, 15);
        if (count($this->postData)>0) {
          $PostStringArray = Array();
          foreach ($this->postData AS $key=>$value) {
            $PostStringArray[] = "$key=$value";
          }
          $PostString = join("&", $PostStringArray);
          $this->headerLines["Content-Length"] = strlen($PostString);
        }
       
        foreach ($this->headerLines AS $key=>$value) {
          if ($this->debug) echo "$key: $value\n";
          fputs($socket, "$key: $value\r\n");
        }
        if ($this->debug) echo "\n";
        fputs($socket, "\r\n");
        if (count($this->postData)>0) {
          if ($this->debug) echo "$PostString";
          fputs($socket, $PostString."\r\n");
        }
    }
    if ($this->debug) echo "\n";
    if ($socket) {
      $line = fgets($socket, 1000);
        if ($this->debug) echo $line;
        $this->lastResponse .= $line;
        $status = substr($line,9,3);
        while (trim($line = fgets($socket, 1000)) != ""){
          if ($this->debug) echo "$line";
          $this->lastResponse .= $line;
          if ($status=="401" AND strpos($line,"WWW-Authenticate: Basic realm=\"")===0) {
            fclose($socket);
            return FALSE;
          }
        }
    }
    return $socket;
  }
  
  /**
  * Make an file call to $url with the parameters set by previous member
  * method calls. Send all set headers, post data and user authentication data.
  * Returns the requested file as a string on success, or false on failure.
  **/
  function file_get_contents($url) {
    if(file_exists($url)) // local file
      return file_get_contents($url);
    $file = '';
    $get = curl_init();
    $getOptions = array(CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_TIMEOUT => 5,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_URL => $url
			);
    curl_setopt_array($get, $getOptions);
    $file = curl_exec($get);
    if(!($file)) {
	_debug('Browser Emulator: file_get_contents; could not get ' . $url, -1);
        return FALSE;
    }
    curl_close($get);

    if(strstr($this->lastResponse, 'Content-Encoding: gzip') !== FALSE) {
      if(function_exists('gzinflate')) {
        $file = gzinflate(substr($file,10));
        if($this->debug) echo "Result file: ".$file;
      } else
        $file = my_gzinflate($file);
    }

//    file_put_contents('/tmp/fsockopen.'.rand(), $file);
    return $file;
  }

  /**
   * Simulate a file() call by exploding file_get_contents()
   **/
  function file($url) {
    $data = $this->file_get_contents($url);
    if($data)
      return explode('\n', $data);
    return False;
  }
 
  function getLastResponseHeaders() {
    return $this->lastResponse;
  }
}


/*
// example code

$be = new BrowserEmulator();

$output = $be->file_get_contents("http://tvbinz.net/rss.php");
$response = $be->getLastResponseHeaders();

echo $output;
*/

?> 

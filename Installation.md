# Dependencies #

  * A web server with php support
  * php5 with json enabled
  * php-curl (highly recommended)
  * php-iconv (highly recommended)
  * Transmission (Tested with 1.7x, 1.8x, 1.9x and 2.xx)
    * Versions reported not to work: 1.34, 1.51
  * Working MTA if you wan't to get warnings and errors by mail.

# Introduction #

  * Copy all files to a directory where you web server can read them.
  * copy $docroot/web/php/config.php.dist to $docroot/web/php/config.php and open it.
  * Configure your web server so it points to $docroot/web/.
  * Create a directory '/etc/torrentwatch' and make sure the web server can write there. Or edit config.php and change the location to your wishes, beware to keep the config file out of the webroot as it holds the torrent clients password.
  * Make sure the web server can write to the following files and directories:
    * $docroot/rss\_cache/
    * $docroot/tvdb\_cache/ (TW-X 0.8 and up)
    * If you will be using the "deep directories" option the default download directory should be writable as well.

  * Create a cron-entry for the user running the web server for periodically checking for new torrents. Every 15 minutes is a good starting point since rss cache is marked old every 15 minutes minus 10 seconds.
> Example:
> > `*/15 * * * * /usr/bin/php-cgi -q /var/www/torrentwatch-x/rss_dl.php -D >/dev/null 2>&1`
  * When using Transmission, please make sure that you have enabled remote management (web server).

You're done! now go to the url corresponding with your webserver/vhost config.
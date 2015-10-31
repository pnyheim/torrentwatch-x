# Announcement! #


## I am going to stop with developing on TW-X due to lack of time. ##
## If anyone is interested to take over this project please mail me. ##

### https://github.com/junalmeida/Sick-Beard and https://github.com/mr-orange/Sick-Beard (Sick Beard with torrent support) are great (and better) alternatives for TW-X. ###


TorrentWatch-X is a web based tool to automate downloads of tv shows via torrents and rss based on the torrentwatch project (now known as NMTDVR). The goal is to make it run on linux, BSD, OSX and NMT (PCH) with different torrent clients.

**Now: Also available in CSI installer!**

Check out the demo at http://torrentwatch.wiebel.nl/ (the configuration and all downloading torrents will be reset every 30 minutes and downloads won't really download for obvious reasons).

## Features ##
  * RSS feeds from a few torrent sites who offer TV shows.
  * Save torrent file in any folder so any torrent client can pick it up.
  * Tell Transmission to save data in any folder.
  * Tell Transmission to stop when a certain ratio is reached.
  * Support for authentication with Transmission.
  * Live download stats for torrents that are currently downloading.
  * List of favorites, for all your favorite TV shows.
  * Add your own RSS feed.

## ChangeLog ##

### 0.8.9 ###
  * Added UserAgent, fixes some issues with some feeds.

### 0.8.8 ###
  * Updated jquery to 1.7.1
  * Fixed issue where ratio limit where not passed correctly to transmission.
  * Fixed issue where rss feeds with magnet links would not be added to transmission.
  * Better hidelist handling.

### 0.8.6 ###
**IMPORTANT: If you are updating from a previous version (non-NMT). After install copy php/config.php to web/php/config.php, then run cleanup.sh to clean files no longer used. If install is done update your webserver config to point to the web/ subdirectory.**
  * Fixed issue that caused the config-cache to corrupt. Causing errors Like: cache\_dir: ""  does not exist / have the wrong rights.
  * Moved web files to 'web' directory. I've included a cleanup.sh script to remove old files after updating. Make sure you backup config.php and move it to 'web/php' (or create a new one).
  * Replaced first run wizzard with default config.
  * Fixed a number of smaller bugs.

### 0.8.4 ###
  * Fixed issue where twx-poller won't run when busybox is not installed on NMT. This caused auto-starting not to work.
  * Fixed issue that caused the config cache not to be cleared when config was changed.
  * Better mobile support.
  * Updated to jquery 1.7
  * Updated to PHPMailer 5.2.0
  * Moved to direct smtp mailing instead of using mail binary
  * Small css updates.

### 0.8.2 ###
  * Moved from cron to a poller script on NMT. This because some NMT apps empty cron on install.
  * Added locking on config so config won't be erased if disk is full.
  * Added configfile caching for performance boost.
  * Fixed compat with transmission 2.4x
  * Better support for android and IOS devices.
  * Fixed issue where hiding a batch of shows would only hide half.
  * Fixed issue where adding a batch of shows as favorites would only add hal

### 0.8.1 ###
  * Fixes issue where install on NMT fails.
  * Updated jquery to 1.6.2

### 0.8.0 ###
  * New look and feel
  * iOS and Android (mobile) support
  * Improved performance
  * Updated guessing routine
  * Fixed a number of smaller and bigger bugs
  * Added "Show Only" option that filters items with no episode info.
  * Disabled ssl checking so https feeds with non-official certs work as well.

### 0.7.1 ###
  * Improved overall performance.
  * Added matching counter on tabs.
  * some other minor UI enhancements.
  * made regexp matching default, simple will be removed in the future.
  * updated guessing routine.
  * Fixed issue where default config on NMT would give errors on devices with a sata drive.
  * Fixed issue that could cause a high load on pch devies and other slower systems. (Thanks Allan for noticing)
  * Fixed issue causing torrents that failed to be added to transmission still got cached. (Thanks Henk for noticing)
  * Fixed a bunch of other small bugs.

### 0.7 ###
  * New fresh look!
  * Added progressbar for active torrents.
  * Added "any" as filter option in favorites. See FAQ for more info.
  * Made Proper/Repack matching optional.
  * Fixed a lot of small UI bugs.
  * Fixed bug where Proper & Repack's where not downloaded.
  * Replaced greybox with own code.
  * Added meaningfull error messages.
  * Added search option in hide list.    -- Upgraded jquery to 1.4.4
  * Update guessing routine.
  * Added option to hide donate button.
  * Fixed issue where Episode filtering stoped working in 0.6.5.
  * Fixed an issue where in some cases Last epi info would not be updated.

### 0.6.5 ###
  * Fixed issue with proper episodes not being saved in Season dir when using Title + Season deep directories.
  * Added mailserver field in wizard.
  * Really fixed php 5.3.x problems.
  * Fixed issue where Favorite feed URLs did not get updated when updating a feed link
  * Fixed issue that caused bug reporting to fail.
  * Fixed issue with feeds that had to links in the feed, where the 2nd link was the torrent which would not be picked up by tw-x. (Thanks Allan Mørk Christensen)
  * Added curl\_getinfo support in curl.php. (Thanks Allan Mørk Christensen)
  * Fixed issue that caused an error while trying to mail while no email address was filled in. Also moved the default email msg to a template file. (Thanks Allan Mørk Christensen)
  * Added negative caching for feeds that are down. This can mean that it takes up to 15 minutes, after thee feed comes back up, for the feed to be working again.
  * Fixed date matching issue where the format "Y-M-D" was not matched correctly.
  * Updated guessing routine for even better episode guessing :)
  * Fixed ui bug that caused hidden feeds the be shown in some cases.
  * Fixed an issue where Deep Directories did not work when using custom directories for favorites.
  * Fixed issue where bug reporting would always give a possitive message even if sending the report failed.
  * Added feature to guess for best link when multiple links are available in the feed. (Thanks Allan Mørk Christensen)
  * Added feature to detect when a link is not a torrent-file, but e.g. (x)html, and then look for a torrent link in that content. (Thanks Allan Mørk Christensen)
  * Better hidelist matching.
  * WebUI will update rss cache if cache is older then 24 hours. The crontab script updates if cache is older then 15 minutes. This ensures the WebUI won't slow down when feeds are down or slow.
  * Added TimeZone option in the Configure menu (Configure -> other).
  * Fixed a lot of PHP notices and warnings
  * Upgraded jquery to 1.4.3

### 0.6.4 ###
  * Fixed issues that caused checking for a download dir even if the transmission host was remote.
  * Added option to hide feeds in UI.
  * Added option so you can choose what smtp server to use for mail.
  * Added option to let tw-x create a directory structure like "**<title**>/**<season**>" (Deep Directories).
  * Redesigned configure window.
  * UI Improvements.
  * Fixed bug that caused font settings not to be remembered.
  * Fixed some minor bugs.
  * Added donation button (Baby on the way :))
### 0.6.3 ###
  * Added support for NMT devices. (See downloads)
  * Added support for TvTorrents.com.
  * Progressbar is now shown when opening dialogs.
  * Updated jquery to version 1.4.2
  * Updated jquery.tinysort to 1.0.4
  * Better guessing routine.
### 0.6.2 ###
  * Added config file option for custom rss cache times. See [FAQ](FAQ.md) for more info.
  * Added TimeZone support in config file. See [FAQ](FAQ.md) for more info.
  * Added writable checks for Transmission SessionID cache file.
  * Fixed bug that caused transmission list to show duplicates.
### 0.6.1 ###
  * Added global speed (up/down) information.
  * Added notification if a feed is down.
  * Improved guessing routine.
  * Fixed some small bug reporting bugs.
  * Fixed sorting bug when feeds were combined.
  * Fixed version-checking bug.
  * Fixed bug that caused tw-x to always use the 'all' filter instead of the one last used.
### 0.6 ###
  * Added bug reporting option.
  * Added configure option to match only shows with episode info.
  * Added Full season recognition.
  * Added script hook (read docs/script.txt for more info).
  * Added version checking.
  * Changed dialog behaviour so that data is pulled on request instead of on page load.
  * Better Episode guessing.
  * Added option to hide items from the list.
  * Added 'TVCap' quality option among others (Thanks job).
  * Fixed bug that causes tw-x to only download the newest epi form a tv-show if more epi's where matched at once.
  * Fixed bug that made it imposible to change the feed of a favorite if 'Set default to All' was set.
  * Fixed bug that caused matching to go wrong in some cases.
  * Fixed bug that made favourites -appear- to revert to 'all feeds' but didn't when after removing 1 ore more feeds. (Thanks Habbie)
  * Fixed download dir bug when items where read from the watch folder.
  * Moved INSTALL, README and LICENSE to docs dir.
### 0.5.2 ###
  * Added confirmation dialog when trashing torrent data.
  * If torrent is started manually and exists in favorites the download dir will be the one set in favorites.
  * Fixed verifying progress message.
  * Fixed focus issue in "move torrent" input.
### 0.5.1 ###
  * Added option to set feed to "All" when adding favorites.
  * Added checking to prevent duplicates in favorites.
  * Added option for editing feed URL's.
  * Upgraded to jQuery 1.4.
  * Fixed bug which caused tw-x not to fallback to the next IP address if a host had multiple IP addresses (round robin or IPv4 + IPv6).
  * Replaced BrowserEmulator with Curl.
  * Better form handling.
  * Fixed Transmission SessionID cache bug.
  * Fixed a number of small bugs.
### 0.5 ###
**IMPORTANT:
  * If you are updating from a previous version. Please copy php/config.php.dist to php/config.php and re-edit it if needed.
  * If you've set a transmision password, please enter it again in the configure menu and press save. The way passwords are stored has changed**
Changes:
    * Added Default ratio option.
    * Added ratio per feed option.
    * Added option to set email address. This will be used for warnings and errors.
    * Added last season and episode field in favorites.
    * Added Download dir and email address to wizard.
    * Added checks for php-json and php-curl.
    * Multiple feeds can now be chosen at once in the wizard.
    * When Tranmission returns an error, this now is shown.
    * Cleaned up config.php some more. Moved function to config\_lib.php.
    * Significant performance improvement.
    * Fixed a bug which caused ratio settings to be set for all torrents instead of the one that was added.
    * Fixed a bug which allowed a show directory to be created if a directory name with differing case already existed.
    * Fixed a sorting bug if rss feed had no `<pubDate>` tag.
    * Fixed transmission hostname and port no longer hardcoded in javascript.
    * Fixed some wizard bugs.
    * Fixed base64 password detection.
    * Fixed Cookie handling. (Read the [FAQ](FAQ.md) for more info.)
    * Disabled magic\_quotes\_gpc.
### 0.4.2 ###
**IMPORTANT: If you are updating from a previous version. Please copy php/config.php.dist to php/config.php and re-edit it if needed.**
      * Added error message when torrent client fails.
      * Added support for combining feeds.
      * Added an option to change the feed name.
### 0.4.1 ###
      * Fixed Transmission button.
### 0.4 ###
      * More intuitive interface, no more context menu.
      * Fixed most IE issues.
      * Transmission support.
        * Pause/Start downloads
        * Remove or Trash downloads
        * Move downloads
        * Full list of transmission downloads
      * Fixed some html & css so it's w3c compatible.
      * Upgraded to jQuery 1.3.2.
      * A whole bunch of bug fixes.
### 0.3 ###
**If you are updating from a previous version. Please copy php/config.php.dist to php/config.php and re-edit it if needed.**
      * Auto refresh every 5 minutes.
      * Disabled atom support for now. If anyone needs support for atom. Sent me a messages and a atom feed to work with :)
      * Added support for CDATA sections within xml rss-feeds.
      * Moved the configfile location option to config.php
      * Live download stats within the torrentlist. Updates every 10 seconds. (Thanks macTijn en Habbie for the javascript help!)
      * Removed refresh button. not very usefull anyway.
### 0.2.1 ###
      * Fix that makes torrenwatch-x download PROPER and REPACK torrents regardless if the specific episode was downloaded before.
      * Added leds for Active downloads and old Downloads (see legend)
### 0.2 ###
      * Changed filter behavior. You can now set a starting point as well. 1x12 as well as s01e12 syntax is allowed.
> > > > Hover over the "Episodes" input field in the favorites screen for more info.
      * Display torrent client in a nice overlay.
      * Changed some CSS settings and layout stuff.
      * Changed feed items to only display the name as a link to the rss feed itself
### 0.1 ###
      * Added support for authentication with transmission
      * Better IE support
      * Renamed (the php file) index.cgi to torrentwatch.php
      * Ported all Transmission connections to php-curl
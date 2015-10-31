  * Q: TorrentWatch-X just fetched an episode but it's shown as "Old Episode". Why?
> A: the cache directory (rss\_cache) is not writeable for the user running the web server. No cache history can be checked so TorrentWatch-X can't know whether it downloaded this file before.

  * Q: How to add feeds which only support cookies?
> A: First you will have to find the cookies containing the uid and password:
    * IE users will find their cookies in `%UserProfile%\Cookies`
    * Firefox users will find their cookies in Tools -> Options -> Privacy -> Show Cookies
    * Opera users will find their cookies in Tools -> Advanced -> Cookies
    * Users of other browsers will have to consult their browser's documentation

> The syntax used for feeds with cookies is:
> > `http://<feed_url>:COOKIE:<cookie1>=<value1>&<cookie2>=<value2>&<cookie3>.....)`

> or:
> > `http://<feed_url>:COOKIE:<cookie1>=<value1>;<cookie2>=<value2>;<cookie3>.....)`

  * Q: I have movies and tv shows combined in my feed(s) and now movies with similar titels as tv shows also are downloaded.

> A: Enable the "Require episode info" in the configure menu. This will cause tw-x to match only items with  episode information (like: S01E12, 1x12, 2010-01-27, etc...).

  * Q: How do I change the default TimeZone.
> A: Add the 'TimeZone' option in your config file, under Settings. See http://nl2.php.net/manual/en/timezones.php for A list of time zones.

> Example:

> ` TimeZone = Antarctica/South_Pole `

  * Q: Can I change the period the rss feed is cached?
> A: Yes you can. Just set the "Cache Time" option in your config file, under Settings, to a value in seconds.

> Example:

> ` Cache Time = 300 `

  * Q: Can I download all torrents from 1 feed, with one favorite?
> A: Yes. Just use "any" as filter in favorites.

  * Q: Can I download all pilots (1x1) from any show?
> A: Yes. In favorites enter "any" as filter and enter "1x1-1x1" in the Episode field.

  * Q: TW-X on my NMT/PCH does not perform that well, is there anything I can do about that?
> A: Yes you can! Just install lighttpd from CSI :)
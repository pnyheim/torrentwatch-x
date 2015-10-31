# Introduction #

A script can be run when a torrent is started.
The script can only be configured in the config file (torrentwatch.config) because of security reasons. This is done in the settings part as "Script = myscript.sh". If 'Script' had no value this option wil be disabled. If an email address is set as well (and you have an mta running) the output of the script will be mailed if it does not exit cleanly.

# Details #

The script gets 3 parameters. The first is one of these:
  * favstart
> > used whenever a favorite torrent is started.
  * nonfavstart
> > used whenever a torrent is started that is not in favorites (via play button or watchdir)
  * error
> > used whenever an error occurs

The second parameter is the torrent filename and the third is an optional error message.

Example script:
```
#!/bin/sh
case $1 in
    favstart)
        curl -f -s -S -u <user>:<pass> --data-urlencode "text=TorrentWatch-X started downloading $2" --data-urlencode "user=< recipient >" http://twitter.com/direct_messages/new.xml
        ;;
    nonfavstart)
        echo "Somebody is downloading $2" | wall
        ;;
    error)
        echo "TorrentWatch-X encountered an error with $2 \n\n $3" | wall  
        ;;
esac
```
chan-archiver
=============

chan-archiver aka 4chan-archiver v2
GPL v3 license etc

Features:
---------

* Archive threads from almost any chan
* Easy to expand with plugins
* Simple login system
* AJAX support for most functions (no need for cron anymore, just leave chan archiver page in an open tab!)

Cons:
-----
* Probably lots of sql exploits
* Spaghetti code

Installation:
-------------

1. Import chanArchiver.sql into a database
2. Edit config.php with your database and archiver info
3. (optional) add a cronjob to run /usr/bin/php -f /path/to/cron.php (your php path might vary)

Sorry there hasn't been any updates to 4chan-archiver in some time. I started rewriting it to support plugins but lost track, been continuing it now and again though.

Eventually I noticed the original github project started having people fork it, and that mixed with chanarchiver.org shutting down prompted me to release this.

Some parts might be messy/unfinished/buggy, this was a bit of a rushed release, haven't really tested this much (works fine on my server with chrome though)

I did intend to write up a small board dumping extension to this but only managed to finish the chanrip.php file, this works for 4chan afaik but it might cause a lot of strain on your server.

Enjoy, and please feel free to improve it however you feel fit :)

(note: if your updating make sure you delete version.txt, also i'm not sure if i enabled the updater in this...)

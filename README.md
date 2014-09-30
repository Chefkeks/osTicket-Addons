osTicket-Addons
===============

Addons
======
- LDAP User Info Addon
- Maybe more in the future...

Info
====
The osTicket LDAP User Info Addon is an external solution, not a plugin. It modifies the osTicket database directly and so you use it on your own risk! So as a first time user, please make a clone of your osTicket installation and try with the clone, instead of your real/live system!
Basically the addon gets LDAP attributes (e.g. physicaldeliveryofficename) and their values (e.g. Room 404) and writes this information into additional fields of the contact information form of an osTicket user.
Additionally it supports ITDB (http://www.sivann.gr/software/itdb/) to get computers assigned to the user, when the label field inside ITDB is formatted like “cn (samaccountname)”. In case you do not use ITDB, don’t worry, you can also use the LDAP User Info Addon without ITDB ;) We just added ITDB support since we use it, but that does not have to mean everybody else is using it.

Note
====
Howdy,
Have fun with this addon, when the configuration is set up once, it’s basically possible to run it every night or so as a cron job (or scheduled task) to update user information ;)
Cheers!

Requirements
============
-	Working osTicket versions:
  - v1.9.2
  - v1.9.3
-	LDAP User Info Addon v0.2 or newer
-	osTicket username(s) must match LDAP samaccountusername(s)
-	LDAP users must have a mail address

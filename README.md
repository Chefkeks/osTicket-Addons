osTicket-Addons
===============

Addons
======
- LDAP User Info Addon
- Maybe more in the future...

Disclaimer
==========
The osTicket LDAP User Info Addon is an external solution, not a plugin. It modifies the osTicket database directly and so you use it on your own risk! So as a first time user, please make a clone of your osTicket installation and try with the clone, instead of your real/live system! Beside that, it's not a part of the official osTicket nor I am an osTicket developer. Use it on your own risk!

Info
====
Basically the addon gets LDAP attributes (e.g. physicaldeliveryofficename) and their values (e.g. Room 404) and writes this information into additional fields of the contact information form of an osTicket user.
Additionally it supports ITDB (http://www.sivann.gr/software/itdb/) to get computers assigned to the user, when the label field inside ITDB is formatted like “cn (samaccountname)”. In case you do not use ITDB, don’t worry, you can also use the LDAP User Info Addon without ITDB ;) We just added ITDB support since we use it, but that does not have to mean everybody else is using it.

Instructions
============
Here are some instructions how to setup the LDAP User Info Addon for osTicket.
- Download and extract the package to your webserver
- (IF necessary) Change file permissions after extraction
- Open smtp.php and configure email settings
- In your browser navigate to the config_ui.php, fill in the database information and other config options and save it. (RECOMMENDATION: Set Debug to ON)
- For every LDAP attribute you'd like to attach to a user, add a new field to the contact information form in osTicket (Admin Panel > Manage > Forms > Contact information)
- On the config_ui.php configure which LDAP attribute value shall be written to which contact information field and save the config
- Let it run by hitting the "Execute update & send logfile" button. You should see the update process on the screen and get an email with the results as well.
- Once successfully configured, you can let the script run as cronjob / scheduled task by calling the cron.php
- Enjoy the LDAP User Info Addon :)

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
  - v1.9.4
  - v1.9.5
  - v1.9.5.1
  - v1.9.6
  - v1.9.7
  - v1.9.8
  - v1.9.8.1
  - v1.9.9
  - v1.9.11
  - v1.9.12
-	LDAP User Info Addon v0.2 or newer
-	osTicket username(s) must match LDAP samaccountusername(s)
-	LDAP users must have a mail address

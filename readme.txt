=== Podlove Beta Tester ===
Contributors: eteubert
Donate link: http://podlove.org/donations/
Tags: podlove, beta, release, test, danger, bee-doo-bee-doo-bee-doo
Requires at least: 3.5
Tested up to: 4.3
Stable tag: trunk
License: MIT

Get beta releases for selected Podlove plugins.

== Description ==

Enable pre-release updates for selected Podlove WordPress plugins.

**WARNING** Installing this plugin equals ripping off the "WARRANTY VOID IF SEAL BROKEN" sticker. It certainly feels good, but you should only do it if you know what you're doing.

This plugin is **not available** through the official WordPress repository because it knowingly violates its rules. It retrieves plugin files from a private server (eric.co.de), which is forbidden. Please be aware of the risks when using this plugin. 

This plugin will offer updates through its own update-mechanism.

== Installation ==

1. Download the latest version: https://github.com/podlove/podlove-beta-tester/archive/master.zip
2. In WordPress, go to Plugins > Add New and choose "Upload Plugin"
3. Upload the zip file you downloaded in step 1.

Configure beta settings in "Settings > Podlove Beta Tester".

Once you have the plugin installed and active, it will receive updates normally. The only difference being that the updates are served by us instead of WordPress. But the user experience is identical.

== Changelog ==

= 2.0.0 =

Use GitHub Releases to manage plugin beta releases.

= 1.1.2 =

* fix certificates

= 1.1.1 =

* add some error handling

= 1.1.0 =

* read config from remote url

= 1.0.2 =

* use custom certificates for *all* requests against eric.co.de

= 1.0.1 =

* ship certificates so update server can be verified

= 1.0 =

Release

# Podlove Beta Tester

Enable pre-release updates for our WordPress plugins.

**WARNING** Installing this plugin equals ripping off the "WARRANTY VOID IF SEAL BROKEN" sticker. It certainly feels good, but you should only do it if you know what you're doing.

## Install & Update

This plugin is not available through the WordPress plugin directory. You need to install it manually.

1. Download the latest version: https://github.com/podlove/podlove-beta-tester/archive/master.zip
2. In WordPress, go to `Plugins > Add New` and choose "Upload Plugin"
3. Upload the zip file you downloaded in step 1.

Once you have the plugin installed and active, it will receive updates normally. The only difference being that the updates are served by us instead of WordPress. But the user experience is identical.

## Maintenance

Available beta branches are defined at `https://eric.co.de/releases/config.json`.

## Directory Structure

```
/        # root: only main plugin file and project files
|- css   # stylesheets
|- inc   # files to include that register hooks or execute code
|- lib   # library (classes)
```

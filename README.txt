=== Plugin Name ===
Contributors: chuckzee (Chuck Zimmerman)
Tags: 
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This was a plugin I developed to support authentication between a native app client and a WordPress website.

The bulk of the work is located here:
https://github.com/chuckzee/gordon-now-app-auth/blob/master/includes/class-gordon-now-app-loader.php

Essentially, it parsed authenticated session cookies that were "shoved" into a native browser session. They were decoded using some config values that are not committed in this repository. 

This did not end up being used in the production website and the SSO project was dropped (Covid fun times!).

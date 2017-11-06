=== CloudFlare ===

Contributors: littlebizzy
Tags: cloudflare, api, cache, cdn, dev mode
Requires at least: 4.4
Tested up to: 4.8
Requires PHP: 7.0
Multisite support: No
Stable tag: 1.0.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Prefix: CLDFLR

Easily connect your WordPress website to CloudFlare's free optimization features, including one-click options to purge cache and enable 'dev' mode.

== Description ==

Easily connect your WordPress website to CloudFlare's free optimization features, including one-click options to purge cache and enable 'dev' mode.

* [Plugin Homepage](https://www.littlebizzy.com/plugins/cloudflare)
* [Plugin GitHub](https://github.com/littlebizzy/cloudflare)
* [SlickStack.io](https://slickstack.io)

#### The Long Version ####

* Correct IP Address information for comments posted to your site
* Better protection as spammers from your WordPress blog get reported to CloudFlare

THINGS YOU NEED TO KNOW:

* The main purpose of this plugin is to ensure you have no change to your originating IPs when using CloudFlare. Since CloudFlare acts a reverse proxy, connecting IPs now come from CloudFlare's range. This plugin will ensure you can continue to see the originating IP. 

* Every time you click the 'spam' button on your blog, this threat information is sent to CloudFlare to ensure you are constantly getting the best site protection.

* We recommend any WordPress and CloudFlare user use this plugin. For more best practices around using WordPress and CloudFlare, see: https://support.cloudflare.com/hc/en-us/articles/201717894-Using-CloudFlare-and-WordPress-Five-Easy-First-Steps

#### Compatibility ####

This plugin has been designed for use on LEMP (Nginx) web servers with PHP 7.0 and MySQL 5.7 to achieve best performance. All of our plugins are meant for single site WordPress installations only; for both performance and security reasons, we highly recommend against using WordPress Multisite for the vast majority of projects.

#### Plugin Features ####

* Settings Page: Yes
* Premium Version Available: Yes ([Purge Them All](https://www.littlebizzy.com/plugins/purge-them-all))
* Includes Media (Images, Icons, Etc): No
* Includes CSS: No
* Database Storage: Yes
  * Transients: No
  * Options: Yes
  * Creates New Tables: No
* Database Queries: Backend Only (Options API Cache)
* Must-Use Support: Yes (Use With [Autoloader](https://github.com/littlebizzy/autoloader))
* Multisite Support: No
* Uninstalls Data: Yes

#### WP Admin Notices ####

This plugin generates multiple [Admin Notices](https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices) in the WP Admin dashboard. The first is a notice that fires during plugin activation which recommends several related free plugins that we believe will enhance this plugin's features; this notice will re-appear approximately once every 5 months as our code and recommendations evolve. The second is a notice that fires a few days after plugin activation which asks for a 5-star rating of this plugin on its WordPress.org profile page. This notice will re-appear approximately once every 8 months. These notices can be dismissed by clicking the **(x)** symbol in the upper right of the notice box. These notices may confuse certain users, but are appreciated by the majority of our userbase, who understand that these notices support our free contributions to the WordPress community. If you feel that these notices are too "annoying" than we encourage you to consider one or more of our upcoming premium plugins that combine several free plugin features into a single control panel. Another alternative would be to develop your own plugins for WordPress, if you feel that supporting free plugin authors is not something that interests you.

#### Code Inspiration ####

This plugin was partially inspired either in "code or concept" by the open-source software and discussions mentioned below:

* [All 404 Redirect to Homepage](https://wordpress.org/plugins/all-404-redirect-to-homepage/)
* [404 Redirection](https://wordpress.org/plugins/404-redirection/)
* [Redirect 404 Error Page to Homepage](https://wordpress.org/plugins/redirect-404-error-page-to-homepage/)

#### Recommended Plugins ####

We invite you to check out a few other related free plugins that our team has also produced that you may find especially useful:

* [Force HTTPS](https://wordpress.org/plugins/force-https-littlebizzy/)
* [Remove Query Strings](https://wordpress.org/plugins/remove-query-strings-littlebizzy/)
* [Remove Category Base](https://wordpress.org/plugins/remove-category-base-littlebizzy/)
* [Server Status](https://wordpress.org/plugins/server-status-littlebizzy/)
* [Disable Embeds](https://wordpress.org/plugins/disable-embeds-littlebizzy/)
* [Disable Emojis](https://wordpress.org/plugins/disable-emojis-littlebizzy/)
* [Disable XML-RPC](https://wordpress.org/plugins/disable-xml-rpc-littlebizzy/)
* [Disable Author Pages](https://wordpress.org/plugins/disable-author-pages-littlebizzy/)
* [Disable Search](https://wordpress.org/plugins/disable-search-littlebizzy/)
* [Virtual Robots.txt](https://wordpress.org/plugins/virtual-robotstxt-littlebizzy/)

#### Special Thanks ####

We thank the following groups for their generous contributions to the WordPress community which have particularly benefited us in developing our own free plugins and paid services:

* [Automattic](https://automattic.com)
* [Delicious Brains](https://deliciousbrains.com)
* [Roots](https://roots.io)
* [rtCamp](https://rtcamp.com)
* [WP Tavern](https://wptavern.com)

#### Disclaimer ####

We released this plugin in response to our managed hosting clients asking for better access to their server, and our primary goal will remain supporting that purpose. Although we are 100% open to fielding requests from the WordPress community, we kindly ask that you keep the above mentioned goals in mind, thanks!

== Installation ==

1. Upload to `/wp-content/plugins/cf-littlebizzy`
2. Activate via WP Admin > Plugins
3. Test the plugin is working by loading a non-existent page URI on your website

== FAQ ==

= Does this plugin alter my 404.php template? =

No, it automatically adds a 404 header using WordPress filters/hooks.

= How can I change this plugin's settings? =

This plugin does not have a settings page and is designed for speed and simplicity.

= I have a suggestion, how can I let you know? =

Please avoid leaving negative reviews in order to get a feature implemented. Instead, we kindly ask that you post your feedback on the wordpress.org support forums by tagging this plugin in your post. If needed, you may also contact our homepage.

== Changelog ==

= 1.3.23 =

Fixed bug that was preventing spam comments from being sent to CloudFlare

= 1.3.22 =

* Fixing bug which prevented a user from activating/deactivating the plugin

= 1.3.21 = 

* Added input sanitization.

= 1.3.20 =

* Updated the method to restore visitor IPs
* Updated the URL rewrite to be compatible with WordPress 4.4

= 1.3.18 =

* Bug: Clean up headers debugging message that can be displayed in some cases

= 1.3.17 =

* Limit http protocol rewriting to text/html Content-Type

= 1.3.16 =

* Update regex to not alter the canonical url

= 1.3.15 =

* Plugin settings are now found under Settings -> CloudFlare
* Plugin is now using the WordPress HTTP_API  - this will give better support to those in hosting environments without cURL or an up to date CA cert bundle
* Fixes to squash some PHP Warnings. Relocated error logging to only happen in WP_DEBUG mode
* Added Protocol Rewriting option to support Flexible SSL

= 1.3.14 =

* Improved logic to detect the customer domain, with added option for a manual override
* Standardised error display
* Updated CloudFlare IP Ranges

= 1.3.13 =

* Clarified error messaging in the plugin further
* Added cURL error detection to explain issues with server installed cert bundles

= 1.3.12 =

* Removed use of php short-code in a couple of places
* Added some cURL / json_decode error handling to output to the screen any failures
* Reformatted error / notice display slightly

= 1.3.11 =

* Adjusted a line syntax to account for differing PHP configurations.

= 1.3.10 = 

* Added IP ranges.

= 1.3.9 =
* Made adjustment to syntax surrounding cURL detection for PHP installations that do not have short_open_tag enabled.

= 1.3.8 =
* Fixed issue with invalid header.
* Updated IP ranges
* Fixed support link

= 1.3.7 =
* Remove Database Optimizer related text.

= 1.3.6 =
* Remove Database Optimizer.

= 1.3.5 =
* Disable Development Mode option if cURL not installed.  Will Use JSONP in future release to allow domains without cURL to use Development Mode.

= 1.3.4 =
* Add in IPV6 support and Development Mode option to wordpress plugin settings page.  Remove cached IP range text file.

= 1.3.3 =
* Bump stable version number.

= 1.3.2.Beta =  
* BETA RELEASE: IPv6 support - Pull the IPv6 range from https://www.cloudflare.com/ips-v6.  Added Development Mode option to wordpress plugin settings page.

= 1.2.4 =  
* Pull the IP range from https://www.cloudflare.com/ips-v4.  Modified to keep all files within cloudflare plugin directory.

= 1.2.3 =  
* Updated with new IP range

= 1.2.2 =
* Restricted database optimization to administrators

= 1.2.1 =
* Increased load priority to avoid conflicts with other plugins

= 1.2.0 =

* WP 3.3 compatibility.

= 1.1.9 =

* Includes latest CloudFlare IP allocation -- 108.162.192.0/18.

= 1.1.8 =

* WP 3.2 compatibility.

= 1.1.7 =

* Implements several security updates.

= 1.1.6 =

* Includes latest CloudFlare IP allocation -- 141.101.64.0/18.

= 1.1.5 =

* Includes latest CloudFlare IP allocation -- 103.22.200.0/22.

= 1.1.4 =

* Updated messaging.

= 1.1.3 =

* Better permission checking for DB optimizer.
* Added CloudFlare's latest /20 to the list of CloudFlare IP ranges.

= 1.1.2 =

* Fixed several broken help links.
* Fixed confusing error message.

= 1.1.1 =

* Fix for Admin menus which are breaking when page variable contains '-'.

= 1.1.0 =

* Added a box to input CloudFlare API credentials.
* Added a call to CloudFlare's report spam API when a comment is marked as spam.

= 1.0.1 =

* Fix to check that it is OK to add a header before adding one.

= 1.0.0 =

* Initial feature set
* Set RemoteIP header correctly.
* On comment spam, send the offending IP to CloudFlare.
* Clean up DB on load.

=== CloudFlare ===

Contributors: littlebizzy
Donate link: https://www.patreon.com/littlebizzy
Tags: cloudflare, api, purge, cdn, dev mode
Requires at least: 4.4
Tested up to: 5.1
Requires PHP: 7.2
Multisite support: No
Stable tag: 1.5.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Prefix: CLDFLR

Easily manage CloudFlare's free features including purge proxy cache (CDN) and enable dev mode, with Dashboard widgets for DNS Records and Analytics.

== Description ==

This plugin was designed to load faster, be more secure, and perform much better than the official CloudFlare plugin. We focus on offering only the key features desired by most developers, while allowing some of the other CloudFlare settings to be better managed at your account over at CloudFlare.com rather than turning WordPress into a bloated control panel with every setting possible which is nearly impossible to keep up with anyways (esp. with stable/secure code).

If you wish to define the CloudFlare API key and API email address on your `wp-config.php` or `functions.php` file you can do that to avoid your clients removing that data, especially during staging site syncing, migrations, etc. Plus it will load the data faster via PHP Opcache and server/Linux RAM memory caching rather than needing database queries or Options API cache.

You can also use those defined constants to better automate server/WordPress setup using bash scripts etc.

* one-click "dev" mode
* one-click purge CloudFlare cache
* automatic CloudFlare domain detection
* real visitor IP address sent to server/WordPress
* much lighter/faster code than "official" CloudFlare plugin
* uses CloudFlare API version 4.0+
* no integration with Akismet
* see all DNS records "live" in WP Admin Dashboard widget (click "update" button anytime)

== Installation ==

1. Upload to `/wp-content/plugins/cf-littlebizzy`
2. Activate via WP Admin > Plugins
3. COnfigure at `/wp-admin/options-general.php?page=cloudflare`

== FAQ ==

= How can I change this plugin's settings? =

Configure at `/wp-admin/options-general.php?page=cloudflare`

= What version of the CloudFlare API is used? =

It currently uses CloudFlare API version 4.

= Does this plugin purge `cache everything` pages? =

No, it does not support caching HTML with CloudFlare, it is only meant to purge static resources. The "cache everything" option is generally not recommended for the vast majority of dynamic websites as it causes conflicts.

= Can I define the API key and email address? =

Yes you can use the supported defined constants to input the API key and email address in static PHP files like `wp-config.php` or `functions.php` to avoid database queries and data loss during staging sync or migrations.

= I have a suggestion, how can I let you know? =

Please avoid leaving negative reviews in order to get a feature implemented. Instead, join our free Facebook group.

== Changelog ==

= 1.5.0 =
* added DNS Records widget to Dashboard
* added support for `CLOUDFLARE_WIDGET_DNS`
* The dashboard widget automatically performs an AJAX request if there has never been updated.
* If no records found, a link to the configuration page is also shown to check the cloudflare settings.
* After the AJAX request, the update link disappears but it will reappear after 60 seconds via a javascript timeout instead of a server side check. I think it is a simple protection that can be skipped reloading again the dashboard, but avoids repeated requests, which is after all what we are looking for.
* Independently of the dashboard AJAX requests, the DNS records are updated via cron each 30 minutes since the plugin activation/upgrade.
* The widget displays 3 columns, but the third column (dns record content) slides to the next row if there are more than 35 characters.
* The whole updating process uses the key and email saved from the settings page, but a change of these settings does not update the dns records until the next cron/ajax update.
* For reasons on WP architecture, the action cron will be executed even if the widget is disabled by custom constant, but ultimately will not make the API request if the constant has value false.

= 1.4.0 =
* tested with WP 5.0
* updated plugin meta

= 1.3.2 =
* remove Admin Toolbar link hover titles (tooltips)

= 1.3.1 =
* make drop-down menu appear on frontend too (not just backend)
* drop-down menu parent now links to `/wp-admin/options-general.php?page=cloudflare`

= 1.3.0 =
* added drop-down menu on Admin Toolbar
* enabled a dual mode in the plugin page, supporting both GET requests and POST submits to perform the clear cache and enable dev mode actions (GET requests are too secured by WP nonce system)

= 1.2.1 =
* updated plugin meta

= 1.2.0 =
* tested with PHP 7.0
* tested with PHP 7.1
* tested with PHP 7.2
* better settings data management for mixed scenarios (form values + defined constants)
* (defined constants will now never "save" to the database)
* `UPDATE SETTINGS` button now hidden if defined constants are recognized
* API domain check now performed for `dev mode` and `purge cache` options (not only for `Update Settings` button)
* (all button/actions are now "real" API results rather than relying on stored data values)
* API notices now include specific API errors from CloudFlare

= 1.1.1 =
* added warning for Multisite installations
* updated recommended plugins

= 1.1.0 =
* versioning correction (new major features in 1.0.4)
* (no code changes)

= 1.0.4 =
* added support for `CLOUDFLARE_API_KEY`
* added support for `CLOUDFLARE_API_EMAIL`

= 1.0.3 =
* minor code optimization

= 1.0.2 =
* fix: `Class 'LittleBizzy\CloudFlare\CLDFLR_Admin_Notices' not found`

= 1.0.1 =
* tested with WP 4.9
* added support for `DISABLE_NAG_NOTICES`
* added recommended plugins notice
* added WP.org rating request notice

= 1.0.0 =
* initial release
* forked (kinda) from plugin *CloudFlare v1.3.24*
* plugin uses PHP namespaces
* object-oriented code

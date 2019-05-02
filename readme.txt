=== CloudFlare ===

Contributors: littlebizzy
Donate link: https://www.patreon.com/littlebizzy
Tags: cloudflare, api, purge, cdn, dev mode
Requires at least: 4.4
Tested up to: 5.0
Requires PHP: 7.2
Multisite support: No
Stable tag: 1.5.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Prefix: CLDFLR

Easily connect your WordPress website to free optimization features from CloudFlare, including one-click options to purge cache and enable dev mode.

== Description ==

Easily connect your WordPress website to free optimization features from CloudFlare, including one-click options to purge cache and enable dev mode.

* [**Join our FREE Facebook group for support**](https://www.facebook.com/groups/littlebizzy/)
* [**Worth a 5-star review? Thank you!**](https://wordpress.org/support/plugin/cf-littlebizzy/reviews/?rate=5#new-post)
* [Plugin Homepage](https://www.littlebizzy.com/plugins/cloudflare)
* [Plugin GitHub](https://github.com/littlebizzy/cloudflare)

#### Current Features ####

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

#### Compatibility ####

This plugin has been designed for use on [SlickStack](https://slickstack.io) web servers with PHP 7.2 and MySQL 5.7 to achieve best performance. All of our plugins are meant for single site WordPress installations only; for both performance and usability reasons, we highly recommend avoiding WordPress Multisite for the vast majority of projects.

Any of our WordPress plugins may also be loaded as "Must-Use" plugins by using our free [Autoloader](https://github.com/littlebizzy/autoloader) script in the `mu-plugins` directory.

#### Defined Constants ####

    /* Plugin Meta */
    define('DISABLE_NAG_NOTICES', true);

    /* CloudFlare Functions */
    define('CLOUDFLARE_API_KEY', '123456789');
    define('CLOUDFLARE_API_EMAIL', 'user@example.com');

#### Technical Details ####

* Prefix: CLDFLR
* Parent Plugin: N/A
* Disable Nag Notices: [Yes](https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices#Disable_Nag_Notices)
* Settings Page: No
* PHP Namespaces: Yes
* Object-Oriented Code: Yes
* Includes Media (images, icons, etc): No
* Includes CSS: No
* Database Storage: Yes
  * Transients: No
  * WP Options Table: Yes
  * Other Tables: No
  * Creates New Tables: No
  * Creates New WP Cron Jobs: No
* Database Queries: Backend Only (Options API)
* Must-Use Support: [Yes](https://github.com/littlebizzy/autoloader)
* Multisite Support: No
* Uninstalls Data: Yes

#### Special Thanks ####

[Alex Georgiou](https://www.alexgeorgiou.gr), [Automattic](https://automattic.com), [Brad Touesnard](https://bradt.ca), [Daniel Auener](http://www.danielauener.com), [Delicious Brains](https://deliciousbrains.com), [Greg Rickaby](https://gregrickaby.com), [Matt Mullenweg](https://ma.tt), [Mika Epstein](https://halfelf.org), [Mike Garrett](https://mikengarrett.com), [Samuel Wood](http://ottopress.com), [Scott Reilly](http://coffee2code.com), [Jan Dembowski](https://profiles.wordpress.org/jdembowski), [Jeff Starr](https://perishablepress.com), [Jeff Chandler](https://jeffc.me), [Jeff Matson](https://jeffmatson.net), [Jeremy Wagner](https://jeremywagner.me), [John James Jacoby](https://jjj.blog), [Leland Fiegel](https://leland.me), [Luke Cavanagh](https://github.com/lukecav), [Mike Jolley](https://mikejolley.com), [Pau Iglesias](https://pauiglesias.com), [Paul Irish](https://www.paulirish.com), [Rahul Bansal](https://profiles.wordpress.org/rahul286), [Roots](https://roots.io), [rtCamp](https://rtcamp.com), [Ryan Hellyer](https://geek.hellyer.kiwi), [WP Chat](https://wpchat.com), [WP Tavern](https://wptavern.com)

#### Disclaimer ####

We released this plugin in response to our managed hosting clients asking for better access to their server, and our primary goal will remain supporting that purpose. Although we are 100% open to fielding requests from the WordPress community, we kindly ask that you keep these conditions in mind, and refrain from slandering, threatening, or harassing our team members in order to get a feature added, or to otherwise get "free" support. The only place you should be contacting us is in our free [**Facebook group**](https://www.facebook.com/groups/littlebizzy/) which has been setup for this purpose, or via GitHub if you are an experienced developer. Thank you!

#### Our Philosophy ####

> "Decisions, not options." -- WordPress.org

> "Everything should be made as simple as possible, but not simpler." -- Albert Einstein, et al

> "Write programs that do one thing and do it well... write programs to work together." -- Doug McIlroy

> "The innovation that this industry talks about so much is bullshit. Anybody can innovate... 99% of it is 'Get the work done.' The real work is in the details." -- Linus Torvalds

#### Search Keywords ####

cf, cloudflare, cloudflare api, cloudflare cache, cloudflare cdn, cloudflare purge

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

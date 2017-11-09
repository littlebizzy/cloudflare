<?php
/*
Plugin Name: CloudFlare
Plugin URI: https://www.littlebizzy.com/plugins/cloudflare
Description: Easily connect your WordPress website to CloudFlare's free optimization features, including one-click options to purge cache and enable 'dev' mode.
Version: 1.0.0
Author: LittleBizzy
Author URI: https://www.littlebizzy.com
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Prefix: CLDFLR
*/



// Initialization
// ---------------------------------------------------------------------------------------------------



// Avoid direct plugin calls
if (!function_exists('add_action'))
	die;



/**
 * Plugin constants
 */
define('CLOUDFLARE_VERSION',  '1.3.24');
define('CLOUDFLARE_API_URL',  'https://www.cloudflare.com/api_json.html');
define('CLOUDFLARE_SPAM_URL', 'https://www.cloudflare.com/ajax/external-event.html');



/**
 * Dependencies
 */
require_once("IpRewrite.php");
require_once("IpRange.php");
use CloudFlare\IpRewrite;



/**
 * CloudFlare IP detection
 */
add_action('init', 'cloudflare_init', 1);
function cloudflare_init() {
    global $is_cf;
    $is_cf = IpRewrite::isCloudFlare();
    add_action('admin_menu', 'cloudflare_config_page');
}



// Admin section
// ---------------------------------------------------------------------------------------------------



/**
 * Admin page
 */
function cloudflare_config_page() {
    add_options_page(__('CloudFlare Configuration'), __('CloudFlare'), 'manage_options', 'cloudflare', 'cloudflare_config_page_load');
}



/**
 * Basic user validation and loads plugin settings page
 */
function cloudflare_config_page_load() {
    if ( function_exists('current_user_can') && !current_user_can('manage_options') )
        die(__('Cheatin&#8217; uh?'));
	require_once dirname(__FILE__).'/cloudflare-conf.php';
	cloudflare_conf();
}



/**
 * Link to the plugin settings from the plugins page
 */
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'cloudflare_plugin_action_links' );
function cloudflare_plugin_action_links( $links ) {
    $links[] = '<a href="'. get_admin_url(null, 'options-general.php?page=cloudflare') .'">Settings</a>';
    return $links;
}



// Comments actions
// ---------------------------------------------------------------------------------------------------



/**
 * Spam comments notification
 */
add_action('wp_set_comment_status', 'cloudflare_check_comment_status', 1, 2);
function cloudflare_check_comment_status($id, $status) {
	if ($status == 'spam' && null !== ($comment = get_comment($id))) {
		require_once dirname(__FILE__).'/cloudflare-api.php';
		cloudflare_set_comment_status($comment);
	}
}



// Buffer functions and URL replacement
// ---------------------------------------------------------------------------------------------------



/**
 * load just the single option, defaulting to on
 */
add_action('plugins_loaded', 'cloudflare_buffer_init');
function cloudflare_buffer_init() {
    if (1 == load_protocol_rewrite())
        ob_start('cloudflare_buffer_wrapup');
}



/**
 * Replaces URL in the current buffer
 */
function cloudflare_buffer_wrapup($buffer) {
    // Check for a Content-Type header. Currently only apply rewriting to "text/html" or undefined
    $headers = headers_list();
    $content_type = null;

    foreach ($headers as $header) {
        if (strpos(strtolower($header), 'content-type:') === 0) {
            $pieces = explode(':', strtolower($header));
            $content_type = trim($pieces[1]);
            break;
        }
    }

    if (is_null($content_type) || substr($content_type, 0, 9) === 'text/html') {
        // replace href or src attributes within script, link, base, and img tags with just "//" for protocol
        $re     = "/(<(script|link|base|img|form)([^>]*)(href|src|action)=[\"'])https?:\\/\\//i";
        $subst  = "$1//";
        $return = preg_replace($re, $subst, $buffer);

        // on regex error, skip overwriting buffer
        if ($return) {
            $buffer = $return;
        }
    }

    return $buffer;
}



/**
 * wordpress 4.4 srcset ssl fix
 * Shoutout to @bhubbard: https://wordpress.org/support/topic/44-https-rewritte-aint-working-with-images?replies=12
 */
add_filter('wp_calculate_image_srcset', 'cloudflare_ssl_srcset');
function cloudflare_ssl_srcset($sources) {
    if (1 == load_protocol_rewrite()) {
        foreach ( $sources as &$source ) {
            $re     = "/https?:\\/\\//i";
            $subst  = "//";
            $return = preg_replace($re, $subst, $source['url']);

            if ($return) {
                $source['url'] = $return;
            }
        }
        return $sources;
    }
    return $sources;
}



// Common functions
// ---------------------------------------------------------------------------------------------------



/**
 * Retrieve saved CF keys
 */
function load_cloudflare_keys() {
    global $cloudflare_api_key, $cloudflare_api_email, $cloudflare_zone_name, $cloudflare_protocol_rewrite;
    $cloudflare_api_key = get_option('cloudflare_api_key');
    $cloudflare_api_email = get_option('cloudflare_api_email');
    $cloudflare_zone_name = get_option('cloudflare_zone_name');
    $cloudflare_protocol_rewrite = load_protocol_rewrite();
}



/**
 * Retrieve protocol rewrite option value
 */
function load_protocol_rewrite() {
    return get_option('cloudflare_protocol_rewrite', 1);
}
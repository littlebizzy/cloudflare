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

/*
Plugin adapted from the Akismet WP plugin.
*/

define('CLOUDFLARE_VERSION', '1.3.24');
define('CLOUDFLARE_API_URL', 'https://www.cloudflare.com/api_json.html');
define('CLOUDFLARE_SPAM_URL', 'https://www.cloudflare.com/ajax/external-event.html');

require_once("IpRewrite.php");
require_once("IpRange.php");

use CloudFlare\IpRewrite;


// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}



function cloudflare_init() {
    global $is_cf;
    $is_cf = IpRewrite::isCloudFlare();
    add_action('admin_menu', 'cloudflare_config_page');
}
add_action('init', 'cloudflare_init',1);



function cloudflare_config_page() {
    add_options_page(__('CloudFlare Configuration'), __('CloudFlare'), 'manage_options', 'cloudflare', 'cloudflare_config_page_load');
}

function cloudflare_config_page_load() {
    if ( function_exists('current_user_can') && !current_user_can('manage_options') )
        die(__('Cheatin&#8217; uh?'));
	require_once dirname(__FILE__).'/cloudflare-conf.php';
	cloudflare_conf();
}



function cloudflare_plugin_action_links( $links ) {
    $links[] = '<a href="'. get_admin_url(null, 'options-general.php?page=cloudflare') .'">Settings</a>';
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'cloudflare_plugin_action_links' );




function load_cloudflare_keys () {
    global $cloudflare_api_key, $cloudflare_api_email, $cloudflare_zone_name, $cloudflare_protocol_rewrite;
    $cloudflare_api_key = get_option('cloudflare_api_key');
    $cloudflare_api_email = get_option('cloudflare_api_email');
    $cloudflare_zone_name = get_option('cloudflare_zone_name');
    $cloudflare_protocol_rewrite = load_protocol_rewrite();
}

function load_protocol_rewrite() {
    return get_option('cloudflare_protocol_rewrite', 1);
}



// Now actually allow CF to see when a comment is approved/not-approved.
function cloudflare_set_comment_status($id, $status) {

    if ($status != 'spam')
		return;

	// Globals
    global $cloudflare_api_key, $cloudflare_api_email;

	// Check keys
    load_cloudflare_keys();
    if (!$cloudflare_api_key || !$cloudflare_api_email)
        return;

    // make sure we have a comment
	$comment = get_comment($id);
    if (is_null($comment))
		return;

    $payload = array(
        "a"     => $comment->comment_author,
        "am"    => $comment->comment_author_email,
        "ip"    => $comment->comment_author_IP,
        "con"   => substr($comment->comment_content, 0, 100)
    );

    $payload = urlencode(json_encode($payload));

    $args = array(
        'method'        => 'GET',
        'timeout'       => 20,
        'sslverify'     => true,
        'user-agent'    => 'CloudFlare/WordPress/'.CLOUDFLARE_VERSION,
    );

    $url = sprintf('%s?evnt_v=%s&u=%s&tkn=%s&evnt_t=%s', CLOUDFLARE_SPAM_URL, $payload, $cloudflare_api_email, $cloudflare_api_key, 'WP_SPAM');

    // fire and forget here, for better or worse
    wp_remote_get($url, $args);
    // ajax/external-event.html?email=ian@cloudflare.com&t=94606855d7e42adf3b9e2fd004c7660b941b8e55aa42d&evnt_v={%22dd%22:%22d%22}&evnt_t=WP_SPAM
}
add_action('wp_set_comment_status', 'cloudflare_set_comment_status', 1, 2);



function get_dev_mode_status($token, $email, $zone) {

    $fields = array(
        'a'=>"zone_load",
        'tkn'=>$token,
        'email'=>$email,
        'z'=>$zone
    );

    $result = cloudflare_curl(CLOUDFLARE_API_URL, $fields, true);

    if (is_wp_error($result)) {
        trigger_error($result->get_error_message(), E_USER_WARNING);
        return $result;
    }

    if ($result->response->zone->obj->zone_status_class == "status-dev-mode") {
        return "on";
    }

    return "off";
}

function set_dev_mode($token, $email, $zone, $value) {

    $fields = array(
        'a'=>"devmode",
        'tkn'=>$token,
        'email'=>$email,
        'z'=>$zone,
        'v'=>$value
    );

    $result = cloudflare_curl(CLOUDFLARE_API_URL, $fields, true);

    if (is_wp_error($result)) {
        trigger_error($result->get_error_message(), E_USER_WARNING);
        return $result;
    }

    return $result;
}

function get_domain($token, $email, $raw_domain) {

    $fields = array(
        'a'=>"zone_load_multi",
        'tkn'=>$token,
        'email'=>$email
    );

    $result = cloudflare_curl(CLOUDFLARE_API_URL, $fields, true);

    if (is_wp_error($result)) {
        trigger_error($result->get_error_message(), E_USER_WARNING);
        return $result;
    }

    $zone_count = $result->response->zones->count;
    $zone_names = array();

    if ($zone_count < 1) {
        return new WP_Error('match_domain', 'API did not return any domains');
    }
    else {
        for ($i = 0; $i < $zone_count; $i++) {
            $zone_names[] = $result->response->zones->objs[$i]->zone_name;
        }

        $match = match_domain_to_zone($raw_domain, $zone_names);

        if (is_null($match)) {
            return new WP_Error('match_domain', 'Unable to automatically find your domain (no match)');
        }
        else {
            return $match;
        }
    }
}

/**
 * @param $domain        string      the domain portion of the WP URL
 * @param $zone_names    array       an array of zone_names to compare against
 *
 * @returns null|string null in the case of a failure, string in the case of a match
 */
function match_domain_to_zone($domain, $zones) {

    $splitDomain = explode('.', $domain);
    $totalParts = count($splitDomain);

    // minimum parts for a complete zone match will be 2, e.g. blah.com
    for ($i = 0; $i <= ($totalParts - 2); $i++) {
        $copy = $splitDomain;
        $currentDomain = implode('.', array_splice($copy, $i));
        foreach ($zones as $zone_name) {
            if (strtolower($currentDomain) == strtolower($zone_name)) {
                return $zone_name;
            }
        }
    }

    return null;
}

/**
 * @param $url       string      the URL to curl
 * @param $fields    array       an associative array of arguments for POSTing
 * @param $json      boolean     attempt to decode response as JSON
 *
 * @returns WP_ERROR|string|object in the case of an error, otherwise a $result string or JSON object
 */
function cloudflare_curl($url, $fields = array(), $json = true) {

    $args = array(
        'method'        => 'GET',
        'timeout'       => 20,
        'sslverify'     => true,
        'user-agent'    => 'CloudFlare/WordPress/'.CLOUDFLARE_VERSION,
    );

    if (!empty($fields)) {
        $args['method'] = 'POST';
        $args['body']   = $fields;
    }

    $response = wp_remote_request($url, $args);

    // if we have an array, we have a HTTP Response
    if (is_array($response)) {
        // Always expect a HTTP 200 from the API

        // HERE BE DRAGONS
        // WP_HTTP does not return conistent types - cURL seems to return an int for the reponse code, streams returns a string.
        if (intval($response['response']['code']) !== 200) {
            // Invalid response code
            return new WP_Error('cloudflare', sprintf('CloudFlare API returned a HTTP Error: %s - %s', $response['response']['code'], $response['response']['message']));
        }
        else {
            if ($json == true) {
                $result = json_decode($response['body']);
                // not a perfect test, but better than nothing perhaps
                if ($result == null) {
                    return new WP_Error('json_decode', sprintf('Unable to decode JSON response'), $result);
                }

                // check for the CloudFlare API failure response
                if (property_exists($result, 'result') && $result->result !== 'success') {
                    $msg = 'Unknown Error';
                    if (property_exists($result, 'msg') && !empty($result->msg)) $msg = $result->msg;
                    return new WP_Error('cloudflare', $msg);
                }

                return $result;
            }
            else {
                return $response['body'];
            }
        }
    }
    else if (is_wp_error($response)) {
        return $response;
    }

    // Should never happen!
    return new WP_Error('unknown_wp_http_error', sprintf('Unknown response from wp_remote_request - unable to contact CloudFlare API'));
}



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
 * load just the single option, defaulting to on
 */
function cloudflare_buffer_init() {
    if (1 == load_protocol_rewrite())
        ob_start('cloudflare_buffer_wrapup');
}
add_action('plugins_loaded', 'cloudflare_buffer_init');



/**
 * wordpress 4.4 srcset ssl fix
 * Shoutout to @bhubbard: https://wordpress.org/support/topic/44-https-rewritte-aint-working-with-images?replies=12
 */
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
add_filter( 'wp_calculate_image_srcset', 'cloudflare_ssl_srcset' );

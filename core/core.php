<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Core;

/**
 * Core class
 *
 * @package CloudFlare
 * @subpackage Core
 */
final class Core {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Single class instance
	 */
	private static $instance;



	/**
	 * Detection flag
	 */
	private $isCF;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Create or retrieve instance
	 */
	public static function instance() {

		// Check instance
		if (!isset(self::$instance))
			self::$instance = new self;

		// Done
		return self::$instance;
	}



	/**
	 * Constructor
	 */
	private function __construct() {

		// Init hook
		add_action('init', array(&$this, 'init'));

		// Admin mode
		if (is_admin()) {

			// AJAX mode
			if (defined('DOING_AJAX') && DOING_AJAX) {
				// Reserved for future implentations

			// Admin
			} else {

				// Initialize object
				\LittleBizzy\CloudFlare\Admin\Admin::instance();
			}

		// Front
		} else {
			// Reserved for future implentations
		}
	}



	// WP Hooks
	// ---------------------------------------------------------------------------------------------------



	/**
	 * IP checking
	 */
	public function init() {
		$this->isCF = LittleBizzy\CloudFlare\Libraries\IpRewrite::isCloudFlare();
	}



	// Internal
	// ---------------------------------------------------------------------------------------------------

}







/**
 * Plugin constants
 */
define('CLOUDFLARE_VERSION',  '1.3.24');
define('CLOUDFLARE_API_URL',  'https://www.cloudflare.com/api_json.html');
define('CLOUDFLARE_SPAM_URL', 'https://www.cloudflare.com/ajax/external-event.html');









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
}



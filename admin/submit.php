<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Admin;

// Aliased namespaces
use \LittleBizzy\CloudFlare\Core;
use \LittleBizzy\CloudFlare\API;

/**
 * Submit class
 *
 * @package CloudFlare
 * @subpackage Admin
 */
final class Submit {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Single class instance
	 */
	private static $instance;



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
	private function __construct() {}



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * API credentials
	 */
	public function credentials(&$args) {

		// Check nonce
		if (empty($_POST['hd-credentials-nonce']) || !wp_verify_nonce($_POST['hd-credentials-nonce'], 'cloudflare_credentials')) {
			$args['notices']['error'][] = 'Invalid form security code, please try again.';
			return;
		}

		// Check key
		$key = isset($_POST['tx-credentials-key'])? trim($_POST['tx-credentials-key']) : false;
		if (empty($key)) {
			$args['notices']['error'][] = 'Missing API Key value';

		// New key value
		} elseif ($key != Core\Data::instance()->key) {
			Core\Data::instance()->save(['key' => $key]);
		}

		// Check email
		$email = isset($_POST['tx-credentials-email'])? trim($_POST['tx-credentials-email']) : false;
		if (empty($email)) {
			$args['notices']['error'][] = 'Missing CloudFlare API email';

		// Validate
		} elseif (!is_email($email)) {
			$email = null;
			$args['notices']['error'][] = 'The email <strong>'.esc_html($email).'<strong> is not valid';

		// Check if is a new email
		} elseif ($email != Core\Data::instance()->email) {
			Core\Data::instance()->save(['email' => $email]);
		}

		// Check values for API validation
		if (!empty($key) && !empty($email)) {

			// Perform the API calls
			$result = $this->checkDomain($args, $key, $email);

			// Check error
			if (is_wp_error($result)) {
				$zone = false;
				$args['notices']['error'][] = 'CloudFlare API request error';

			// Continue
			} else {
				$zone = $result;
				$args['notices']['success'][] = 'Updated domain status via CloudFlare API';
			}

			// Update data
			Core\Data::instance()->save['zone' => $zone];
		}
	}



	// Internal
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Check domain calling the API
	 */
	private function checkDomain($key, $email) {

		// Initialize
		$page = $maxPages = 1;

		// Enum page
		while ($page <= $maxPages) {

			// Perform the API call
			$response = API\CloudFlare::instance($key, $email)->getZones($page);
			if (is_wp_error($result))
				return $result;

			// Check domains
			if (false !== ($zone = $this->matchDomains($response['result'])))
				return @json_encode($zone);

			// Max pages check
			if (1 == $page)
				$maxPages = empty($response['result_info']['total_pages'])? 0 : (int) $response['result_info']['total_pages'];

			// Next page
			$page++;
		}

		// Done
		return false;
	}



	private function matchZones($result) {
		if (empty($result) || !is_array($result))
		$domain = Core\Data::instance()->domain;
	}



}
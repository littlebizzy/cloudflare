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


		/* Key */

		// Check key
		$key = isset($_POST['tx-credentials-key'])? trim($_POST['tx-credentials-key']) : false;
		if (empty($key)) {
			$args['notices']['error'][] = 'Missing Cloudflare API Key value';

		// New key value
		} elseif ($key != Core\Data::instance()->key) {
			Core\Data::instance()->save(['key' => $key]);
		}


		/* email */

		// Check email
		$email = isset($_POST['tx-credentials-email'])? trim($_POST['tx-credentials-email']) : false;
		if (empty($email)) {
			$args['notices']['error'][] = 'Missing Cloudflare API email';

		// Validate
		} elseif (!is_email($email)) {
			$email = null;
			$args['notices']['error'][] = 'The email <strong>'.esc_html($email).'<strong> is not valid';

		// Check if is a new email
		} elseif ($email != Core\Data::instance()->email) {
			Core\Data::instance()->save(['email' => $email]);
		}


		/* API request */

		// Check values for API validation
		if (!empty($key) && !empty($email)) {

			// Initialize
			$zone = false;

			// Perform the API calls
			$result = $this->checkDomain($key, $email);
			if (is_wp_error($result)) {
				$args['notices']['error'][] = 'CloudFlare API request error';

			// Missing domain
			} elseif (false === $result) {
				$args['notices']['error'][] = 'Current domain does not match the CloudFlare API zones';

			// Found
			} else {
				$zone = Core\Data::instance()->sanitizeZone($result);
				$args['notices']['success'][] = 'Updated domain info via CloudFlare API';
			}

			// Update data
			Core\Data::instance()->save(['zone' => $zone]);
		}
	}



	/**
	 * Change Development Mode status
	 */
	public function devMode(&$args) {

		// Check nonce
		if (empty($_POST['hd-devmode-nonce']) || !wp_verify_nonce($_POST['hd-devmode-nonce'], 'cloudflare_devmode')) {
			$args['notices']['error'][] = 'Invalid form security code, please try again.';
			return;
		}

		// Check API data
		$data = Core\Data::instance();
		if (empty($data->key) || empty($data->email)) {
			$args['notices']['error'][] = 'Missing API Key or email value';
			return;
		}

		// Check zone data
		if (empty($data->zone['id'])) {
			$args['notices']['error'][] = 'Missing API zone detected';
			return;
		}

		// Determine action
		$enable = empty($_POST['hd-devmode-action'])? false : ('on' == $_POST['hd-devmode-action']);

		// Enable or disable Dev mode
		$response = API\CloudFlare::instance($data->key, $data->email)->setDevMode($data->zone['id'], $enable);
		if (is_wp_error($response)) {
			$args['notices']['error'][] = 'CloudFlare API request error';

		// Success
		} else {
			$data->zone['development_mode'] = $response['result']['time_remaining'];
			$data->save(['zone' => $data->zone, 'dev_mode_at' => time()]);
			$args['notices']['success'][] = 'Updated <strong>development mode</strong> status via CloudFlare API';
		}
	}



	/**
	 * Purge all files
	 */
	public function purge(&$args) {

		// Check nonce
		if (empty($_POST['hd-purge-nonce']) || !wp_verify_nonce($_POST['hd-purge-nonce'], 'cloudflare_purge')) {
			$args['notices']['error'][] = 'Invalid form security code, please try again.';
			return;
		}

		// Check API data
		$data = Core\Data::instance();
		if (empty($data->key) || empty($data->email)) {
			$args['notices']['error'][] = 'Missing API Key or email value';
			return;
		}

		// Check zone data
		if (empty($data->zone['id'])) {
			$args['notices']['error'][] = 'Missing API zone detected';
			return;
		}

		$response = API\CloudFlare::instance($data->key, $data->email)->purgeZone($data->zone['id']);
		if (is_wp_error($response)) {
			$args['notices']['error'][] = 'CloudFlare API request error';

		// Success
		} else {
			$args['notices']['success'][] = 'Purged all files successfully via CloudFlare API';
		}
	}



	// Internal
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Check current domain calling the API
	 */
	private function checkDomain($key, $email) {

		// Initialize
		$page = $maxPages = 1;

		// Enum page
		while ($page <= $maxPages) {

			// Perform the API call
			$response = API\CloudFlare::instance($key, $email)->getZones($page);
			if (is_wp_error($response))
				return $response;

			// Check domains
			if (false !== ($zone = $this->matchZone($response['result'])))
				return $zone;

			// Max pages check
			if (1 == $page)
				$maxPages = empty($response['result_info']['total_pages'])? 0 : (int) $response['result_info']['total_pages'];

			// Next page
			$page++;
		}

		// Done
		return false;
	}



	/**
	 * Compare zones with current domain
	 */
	private function matchZone($result) {

		//Check array
		if (empty($result) || !is_array($result))
			return false;

		// Current domain
		$domain = strtolower(trim(Core\Data::instance()->domain));

		// Enum zones
		foreach ($result as $zone) {

			// Check zone name
			$name = strtolower(trim($zone['name']));
			if ('' === $name || false === strpos($domain, $name))
				continue;

			// Check same alue
			if ($domain == $name)
				return $zone;

			// Check length
			$length = strlen($name);
			if ($length > strlen($domain))
				continue;

			// Ends with the zone name
			if (substr($domain, -$length) === $name)
				return $zone;
		}

		// Not found
		return false;
	}



}
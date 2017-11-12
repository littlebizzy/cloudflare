<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Admin;

// Aliased namespaces
use \LittleBizzy\CloudFlare\Core;

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
			$keyChanged = true;
			Core\Data::instance()->save(['key' => $key]);
		}

		// Check email
		$email = isset($_POST['tx-credentials-email'])? trim($_POST['tx-credentials-email']) : false;
		if (empty($email)) {
			$args['notices']['error'][] = 'Missing CloudFlare API email';

		// Validate
		} elseif (!is_email($email)) {
			$args['notices']['error'][] = 'The email <strong>'.esc_html($email).'<strong> is not valid';
			$email = null;

		// Check if is a new email
		} elseif ($email != Core\Data::instance()->email) {
			$emailChanged = true;
			Core\Data::instance()->save(['email' => $email]);
		}

		// Check values for API validation
		if (empty($key) || empty($email))
			return;

		// Check changes
		if (empty($keyChanged) && empty($emailChanged)) {
			if ('valid' == Core\Data::instance()->status)
				return;
		}

		// Request with current database values
		Core\API::instance()->setCredentials($key, $email, Core\Data::instance()->domain);
		if (false == $domain = Core\API::instance()->getDomain()) {
			Core\Data::instance()->save(['status' => 'error']);
			$args['notices']['error'][] = 'The current domain could not be verified';
			return;
		}
	}



	/**
	 * API devMode request
	 */
	public function devMode(&$args) {

	}



}
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

		// Initialize
		$this->init($args);

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
		if (empty($keyChanged) && empty($emailChanged))
			return;

		Core\API::instance()->setCredentials($key, $email);
		$result = Core\API::instance()->getDomain();
	}



	/**
	 * API devMode request
	 */
	public function devMode(&$args) {

	}



	// Internal
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Set notices array
	 */
	private function init(&$args) {
		if (empty($args['notices']) || !is_array($args['notices']))
			$args['notices'] = ['error' => [], 'success' => []];
	}



}
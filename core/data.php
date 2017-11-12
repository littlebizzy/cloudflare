<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Core;

// Aliased namespaces
use \LittleBizzy\CloudFlare\Libraries;
use \LittleBizzy\CloudFlare\Helpers;

/**
 * Data class
 *
 * @package CloudFlare
 * @subpackage Core
 */
final class Data {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Single class instance
	 */
	private static $instance;



	/**
	 * Options object
	 */
	public $options;



	/**
	 * CloudFlare
	 */
	public $key;
	public $email;
	public $domain;
	public $zone;
	public $devModeAt;



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
		$this->init();
		$this->load();
		$this->checkDevMode();
	}



	/**
	 * Set the Options object
	 */
	private function init() {

		// Domain
		$this->setDomain();

		// Options object
		$this->options = new Libraries\Options(Helpers\Plugin::instance()->prefix.'_cloudflare_');
	}



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Load data
	 */
	public function load()  {
		$this->key 		 = $this->options->get('key', true);
		$this->email 	 = $this->options->get('email', true);
		$this->zone 	 = $this->sanitizeZone(@json_decode($this->options->get('zone', true), true));
		$this->devModeAt = (int) $this->options->get('dev_mode_at');
	}



	/**
	 * Save data
	 */
	public function save($values, $reload = true) {

		// Check arguments
		if (empty($values) || !is_array($values))
			return;

		// Check key value
		if (isset($values['key']))
			$this->options->set('key', $values['key'], false, true);

		// Check email value
		if (isset($values['email']))
			$this->options->set('email', $values['email'], false, true);

		// Check zone value
		if (isset($values['zone']))
			$this->options->set('zone', @json_encode($values['zone']), false, true);

		// Check Dev mode timestamp
		if (isset($values['dev_mode_at']))
			$this->options->set('dev_mode_at', (int) $values['dev_mode_at']);

		// Check reload
		if ($reload)
			$this->load();
	}



	/**
	 * Remove options from database
	 */
	public function remove() {
		$this->options->del(['key', 'email', 'zone', 'dev_mode_at']);
	}



	/**
	 * Sanitize zone data
	 */
	public function sanitizeZone($zone) {

		// Check array
		if (empty($zone) || !is_array($zone))
			$zone = array();

		// Sanitize values
		return [
			'id' 				=> isset($zone['id'])? 				 $zone['id'] : '',
			'name' 				=> isset($zone['name'])? 			 $zone['name'] : '',
			'status' 			=> isset($zone['status'])? 			 $zone['status'] : '',
			'paused' 			=> isset($zone['paused'])? 			 $zone['paused'] : '',
			'type' 				=> isset($zone['type'])? 			 $zone['type'] : '',
			'development_mode' 	=> isset($zone['development_mode'])? $zone['development_mode'] : '',
		];
	}



	// Internal
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Check a valid Dev Mode due the 3 hours limit
	 */
	private function checkDevMode() {

		// Check timestamp
		if (empty($this->devModeAt))
			return;

		// Check current value
		$devMode = (int) $this->zone['development_mode'];
		if ($devMode <= 0)
			return;

		// Check 3 hours limit
		if (time() - $this->devModeAt >= 10800) {
			$this->zone['development_mode'] = 0;
			$this->save(['zone' => $this->zone, 'dev_mode_at' => 0]);
		}
	}



	/**
	 * Current domain
	 */
	private function setDomain() {
		$this->domain = @parse_url(site_url(), PHP_URL_HOST);
		if (0 === stripos($this->domain, 'www.'))
			$this->domain = substr($this->domain, 4);
	}



}
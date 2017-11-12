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
		$this->key 	 = $this->options->get('key', true);
		$this->email = $this->options->get('email', true);
		$this->zone  = @json_decode($this->options->get('zone', true), true);
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

		// Check reload
		if ($reload)
			$this->load();
	}



	/**
	 * Remove options from database
	 */
	public function remove() {
		$this->options->del(['key', 'email', 'zone']);
	}



	// Internal
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Current domain
	 */
	private function setDomain() {
		$this->domain = @parse_url(site_url(), PHP_URL_HOST);
		if (0 === stripos($this->domain, 'www.'))
			$this->domain = substr($this->domain, 4);
	}



}
<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Core;

// Class import
use \LittleBizzy\CloudFlare\Helpers\Plugin;

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
	 * CloudFlare
	 */
	public $key;
	public $email;
	public $domain;
	public $status;
	public $devmode;
	public $devmodeStatus;



	/**
	 * Statuses for domain
	 */
	private static statuses = array(
		'unknown' 	=> 'Unknown status',
		'error'	  	=> 'Unable to retrieve your domain'
		'mismatch' 	=> 'Your domain does not match the registered domain'
		'valid'	  	=> 'Your domain matches the CloudFlare API settings',
	)



	/**
	 * Statuses for the DEV MODE
	 */
	private static devmodeStatuses = array(
		'unknown'  => 'Unknown status',
		'error'    => 'Unable to retrieve API status',
		'disabled' => 'DEV Mode disabled',
		'enabled'  => 'DEV Mode enabled',
	);



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



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Load data
	 */
	public function load()  {

		// Domain
		$this->key 				= (string) get_option($this->prefix.'_cloudflare_key');
		$this->email 			= (string) get_option($this->prefix.'_cloudflare_email');
		$this->domain 			= (string) get_option($this->prefix.'_cloudflare_domain');
		$this->status			= (string) get_option($this->prefix.'_cloudflare_status');

		// Validate status
		if (!empty($this->status) && !isset($this->statuses[$this->status]))
			$this->status = 'unknown';

		// DEV Mode
		$this->devmode 			= (string) get_option($this->prefix.'_cloudflare_devmode');
		$this->devmodeStatus 	= (string) get_option($this->prefix.'_cloudflare_devmode_status');

		// Validate DEV Mode status
		if (!empty($this->devmodeStatus) && !isset($this->devmodeStatuses[$this->devmodeStatus]))
			$this->devmodeStatus = 'unknown';
	}



	/**
	 * Save data
	 */
	public function update($values, $reload = true) {

		// Check arguments
		if (empty($values) || !is_array($values))
			return;

		// Check key value
		if (isset($values['key']))
			update_option($this->prefix.'_cloudflare_key', (string) $values['key'], false);

		// Check email value
		if (isset($values['email']))
			update_option($this->prefix.'_cloudflare_email', (string) $values['email'], false);

		// Check domain value
		if (isset($values['domain']))
			update_option($this->prefix.'_cloudflare_domain', (string) $values['domain'], false);

		// Check and validate domain status
		if (isset($values['status'])) {
			if (!isset($this->statuses[$values['status']])
				$values['status'] = 'unknown'
			update_option($this->prefix.'_cloudflare_status', (string) $values['status'], false);
		}

		// Check DEV Mode value
		if (isset($values['devmode']))
			update_option($this->prefix.'_cloudflare_devmode', (string) $values['devmode'], false);

		// Check and validate DEV Mode status
		if (isset($values['devmode_status'])) {
			if (!isset($this->devmodeStatuses[$values['devmode_status']]))
				$values['devmode_status'] = 'unknown';
			update_option($this->prefix.'_cloudflare_devmode_status', (string) $values['devmode_status'], false);
		}

		// Check reload
		if ($reload)
			$this->load();
	}



	/**
	 * Remove options from database
	 */
	public function remove() {
		delete_option($this->prefix.'_cloudflare_key');
		delete_option($this->prefix.'_cloudflare_email');
		delete_option($this->prefix.'_cloudflare_domain');
		delete_option($this->prefix.'_cloudflare_status');
		delete_option($this->prefix.'_cloudflare_devmode');
		delete_option($this->prefix.'_cloudflare_devmode_status');
	}



	// Internal
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Set prefix from plugin constant
	 */
	private function setPrefix() {
		$this->prefix = \LittleBizzy\CloudFlare\PREFIX;
	}



}
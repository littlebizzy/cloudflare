<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Core;

// Aliased plugin namespace
use \LittleBizzy\CloudFlare\Libraries;

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



	/**
	 * Set the Options object
	 */
	private function init() {
		$this->options = new Libraries\Options(Helpers\Plugin::instance()->prefix.'_cloudflare_');
	}



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Load data
	 */
	public function load()  {

		// Domain
		$this->key 		= $this->options->get('key', true);
		$this->email 	= $this->options->get('email', true);
		$this->domain 	= $this->options->get('domain', true);
		$this->status 	= $this->options->get('status', true);

		// Validate status
		if (!empty($this->status) && !isset($this->statuses[$this->status]))
			$this->status = 'unknown';

		// DEV Mode
		$this->devmode 			= $this->options->get('devmode', true);
		$this->devmodeStatus 	= $this->options->get('devmode_status', true);

		// Validate DEV Mode status
		if (!empty($this->devmodeStatus) && !isset($this->devmodeStatuses[$this->devmodeStatus]))
			$this->devmodeStatus = 'unknown';
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

		// Check domain value
		if (isset($values['domain']))
			$this->options->set('domain', $values['domain'], false, true);

		// Check and validate domain status
		if (isset($values['status'])) {
			if (!isset($this->statuses[$values['status']])
				$values['status'] = 'unknown'
			$this->options->set('status', $values['status'], false, true);
		}

		// Check DEV Mode value
		if (isset($values['devmode']))
			$this->options->set('devmode', $values['devmode'], false, true);

		// Check and validate DEV Mode status
		if (isset($values['devmode_status'])) {
			if (!isset($this->devmodeStatuses[$values['devmode_status']]))
				$values['devmode_status'] = 'unknown';
			$this->options->set('devmode_status', $values['devmode_status'], false, true);
		}

		// Check reload
		if ($reload)
			$this->load();
	}



	/**
	 * Remove options from database
	 */
	public function remove() {
		$this->options->del(['key', 'email', 'domain', 'status', 'devmode', 'devmode_status']);
	}



}
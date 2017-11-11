<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Core;

// Aliased namespaces
use \LittleBizzy\CloudFlare\Helpers;
use \LittleBizzy\CloudFlare\Libraries;
use \LittleBizzy\CloudFlare\Admin;

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
	public $isCloudFlare = false;



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

		// Register plugin hooks
		Helpers\Plugin::instance()->pluginHooks();

		// WP Init hook
		add_action('init', array(&$this, 'init'));

		// Admin mode
		if (is_admin()) {

			// AJAX mode
			if (defined('DOING_AJAX') && DOING_AJAX) {
				// Reserved for future implentations

			// Admin
			} else {

				// Initialize object
				Admin\Admin::instance();
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
		$this->isCloudFlare = Libraries\Ip_Rewrite::isCloudFlare();
	}



}
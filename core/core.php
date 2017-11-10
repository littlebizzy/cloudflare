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
	private $isCloudFlare = false;



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

		// Uninstall hook
		$this->pluginHooks();

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
		$this->isCloudFlare = \LittleBizzy\CloudFlare\Libraries\IpRewrite::isCloudFlare();
	}



	// Internal
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Set plugin hooks
	 */
	private function pluginHooks() {
		register_uninstall_hook(\LittleBizzy\CloudFlare\FILE, array('\LittleBizzy\CloudFlare\Helpers\Plugin', 'uninstall'));
	}



}
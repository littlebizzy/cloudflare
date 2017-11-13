<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Helpers;

// Aliased namespaces
use \LittleBizzy\CloudFlare;

/**
 * Plugin class
 *
 * @package CloudFlare
 * @subpackage Helpers
 */
final class Plugin {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Single class instance
	 */
	private static $instance;



	/**
	 * Basic plugin data
	 */
	public $path;
	public $root;
	public $prefix;
	public $version;



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
		$this->path 	= CloudFlare\FILE;
		$this->root 	= dirname($this->path);
		$this->prefix 	= CloudFlare\PREFIX;
		$this->version 	= CloudFlare\VERSION;
	}



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Handle the plugin hooks
	 */
	public function pluginHooks() {
		$classname = '\\'.__CLASS__;
		register_uninstall_hook($this->path, array($classname, 'uninstall'));
	}



	// WP Hooks
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Plugin activation
	 */
	public static function activation() {}



	/**
	 * Plugin deactivation
	 */
	public static function deactivation() {}



	/**
	 * Plugin uninstall
	 */
	public static function uninstall() {
		CloudFlare\Core\Data::instance()->remove();
	}



}
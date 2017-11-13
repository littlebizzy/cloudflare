<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Admin;

// Aliased namespaces
use \LittleBizzy\CloudFlare\Helpers;

/**
 * Admin class
 *
 * @package CloudFlare
 * @subpackage Admin
 */
final class Admin {



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
	private function __construct() {

		// Admin menu
		add_action('admin_menu', array(&$this, 'adminMenu'));

		// Plugin links
		add_filter('plugin_action_links_'.plugin_basename(Helpers\Plugin::instance()->path), array(&$this, 'settingsLink'));
	}



	// WP hooks
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Display menu
	 */
	public function adminMenu() {
		add_options_page('CloudFlare', 'CloudFlare', 'manage_options', 'cloudflare', array(&$this, 'adminPage'));
	}



	/**
	 * Displays the settings page
	 */
	public function adminPage() {

		// Exit on unauthorized access
		if (!current_user_can('manage_options'))
			die;

		// Display page
		Settings::instance();
	}



	/**
	 * Add a settings link from the plugins page
	 */
	public function settingsLink($links) {
		$links[] = '<a href="'.get_admin_url(null, 'options-general.php?page=cloudflare').'">Settings</a>';
		return $links;
	}



}

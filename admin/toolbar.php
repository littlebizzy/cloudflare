<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Admin;

// Aliased namespaces
use \LittleBizzy\CloudFlare\Helpers;

/**
 * Toolbar class
 *
 * @package CloudFlare
 * @subpackage Admin
 */
class Toolbar {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Single class instance
	 */
	private static $instance;



	/**
	 * Plugin object
	 */
	private $plugin;



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

		// Check current user permissions
		if (!current_user_can('manage_options')) {
			return;
		}

		// Plugin object reference
		$this->plugin = Helpers\Plugin::instance();

		// Add the admin bar
		add_action('admin_bar_menu', [$this, 'add']);
	}



	// WP Hooks
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Adds the admin bar link
	 */
	public function add(&$wp_admin_bar) {

		// Initialize
		$menuItems = [];

		// Prepare base URL
		$url = admin_url('options-general.php?page=cloudflare');

		// URL for submenu actions
		$urlAction = $url.'&'.$this->plugin->prefix.'_nonce='.wp_create_nonce('cloudflare_toolbar').'&'.$this->plugin->prefix.'_action=';

		// Top menu
		$menuItems[] = [
			'id'     => $this->plugin->prefix.'-menu',
			'parent' => 'top-secondary',
			'title'  => 'CloudFlare',
			'href'   => $url,
			'meta'   => [
				'tabindex' => -1,
			],
		];

		$menuItems[] = [
			'id'     => $this->plugin->prefix.'-menu-cloudflare',
			'parent' => $this->plugin->prefix.'-menu',
			'title'  => 'Purge All Files',
			'href'   => $urlAction.'purgeall',
			'meta'   => [
				'tabindex' => -1,
			],
		];

		$menuItems[] = [
			'id'     => $this->plugin->prefix.'-menu-opcache',
			'parent' => $this->plugin->prefix.'-menu',
			'title'  => 'Enable Dev Mode',
			'href'   => $urlAction.'devmode',
			'meta'   => [
				'tabindex' => -1,
			],
		];

		// Add menus
		foreach ($menuItems as $menuItem) {
			$wp_admin_bar->add_menu($menuItem);
		}
	}



}

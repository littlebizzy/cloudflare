<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Admin;

// Aliased namespaces
use \LittleBizzy\CloudFlare\Core;
use \LittleBizzy\CloudFlare\Helpers;

/**
 * Dashboard class
 *
 * @package CloudFlare
 * @subpackage Admin
 */
class Dashboard {



	/**
	 * Single class instance
	 */
	private static $instance;



	/**
	 * Plugin object
	 */
	private $plugin;



	/**
	 * Create or retrieve instance
	 */
	public static function instance() {

		// Check instance
		if (!isset(self::$instance)) {
			self::$instance = new self;
		}

		// Done
		return self::$instance;
	}



	/**
	 * Constructor
	 */
	private function __construct() {

		// Check DNS widget
		if (!defined('CLOUDFLARE_WIDGET_DNS') || CLOUDFLARE_WIDGET_DNS) {
			wp_add_dashboard_widget(Helpers\Plugin::instance()->prefix.'_dns_dashboard_widget', 'DNS Records (CloudFlare)', [$this, 'widgetDNS']);
		}
	}



	public function widgetDNS() {
		echo '<p>test</p>';
	}


}
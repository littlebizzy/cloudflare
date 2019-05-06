<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Core;

// Aliased namespaces
use \LittleBizzy\CloudFlare\Helpers;

/**
 * CRON class
 *
 * @package CloudFlare
 * @subpackage Core
 */
final class CRON {


	/**
	 * Single class instance
	 */
	private static $instance;



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

		// Set interval key
		$this->intervalDNSUpdate = Helpers\Plugin::instance()->prefix.'_dns_records_update';

		// Add intervals filter
		add_filter('cron_schedules', [$this, 'intervals']);
	}



	/**
	 * Configure custom intervals
	 */
	public function intervals($schedules) {

		// Set interval
		$schedules[$this->intervalDNSUpdate] = [
			'interval' => 1800,
			'display'  => 'Update DNS Records each 30 minutes',
		];

		// Done
		return $schedules;
	}



	/**
	 * Configure schedulings
	 */
	public function schedulings() {

		// Set action
		add_action('cronDNSRecords', [$this, 'cronDNSRecords']);

		// Check event
		$event = Helpers\Plugin::instance()->prefix.'_dns_records_update';
		if (!wp_next_scheduled($event)) {

			// Schedule event and action
			wp_schedule_event(time(), $this->intervalDNSUpdate, 'cronDNSRecords');
		}
	}



	/**
	 * Update DNS records via cron
	 */
	public function cronDNSRecords() {
		DNS::instance()->update();
	}



}
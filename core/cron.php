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
	 * CRON settings
	 */
	private $eventDNS;
	private $intervalDNS;



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

		// Settings
		$this->eventDNS = Helpers\Plugin::instance()->prefix.'_dns_records_update';
		$this->intervalDNS = Helpers\Plugin::instance()->prefix.'_dns_records_interval';

		// Add intervals filter
		add_filter('cron_schedules', [$this, 'intervals']);
	}



	/**
	 * Configure custom intervals
	 */
	public function intervals($schedules) {

		// Set interval
		$schedules[$this->intervalDNS] = [
			'interval' => 1800,
			'display'  => 'Update DNS Records each 30 minutes',
		];

		// Done
		return $schedules;
	}



	/**
	 * Remove current scheduling
	 */
	public function unschedule() {
		wp_clear_scheduled_hook($this->eventDNS);
	}



	/**
	 * Configure schedulings
	 */
	public function schedulings() {

		// Set action
		add_action($this->eventDNS, [$this, 'cronDNSRecords']);

		// Check event
		if (!wp_next_scheduled($this->eventDNS)) {

			// Schedule event and action
			wp_schedule_event(time() + 30, $this->intervalDNS, $this->eventDNS);
		}
	}



	/**
	 * Update DNS records via cron
	 */
	public function cronDNSRecords() {
		if (!defined('CLOUDFLARE_WIDGET_DNS') || CLOUDFLARE_WIDGET_DNS) {
			DNS::instance()->update();
		}
	}



}
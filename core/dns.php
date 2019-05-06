<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Core;

// Aliased namespaces
use \LittleBizzy\CloudFlare\API;

/**
 * DNS class
 *
 * @package CloudFlare
 * @subpackage Core
 */
final class DNS {



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
	private function __construct() {}



	/**
	 * Update from remote sources
	 */
	public function update() {

		// Load data
		$data = Data::instance();

		// Force load
		$data->load();

		// Initialize
		$items = [];
		$timestamp = time();

		// Check key, email and zone
		if (!empty($data->key) &&
			!empty($data->email) &&
			!empty($data->zone['id'])) {

			// Remote API request
			$request = API\Cloudflare::instance($data->key, $data->email)->getDNSRecords($data->zone['id']);

			// Check results
			if (!empty($request['result']) && is_array($request['result'])) {
				foreach ($request['result'] as $item) {
					$items[] = [
						'type' 		=> isset($item['type'])? 	$item['type'] 		: '',
						'name' 		=> isset($item['name'])? 	$item['name'] 		: '',
						'content' 	=> isset($item['content'])? $item['content'] 	: '',
						'ttl' 		=> isset($item['ttl'])? 	$item['ttl'] 		: '',
					];
				}
			}
		}

		// Prepare data
		$payload = [
			'items' 	=> $items,
			'timestamp' => $timestamp,
		];

		// Save
		$data->save([
			'dns_records' => $payload,
		]);

		// Done
		return $payload;
	}



}
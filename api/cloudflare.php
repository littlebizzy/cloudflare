<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\API;

/**
 * Cloudflare API class
 *
 * @package CloudFlare
 * @subpackage API
 */
final class Cloudflare {



	// Constants
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Endpoint base URL
	 */
	const BASE_URL = 'https://api.cloudflare.com/client/v4/';



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Single class instance
	 */
	private static $instance;



	/**
	 * Current values
	 */
	private $key;
	private $email;



	/**
	 * Last error WP object
	 */
	public $error;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Create or retrieve instance
	 */
	public static function instance($key = null, $email = null) {

		// Check instance
		if (!isset(self::$instance))
			self::$instance = new self($key, $email);

		// Done
		return self::$instance;
	}



	/**
	 * Constructor
	 */
	private function __construct($key, $email) {
		$this->key = $key;
		$this->email = $email;
	}



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Retrieve current zones
	 */
	public function getZones($page = 1, $per_page = 50) {
		return $this->request('zones', [
			'method' 	=> 'GET',
			'body' 		=> [
				'page' 		=> $page,
				'per_page' 	=> $per_page,
			],
		]);
	}



	/**
	 * Retrieve zone details
	 */
	public function getZone($zoneId) {
		return $this->request('zones/'.$zoneId, [
			'method' 	=> 'GET',
		]);
	}



	/**
	 * Purge zone cache
	 */
	public function purgeZone($zoneId) {
		return $this->request('zones/'.$zoneId.'/purge_cache', [
			'method' 	=> 'GET',
		]);
	}



	/**
	 * Makes an API request
	 */
	public function request($endpoint, $args) {

		// Perform request
	    $result = wp_remote_request(self::BASE_URL.$endpoint, array_merge([
	        'timeout' 	=> 10,
	        'sslverify' => true,
			'headers' 	=> [
				'Content-Type'	=> 'application/json',
				'X-Auth-Key'	=> $this->key,
				'X-Auth-Email'	=> $this->email,
			],
	    ], $args));

		// Check error
		if (is_wp_error($result))
			return $result;

		// Check response code
		if (empty($result['response']) || empty($result['response']['code']) || 200 != $result['response']['code'])
			return $this->newError('cloudflare_api_error_code', $result);

		// Check body
		if (empty($result['body']))
			return $this->newError('cloudflare_api_error_body', $result);

		// Cast body
		$response = @json_decode($result['body'], true);
		if (empty($response) || !is_array($response))
			return $this->newError('cloudflare_api_error_response', $result);

		// Check result
		if (empty($response['result']) || empty($response['result_info']) || 'true' != $response['success'])
			return $this->newError('cloudflare_api_error_result', $response);

		// Done
		return $response;
	}





	/**
	 * Retrieve associated domain
	 */
	public function getDomain() {

		// Perform request
		$result = $this->request([
			'a' => 'zone_load_multi',
		]);

		// Direct error
		if (is_wp_error($result)) {
			$this->error = $result;
			return false;
		}
//print_r($result);
		// Check zones
		$zones = (empty($result->response->zones->objs) || !is_array($result->response->zones->objs))? array() : $result->response->zones->objs;
		if (0 == count($zones)) {
			$this->error = $this->newError('match_domain', 'API did not return any domains');
			return false;
		}

		// Collect
		$names = [];
		foreach ($zones as $zone) {
			if (!empty($zone->zone_name))
				$names[strtolower($zone->zone_name)] = $zone;
		}

		// Check values
		if (empty($names)) {
			$this->error = $this->newError('match_domain', 'API did not return any domains');
			return false;
		}

		// Check if exists
		$domain = strtolower($this->domain);
		if (isset($names[$domain]))
			return $names[$domain];

		// Now see match_domain_to_zone
		$this->error = $this->newError('match_domain', 'API did not match any domains');
		return false;
	}


/**
 * @param $domain        string      the domain portion of the WP URL
 * @param $zone_names    array       an array of zone_names to compare against
 *
 * @returns null|string null in the case of a failure, string in the case of a match
 */
function match_domain_to_zone($domain, $zones) {

    $splitDomain = explode('.', $domain);
    $totalParts = count($splitDomain);

    // minimum parts for a complete zone match will be 2, e.g. blah.com
    for ($i = 0; $i <= ($totalParts - 2); $i++) {
        $copy = $splitDomain;
        $currentDomain = implode('.', array_splice($copy, $i));
        foreach ($zones as $zone_name) {
            if (strtolower($currentDomain) == strtolower($zone_name)) {
                return $zone_name;
            }
        }
    }

    return null;
}


	/**
	 * Retrieve DEV MODE status
	 */
	public function getDevMode() {

		// Perform request
		$result = $this->request([
			'a' => 'zone_load',
			'z' => $this->host,
		]);

		// Check error
		if (is_wp_error($result)) {
			$this->error = $result;
			return false;
		}

		// Need improvements
		return isset($result->response->zone->obj->zone_status_class)? $result->response->zone->obj->zone_status_class : false;
	}



	/**
	 * Check DEV MODE status value
	 */
	public function isDevMode($value) {
		// See if value match "status-dev-mode"
	}



	/**
	 * Update DEV MODE status
	 *
	 * @param $value	boolean
	 */
	function setDevMode($value) {

		// Perform request
		$result = $this->request([
			'a' => 'devmode',
	        'z' => $this->host,
	        'v' => $value? 'status-dev-mode' : 'other-value?',
		]);

		// Check error
	    if (is_wp_error($result)) {
			$this->error = $result;
			return false;
		}

		// Done
	    return $result;
	}






	/**
	 * API Request
	 */
	private function request_old($fields, $json = true) {

		// Check credentials
		if (empty($this->key) || empty($this->email))
			$this->newError('missing_credentials', 'API credentials must be established before the API request call.');

		// Prepare fields
		$fields = array_merge($fields, [
			'tkn'	=> $this->key,
			'email'	=> $this->email,
		]);

		// Perform request
	    $response = wp_remote_post(self::ENDPOINT_URL, [
			'body'			=> $fields,
	        'timeout'       => 20,
	        'sslverify'     => true,
	        'user-agent'    => 'CloudFlare/WordPress/1.3.24',
	    ]);

		// Check error
		if (is_wp_error($response))
			return $response;

		// Check results
		if (!is_array($response))
			$this->newError('unknown_wp_http_error', sprintf('Unknown response from wp_remote_post - unable to contact CloudFlare API'));

		// Check HTTP code
		if (200 != (int) $response['response']['code'])
			return $this->newError('cloudflare', sprintf('CloudFlare API returned a HTTP Error: %s - %s', $response['response']['code'], $response['response']['message']));

		// Check output
		if (!$json)
			return $response;

		// Decode from JSON
		$result = @json_decode($response['body']);
		if (empty($result))
			return $this->newError('json_decode', sprintf('Unable to decode JSON response'), $response['body']);

		// Check for the CloudFlare API failure response
		if (property_exists($result, 'result') && $result->result != 'success') {
			$msg = (property_exists($result, 'msg') && !empty($result->msg))? $result->msg : 'Unknown Error';
			return $this->newError('cloudflare', $msg);
		}

		// Done
		return $result;
	}



	// Utils
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Creates new WP_Error object
	 */
	private function newError($code = '', $message = '', $data = '') {
		return new \WP_Error($code, $message, $data);
	}



}
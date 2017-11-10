<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Core;

/**
 * API class
 *
 * @package CloudFlare
 * @subpackage Core
 */
final class API {



	// Constants
	// ---------------------------------------------------------------------------------------------------



	/**
	 * API endpoint URL
	 */
	const ENDPOINT_URL = 'https://www.cloudflare.com/api_json.html';



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Single class instance
	 */
	private static $instance;



	/**
	 * This host
	 */
	public $host;



	/**
	 * Current values
	 */
	private $key;
	private $email;


	/**
	 * Last error object
	 */
	public $error;



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
		$this->setHost();
	}



	/**
	 * Detect current host
	 */
	private function setHost() {
		$this->host = @parse_url(site_url(), PHP_URL_HOST);
		if (0 === stripos($this->host, 'www.'))
			$this->host = substr($this->host, 4);
	}



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Set API values
	 */
	public function setCredentials($key, $email) {
		$this->key = $key;
		$this->email = $email;
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

		// Check zones
		$zones = (empty($result->response->zones) || !is_array($result->response->zones))? array() : $result->response->zones;
		if (0 == count($zones)) {
			$this->error = new WP_Error('match_domain', 'API did not return any domains');
			return false;
		}

		// Collect
		$names = [];
		foreach ($zones as $zone) {
			if (!empty($zone->zone_name))
				$names[] = $zone->zone_name;
		}

		// Check values
		if (empty($names)) {
			$this->error = new WP_Error('match_domain', 'API did not return any domains');
			return false;
		}

		// Now see match_domain_to_zone




		print_r($zones);die;
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
	private function request($fields, $json = true) {

		// Check credentials
		if (empty($this->key) || empty($this->email))
			new WP_Error('missing_credentials', 'API credentials must be established before the API request call.');

		// Prepare fields
		$fields = array_merge($fields, [
			'tkn'	=> $this->key,
			'email'	=> $this->email,
		]);

		// Perform request
	    $response = wp_remote_post(self::ENDPOINT_URL, [
			'body'			=> $fields;
	        'timeout'       => 20,
	        'sslverify'     => true,
	        'user-agent'    => 'CloudFlare/WordPress/1.3.24',
	    ]);

		// Check error
		if (is_wp_error($response))
			return $response;

		// Check results
		if (!is_array($response))
			new WP_Error('unknown_wp_http_error', sprintf('Unknown response from wp_remote_post - unable to contact CloudFlare API'));

		// Check HTTP code
		if (200 != (int) $response['response']['code'])
			return new WP_Error('cloudflare', sprintf('CloudFlare API returned a HTTP Error: %s - %s', $response['response']['code'], $response['response']['message']));

		// Check output
		if (!$json)
			return $response;

		// Decode from JSON
		$result = @json_decode($response['body']);
		if (empty($result))
			return new WP_Error('json_decode', sprintf('Unable to decode JSON response'), $response['body']);

		// Check for the CloudFlare API failure response
		if (property_exists($result, 'result') && $result->result != 'success') {
			$msg = (property_exists($result, 'msg') && !empty($result->msg))? $result->msg : 'Unknown Error';
			return new WP_Error('cloudflare', $msg);
		}

		// Done
		return $result;
	}



}
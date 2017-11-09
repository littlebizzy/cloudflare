<?php



/**
 * Send span comment data
 */
function cloudflare_set_comment_status($comment) {

	// Globals
    global $cloudflare_api_key, $cloudflare_api_email;

	// Check keys
    load_cloudflare_keys();
    if (!$cloudflare_api_key || !$cloudflare_api_email)
        return;

    $payload = array(
        "a"     => $comment->comment_author,
        "am"    => $comment->comment_author_email,
        "ip"    => $comment->comment_author_IP,
        "con"   => substr($comment->comment_content, 0, 100)
    );

    $payload = urlencode(json_encode($payload));

    $args = array(
        'method'        => 'GET',
        'timeout'       => 20,
        'sslverify'     => true,
        'user-agent'    => 'CloudFlare/WordPress/'.CLOUDFLARE_VERSION,
    );

    $url = sprintf('%s?evnt_v=%s&u=%s&tkn=%s&evnt_t=%s', CLOUDFLARE_SPAM_URL, $payload, $cloudflare_api_email, $cloudflare_api_key, 'WP_SPAM');

    // fire and forget here, for better or worse
    wp_remote_get($url, $args);
    // ajax/external-event.html?email=ian@cloudflare.com&t=94606855d7e42adf3b9e2fd004c7660b941b8e55aa42d&evnt_v={%22dd%22:%22d%22}&evnt_t=WP_SPAM
}



/**
 * Retrieve DEV MODE status
 */
function get_dev_mode_status($token, $email, $zone) {

    $fields = array(
        'a'=>"zone_load",
        'tkn'=>$token,
        'email'=>$email,
        'z'=>$zone
    );

    $result = cloudflare_curl(CLOUDFLARE_API_URL, $fields, true);

    if (is_wp_error($result)) {
        trigger_error($result->get_error_message(), E_USER_WARNING);
        return $result;
    }

    if ($result->response->zone->obj->zone_status_class == "status-dev-mode") {
        return "on";
    }

    return "off";
}



/**
 * Set DEV MODE status
 */
function set_dev_mode($token, $email, $zone, $value) {

    $fields = array(
        'a'=>"devmode",
        'tkn'=>$token,
        'email'=>$email,
        'z'=>$zone,
        'v'=>$value
    );

    $result = cloudflare_curl(CLOUDFLARE_API_URL, $fields, true);

    if (is_wp_error($result)) {
        trigger_error($result->get_error_message(), E_USER_WARNING);
        return $result;
    }

    return $result;
}



/**
 * Retrieve domain based on current token and email
 */
function get_domain($token, $email, $raw_domain) {

    $fields = array(
        'a'=>"zone_load_multi",
        'tkn'=>$token,
        'email'=>$email
    );

    $result = cloudflare_curl(CLOUDFLARE_API_URL, $fields, true);

    if (is_wp_error($result)) {
        trigger_error($result->get_error_message(), E_USER_WARNING);
        return $result;
    }

    $zone_count = $result->response->zones->count;
    $zone_names = array();

    if ($zone_count < 1) {
        return new WP_Error('match_domain', 'API did not return any domains');
    }
    else {
        for ($i = 0; $i < $zone_count; $i++) {
            $zone_names[] = $result->response->zones->objs[$i]->zone_name;
        }

        $match = match_domain_to_zone($raw_domain, $zone_names);

        if (is_null($match)) {
            return new WP_Error('match_domain', 'Unable to automatically find your domain (no match)');
        }
        else {
            return $match;
        }
    }
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
 * @param $url       string      the URL to curl
 * @param $fields    array       an associative array of arguments for POSTing
 * @param $json      boolean     attempt to decode response as JSON
 *
 * @returns WP_ERROR|string|object in the case of an error, otherwise a $result string or JSON object
 */
function cloudflare_curl($url, $fields = array(), $json = true) {

    $args = array(
        'method'        => 'GET',
        'timeout'       => 20,
        'sslverify'     => true,
        'user-agent'    => 'CloudFlare/WordPress/'.CLOUDFLARE_VERSION,
    );

    if (!empty($fields)) {
        $args['method'] = 'POST';
        $args['body']   = $fields;
    }

    $response = wp_remote_request($url, $args);

    // if we have an array, we have a HTTP Response
    if (is_array($response)) {
        // Always expect a HTTP 200 from the API

        // HERE BE DRAGONS
        // WP_HTTP does not return conistent types - cURL seems to return an int for the reponse code, streams returns a string.
        if (intval($response['response']['code']) !== 200) {
            // Invalid response code
            return new WP_Error('cloudflare', sprintf('CloudFlare API returned a HTTP Error: %s - %s', $response['response']['code'], $response['response']['message']));
        }
        else {
            if ($json == true) {
                $result = json_decode($response['body']);
                // not a perfect test, but better than nothing perhaps
                if ($result == null) {
                    return new WP_Error('json_decode', sprintf('Unable to decode JSON response'), $result);
                }

                // check for the CloudFlare API failure response
                if (property_exists($result, 'result') && $result->result !== 'success') {
                    $msg = 'Unknown Error';
                    if (property_exists($result, 'msg') && !empty($result->msg)) $msg = $result->msg;
                    return new WP_Error('cloudflare', $msg);
                }

                return $result;
            }
            else {
                return $response['body'];
            }
        }
    }
    else if (is_wp_error($response)) {
        return $response;
    }

    // Should never happen!
    return new WP_Error('unknown_wp_http_error', sprintf('Unknown response from wp_remote_request - unable to contact CloudFlare API'));
}
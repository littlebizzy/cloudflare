<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Admin;

// Class import
use \LittleBizzy\CloudFlare\Helpers\AutoLoad;

/**
 * CloudFlare Admin class
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
		add_filter('plugin_action_links_'.plugin_basename(AutoLoad::instance()->pluginFile), array(&$this, 'settingsLink'));
	}



	// WP hooks
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Display menu
	 */
	public function adminMenu() {
		add_options_page('CloudFlare Settings', 'CloudFlare Settings', 'manage_options', 'cloudflare-littlebizzy', array(&$this, 'adminPage'));
	}



	/**
	 * Displays the settings page
	 */
	public function adminPage() {

		// Exit on unauthorized access
		if (!current_user_can('manage_options'))
			die;

		// Prepare arguments
		$args = empty($_POST['test'])? array() : $this->handleSubmit();

		// Display page
		\LittleBizzy\CloudFlare\Admin\Settings::instance($args);
	}



	// Internal
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Handle the submitted form data
	 */
	public function handleSubmit() {

	}




function cloudflare_conf() {

	// Globals
    global $cloudflare_zone_name, $cloudflare_api_key, $cloudflare_api_email, $cloudflare_protocol_rewrite, $is_cf;
    global $wpdb;

	// Load CF keys
	load_cloudflare_keys();

	// Load dependencies
	require_once dirname(__FILE__).'/cloudflare-api.php';

	// Output messages
    $messages = array(
        'ip_restore_on' => array('text' => __('Plugin Status: True visitor IP is being restored')),
        'comment_spam_on' => array('text' => __('Plugin Status: CloudFlare will be notified when you mark comments as spam')),
        'comment_spam_off' => array('text' => __('Plugin Status: CloudFlare will NOT be notified when you mark comments as spam, enter your API details below')),
        'dev_mode_on' => array('text' => __('Development mode is On. Happy blogging!')),
        'dev_mode_off' => array('text' => __('Development mode is Off. Happy blogging!')),
        'protocol_rewrite_on' => array('text' => __('Protocol rewriting is On. Happy blogging!')),
        'protocol_rewrite_off' => array('text' => __('Protocol rewriting is Off. Happy blogging!')),
        'manual_entry' => array('text' => __('Enter your CloudFlare domain name, e-mail address and API key below')),
        'options_saved' => array('text' => __('Options Saved')),
    );

    $notices = array();
    $warnings = array();
    $errors = array();

    $notices[] = 'ip_restore_on';

    // get raw domain - may include www.
    $urlparts = parse_url(site_url());
    $raw_domain = $urlparts["host"];

    // if we don't have a domain name already populated
    if (empty($cloudflare_zone_name)) {

        if (!empty($cloudflare_api_key) && !empty($cloudflare_api_email)) {
            // Attempt to get the matching host from CF
            $getDomain = get_domain($cloudflare_api_key, $cloudflare_api_email, $raw_domain);

            // If not found, default to pulling the domain via client side.
            if (is_wp_error($getDomain)) {
                $messages['get_domain_failed'] = array('text' => __('Unable to automatically get domain - ' . $getDomain->get_error_message() . ' - please tell us your domain in the form below'));
                $warnings[] = 'get_domain_failed';
            }
            else {
                update_option('cloudflare_zone_name', $getDomain);
                update_option('cloudflare_zone_name_set_once', "TRUE");
                load_cloudflare_keys();
            }
        }
    }

    $db_results = array();

    if ( isset($_POST['submit'])
        && check_admin_referer('cloudflare-db-api','cloudflare-db-api-nonce') ) {

        if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
            die(__('Cheatin&#8217; uh?'));
        }

		$cfPostKeys = array('cloudflare_zone_name', 'cf_key', 'cf_email', 'dev_mode', 'protocol_rewrite');
		foreach($_POST as $key => $value) {
		    if(!empty($value) && in_array($key, $cfPostKeys)) {
		        $_POST[$key] = is_array($value)? '' : htmlentities($value, ENT_QUOTES | ENT_HTML5, "UTF-8");
		    }
		}

        $zone_name = isset($_POST['cloudflare_zone_name'])? trim($_POST['cloudflare_zone_name']) : '';
        $zone_name = str_replace("&period;",".",$zone_name);

        $key = isset($_POST['cf_key'])? trim($_POST['cf_key']) : '';
        $email = isset($_POST['cf_email'])? trim($_POST['cf_email']) : '';

        $allowedCharacters = array(
            "&period;" => ".",
            "&commat;" => "@",
            "&plus;" => "+"
            );

        foreach($allowedCharacters as $arrayKey => $value) {
            $email = str_replace($arrayKey, $value, $email);
        }

        $dev_mode = isset($_POST['dev_mode'])? $_POST['dev_mode'] : '';
        $protocol_rewrite = isset($_POST["protocol_rewrite"])? $_POST["protocol_rewrite"] : '';

        if ( empty($zone_name) ) {
            $zone_status = 'empty';
            $zone_message = 'Your domain name has been cleared.';
            delete_option('cloudflare_zone_name');
        } else {
            $zone_message = 'Your domain name has been saved.';
            update_option('cloudflare_zone_name', $zone_name);
            update_option('cloudflare_zone_name_set_once', "TRUE");
        }

        if ( empty($key) ) {
            $key_status = 'empty';
            $key_message = 'Your key has been cleared.';
            delete_option('cloudflare_api_key');
        } else {
            $key_message = 'Your key has been verified.';
            update_option('cloudflare_api_key', $key);
            update_option('cloudflare_api_key_set_once', "TRUE");
        }

        if ( empty($email) || !is_email($email) ) {
            $email_status = 'empty';
            $email_message = 'Your email has been cleared.';
            delete_option('cloudflare_api_email');
        } else {
            $email_message = 'Your email has been verified.';
            update_option('cloudflare_api_email', $email);
            update_option('cloudflare_api_email_set_once', "TRUE");
        }

        if (in_array($protocol_rewrite, array("0","1"))) {
            update_option('cloudflare_protocol_rewrite', $protocol_rewrite);
        }

        // update the values
        load_cloudflare_keys();

        if ($cloudflare_api_key != "" && $cloudflare_api_email != "" && $cloudflare_zone_name != "" && $dev_mode != "") {

            $result = set_dev_mode($cloudflare_api_key, $cloudflare_api_email, $cloudflare_zone_name, $dev_mode);

            if (is_wp_error($result)) {
                trigger_error($result->get_error_message(), E_USER_WARNING);
                $messages['set_dev_mode_failed'] = array('text' => __('Unable to set development mode - ' . $result->get_error_message() . ' - try logging into cloudflare.com to set development mode'));
                $errors[] = 'set_dev_mode_failed';
            }
            else {
                if ($dev_mode && $result->result == 'success') {
                    $notices[] = 'dev_mode_on';
                }
                else if (!$dev_mode && $result->result == 'success') {
                    $notices[] = 'dev_mode_off';
                }
            }
        }

        $notices[] = 'options_saved';
    } // End of submit check

    if (!empty($cloudflare_api_key) && !empty($cloudflare_api_email) && !empty($cloudflare_zone_name)) {
        $dev_mode = get_dev_mode_status($cloudflare_api_key, $cloudflare_api_email, $cloudflare_zone_name);

        if (is_wp_error($dev_mode)) {
            $messages['get_dev_mode_failed'] = array('text' => __('Unable to get current development mode status - ' . $dev_mode->get_error_message()));
            $errors[] = 'get_dev_mode_failed';
        }
    }
    else {
        $warnings[] = 'manual_entry';
    }

    if (!empty($cloudflare_api_key) && !empty($cloudflare_api_email)) $notices[] = 'comment_spam_on';
    else $warnings[] = 'comment_spam_off';

    ?>
    <div class="wrap">

        <?php if ($is_cf) { ?>
            <h3>You are currently using CloudFlare!</h3>
        <?php } ?>

        <?php if ($notices) { foreach ( $notices as $m ) { ?>
            <div class="updated" style="border-left-color: #7ad03a; padding: 10px;"><?php echo $messages[$m]['text']; ?></div>
        <?php } } ?>

        <?php if ($warnings) { foreach ( $warnings as $m ) { ?>
            <div class="updated" style="border-left-color: #ffba00; padding: 10px;"><em><?php echo $messages[$m]['text']; ?></em></div>
        <?php } } ?>

        <?php if ($errors) { foreach ( $errors as $m ) { ?>
            <div class="updated" style="border-left-color: #dd3d36; padding: 10px;"><b><?php echo $messages[$m]['text']; ?></b></div>
        <?php } } ?>

        <h4><?php _e('CLOUDFLARE WORDPRESS PLUGIN:'); ?></h4>

        CloudFlare has developed a plugin for WordPress. By using the CloudFlare WordPress Plugin, you receive:
        <ol>
            <li>Correct IP Address information for comments posted to your site</li>
            <li>Better protection as spammers from your WordPress blog get reported to CloudFlare</li>
            <li>If cURL is installed, you can enter your CloudFlare API details so you can toggle <a href="https://support.cloudflare.com/hc/en-us/articles/200168246-What-does-CloudFlare-Development-mode-mean-" target="_blank">Development mode</a> on/off using the form below</li>
        </ol>

        <h4>VERSION COMPATIBILITY:</h4>

        The plugin is compatible with WordPress version 2.8.6 and later. The plugin will not install unless you have a compatible platform.

        <h4>THINGS YOU NEED TO KNOW:</h4>

        <ol>
            <li>The main purpose of this plugin is to ensure you have no change to your originating IPs when using CloudFlare. Since CloudFlare acts a reverse proxy, connecting IPs now come from CloudFlare's range. This plugin will ensure you can continue to see the originating IP. Once you install the plugin, the IP benefit will be activated.</li>

            <li>Every time you click the 'spam' button on your blog, this threat information is sent to CloudFlare to ensure you are constantly getting the best site protection.</li>

            <li>We recommend that any user on CloudFlare with WordPress use this plugin. </li>

            <li>NOTE: This plugin is complementary to Akismet and W3 Total Cache. We recommend that you continue to use those services.</li>

        </ol>

        <h4>MORE INFORMATION ON CLOUDFLARE:</h4>

        CloudFlare is a service that makes websites load faster and protects sites from online spammers and hackers. Any website with a root domain (ie www.mydomain.com) can use CloudFlare. On average, it takes less than 5 minutes to sign up. You can learn more here: <a href="http://www.cloudflare.com/" target="_blank">CloudFlare.com</a>.

        <hr />

        <form action="" method="post" id="cloudflare-conf">
            <?php wp_nonce_field('cloudflare-db-api','cloudflare-db-api-nonce'); ?>
            <?php if (get_option('cloudflare_api_key') && get_option('cloudflare_api_email')) { ?>
            <?php } else { ?>
                <p><?php printf(__('Input your API key from your CloudFlare Accounts Settings page here. To find your API key, log in to <a href="%1$s">CloudFlare</a> and go to \'Account\'.'), 'https://www.cloudflare.com/a/account/my-account'); ?></p>
            <?php } ?>
            <h3><label for="cloudflare_zone_name"><?php _e('CloudFlare Domain Name'); ?></label></h3>
            <p>
                <input id="cloudflare_zone_name" name="cloudflare_zone_name" type="text" size="50" maxlength="255" value="<?php echo $cloudflare_zone_name; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" /> (<?php _e('<a href="https://www.cloudflare.com/a/overview" target="_blank">Get this?</a>'); ?>)
            </p>
            <p>E.g. Enter domain.com not www.domain.com / blog.domain.com</p>
            <?php if (isset($zone_message)) echo sprintf('<p>%s</p>', $zone_message); ?>
            <h3><label for="key"><?php _e('CloudFlare API Key'); ?></label></h3>
            <p>
                <input id="key" name="cf_key" type="text" size="50" maxlength="48" value="<?php echo get_option('cloudflare_api_key'); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" /> (<?php _e('<a href="https://www.cloudflare.com/a/account/my-account" target="_blank">Get this?</a>'); ?>)
            </p>
            <?php if (isset($key_message)) echo sprintf('<p>%s</p>', $key_message); ?>

            <h3><label for="email"><?php _e('CloudFlare API Email'); ?></label></h3>
            <p>
                <input id="email" name="cf_email" type="text" size="50" maxlength="48" value="<?php echo get_option('cloudflare_api_email'); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" /> (<?php _e('<a href="https://www.cloudflare.com/a/account/my-account" target="_blank">Get this?</a>'); ?>)
            </p>
            <?php if (isset($key_message)) echo sprintf('<p>%s</p>', $key_message); ?>

            <h3><label for="dev_mode"><?php _e('Development Mode'); ?></label> <span style="font-size:9pt;">(<a href="https://support.cloudflare.com/hc/en-us/articles/200168246-What-does-CloudFlare-Development-mode-mean-" target="_blank">What is this?</a>)</span></h3>

            <div style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;">
                <input type="radio" name="dev_mode" value="0" <?php if (!empty($dev_mode) && $dev_mode == "off") echo "checked"; ?>> Off
                <input type="radio" name="dev_mode" value="1" <?php if (!empty($dev_mode) && $dev_mode == "on") echo "checked"; ?>> On
            </div>

            <h3><label for="protocol_rewrite"><?php _e('HTTPS Protocol Rewriting'); ?></label> <span style="font-size:9pt;">(<a href="https://support.cloudflare.com/hc/en-us/articles/203652674" target="_blank">What is this?</a>)</span></h3>

            <div style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;">
                <input type="radio" name="protocol_rewrite" value="0" <?php if ($cloudflare_protocol_rewrite == 0) echo "checked"; ?>> Off
                <input type="radio" name="protocol_rewrite" value="1" <?php if ($cloudflare_protocol_rewrite == 1) echo "checked"; ?>> On
            </div>

            <p class="submit"><input type="submit" name="submit" value="<?php _e('Update options &raquo;'); ?>" /></p>
        </form>

        <?php //    </div> ?>
    </div>
    <?php
}
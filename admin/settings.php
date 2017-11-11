<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Admin;

// Aliased plugin namespace
use \LittleBizzy\CloudFlare;

/**
 * CloudFlare Admin class
 *
 * @package CloudFlare
 * @subpackage Admin
 */
final class Settings {



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
	public static function instance($args = array()) {

		// Check instance
		if (!isset(self::$instance))
			self::$instance = new self($args);

		// Done
		return self::$instance;
	}



	/**
	 * Constructor
	 */
	private function __construct($args) {
		$this->display($args);
	}



	// Output
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Show the settings page
	 */
	private function display($args) { ?>

		<div class="wrap">

			<h1>CloudFlare Settings</h1>

			<?php if (Core::instance()->isCloudFlare) : ?><h3>You are currently using CloudFlare!</h3><?php endif; ?>

			<p>CloudFlare is a service that makes websites load faster and protects sites from online spammers and hackers. Any website with a root domain (ie www.mydomain.com) can use CloudFlare. On average, it takes less than 5 minutes to sign up. You can learn more here: <a href="http://www.cloudflare.com/" target="_blank">CloudFlare.com</a>.</p>

			<form method="POST">

				<input type="hidden" name="hd-api-settings" value="1" />

				<p><label>Domain: </label></p>

				<p><label for="cldflr-tx-api-email">CloudFlare API Email</label><br />
				<input type="text" name="tx-api-email" id="cldflr-tx-api-email" value="<?php echo esc_attr(Core\Data::instance()->email); ?>" /></p>

				<p><label for="cldflr-tx-api-key">CloudFlare API Key</label><br />
				<input type="text" name="tx-api-key" id="cldflr-tx-api-key" value="<?php echo esc_attr(Core\::instance()->key); ?>" /></p>

				<p><input type="submit" value="Update API settings" /></p>

			</form>

			<form method="POST">

				<input type="hidden" name="hd-api-dev-mode" value="1" />

				<p>Dev mode</p>

				<p><input type="submit" value="Update Dev Mode" /></p>

			</form>

		</div>

	<?php }



}
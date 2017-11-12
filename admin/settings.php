<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Admin;

// Aliased namespaces
use \LittleBizzy\CloudFlare\Core;

/**
 * Settings class
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
	public static function instance() {

		// Check instance
		if (!isset(self::$instance))
			self::$instance = new self();

		// Done
		return self::$instance;
	}



	/**
	 * Constructor
	 */
	private function __construct() {

		// Prepare arguments
		$args = [
			'notices' => ['error' => [], 'success' => []]
		];

		// Check submit
		if (isset($_POST['hd-credentials-nonce'])) {
			Submit::instance()->credentials($args);

		} elseif (isset($_POST['hd-devmode-nonce'])) {
			Submit::instance()->devMode($args);
		}

		// Load data
		$args = array_merge($args, [
			'key'   		 => Core\Data::instance()->key,
			'email' 		 => Core\Data::instance()->email,
			'status'		 => Core\Data::instance()->status,
			'isCloudFlare' 	 => Core\Core::instance()->isCloudFlare,
			'devmodeEnabled' => ('enabled' == Core\Data::instance()->devmodeStatus),
		]);

		// Show the forms
		$this->display($args);
	}



	// Output
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Show the settings page
	 */
	private function display($args) {

		// Vars
		extract($args);

		// Display  ?>

		<div class="wrap">

			<?php foreach ($notices['error']  as $message) : ?><div class="notice error"><?php echo $message; ?></div><?php endforeach; ?>

			<?php foreach ($notices['success'] as $message) : ?><div class="notice success"><?php echo $message; ?></div><?php endforeach; ?>

			<h1>CloudFlare Settings</h1>

			<?php if ($isCloudFlare) : ?><h3>You are currently using CloudFlare!</h3><?php endif; ?>

			<p>CloudFlare is a service that makes websites load faster and protects sites from online spammers and hackers. Any website with a root domain (ie www.mydomain.com) can use CloudFlare. On average, it takes less than 5 minutes to sign up. You can learn more here: <a href="http://www.cloudflare.com/" target="_blank">CloudFlare.com</a>.</p>

			<form method="POST">

				<input type="hidden" name="hd-credentials-nonce" value="<?php echo esc_attr(wp_create_nonce('cloudflare_credentials')); ?>" />

				<p><label>Domain: </label></p>

				<p><label for="cldflr-tx-credentials-key">CloudFlare API Key</label><br />
				<input type="text" name="tx-credentials-key" id="cldflr-tx-credentials-key" value="<?php echo esc_attr($key); ?>" /></p>

				<p><label for="cldflr-tx-credentials-email">CloudFlare API Email</label><br />
				<input type="text" name="tx-credentials-email" id="cldflr-tx-credentials-email" value="<?php echo esc_attr($email); ?>" /></p>

				<p><input type="submit" value="Update API settings" /></p>

			</form>

			<?php if (!empty($key) && !empty($email)) : ?>

				<?php if ('enabled' == $status) : ?>

					<form method="POST">

						<input type="hidden" name="hd-devmode-nonce" value="<?php echo esc_attr(wp_create_nonce('cloudflare_devmode')); ?>" />
						<input type="hidden" name="hd-devmode-action" value="" />

						<p>Dev mode</p>

						<p><input type="submit" value="" /></p>

					</form>

				<?php endif; ?>

			<?php endif; ?>

		</div>

	<?php }



}
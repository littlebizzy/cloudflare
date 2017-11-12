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

		// Development mode
		} elseif (isset($_POST['hd-devmode-nonce'])) {
			Submit::instance()->devMode($args);

		// Purge cache
		} elseif (isset($_POST['hd-purge-nonce'])) {
			Submit::instance()->purge($args);
		}

		// Display data
		$data = Core\Data::instance();
		$args = array_merge($args, [
			'key'   		=> $data->key,
			'email' 		=> $data->email,
			'domain' 		=> $data->domain,
			'zone'		 	=> $data->zone,
			'isCloudFlare'  => Core\Core::instance()->isCloudFlare,
			'devMode' 		=> (!empty($data->zone['id']) && $data->zone['development_mode'] > 0),
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

			<?php foreach ($notices['error']  as $message) : ?><div class="notice notice-error"><p><?php echo $message; ?></p></div><?php endforeach; ?>

			<?php foreach ($notices['success'] as $message) : ?><div class="notice notice-success"><p><?php echo $message; ?></p></div><?php endforeach; ?>

			<h1>CloudFlare Settings</h1>

			<?php if ($isCloudFlare) : ?><h3>You are currently using CloudFlare!</h3><?php endif; ?>

			<form method="POST">

				<input type="hidden" name="hd-credentials-nonce" value="<?php echo esc_attr(wp_create_nonce('cloudflare_credentials')); ?>" />

				<p><label>Domain: </label>
				<?php echo esc_html($domain); ?></p>

				<?php if (!empty($zone['name'])) : ?><p><label>Zone: </label>
				<?php echo esc_html($zone['name']); ?></p><?php endif; ?>

				<p><label for="cldflr-tx-credentials-key">CloudFlare API Key</label><br />
				<input type="text" name="tx-credentials-key" id="cldflr-tx-credentials-key" value="<?php echo esc_attr($key); ?>" /></p>

				<p><label for="cldflr-tx-credentials-email">CloudFlare API Email</label><br />
				<input type="text" name="tx-credentials-email" id="cldflr-tx-credentials-email" value="<?php echo esc_attr($email); ?>" /></p>

				<p><input type="submit" class="button button-primary" value="Update API Settings" /></p>

			</form>

			<?php if (!empty($key) && !empty($email) && !empty($zone['id'])) : ?>

				<form method="POST">

					<h3>Development mode: <?php echo $devMode? '<strong>Enabled</strong>' : 'Disabled'; ?></h3>

					<input type="hidden" name="hd-devmode-nonce" value="<?php echo esc_attr(wp_create_nonce('cloudflare_devmode')); ?>" />
					<input type="hidden" name="hd-devmode-action" value="<?php echo $devMode? 'off' : 'on'; ?>" />

					<p><input type="submit" class="button button-primary" value="<?php echo $devMode? 'Turn Off' : 'Turn On' ; ?>" style="width: 200px;" /></p>

				</form>

				<form method="POST">

					<h3>Cache</h3>

					<input type="hidden" name="hd-purge-nonce" value="<?php echo esc_attr(wp_create_nonce('cloudflare_purge')); ?>" />

					<p><input type="submit" class="button button-primary" value="Purge All Files" style="width: 200px;" /></p>

				</form>

			<?php endif; ?>

		</div>

	<?php }



}
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

		// Debug data
		//Core\Data::instance()->domain = 'asimetrica.com';

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

			<div style="margin-bottom: 25px;">

				<h1>CloudFlare</h1>

				<?php if ($isCloudFlare) : ?><h3>You are currently using CloudFlare!</h3><?php endif; ?>

			</div>

			<form method="POST" style="margin-bottom: 50px;">

				<input type="hidden" name="hd-credentials-nonce" value="<?php echo esc_attr(wp_create_nonce('cloudflare_credentials')); ?>" />

				<h2>Site settings</h2>

				<table class="form-table">
					<tr>
						<th scope="row"><label>Current Domain:</label></th>
						<td><?php echo esc_html($domain); ?></td>
					</tr>
					<?php if (!empty($zone['name'])) : ?><tr>
						<th scope="row"><label>Cloudflare Zone:</label></th>
						<td><?php echo esc_html($zone['name']).((empty($zone['status'])? '' : ' ('.esc_html($zone['status']).')')).(empty($zone['paused'])? '' : ' <strong>PAUSED</strong>'); ?></td>
					</tr><?php endif; ?>
					<tr>
						<th scope="row"><label for="cldflr-tx-credentials-key">CloudFlare API Key</label></th>
						<td><input type="text" name="tx-credentials-key" id="cldflr-tx-credentials-key" class="regular-text" value="<?php echo esc_attr($key); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="cldflr-tx-credentials-email">CloudFlare API Email</label></th>
						<td><input type="text" name="tx-credentials-email" id="cldflr-tx-credentials-email" class="regular-text" value="<?php echo esc_attr($email); ?>" /></td>
					</tr>
				</table>

				<p><input type="submit" class="button button-primary" value="Update API Settings" /></p>

			</form>

			<?php if (!empty($key) && !empty($email) && !empty($zone['id'])) : ?>

				<form method="POST">

					<h2 style="margin-bottom: 0">Development Mode</h2>

					<input type="hidden" name="hd-devmode-nonce" value="<?php echo esc_attr(wp_create_nonce('cloudflare_devmode')); ?>" />
					<input type="hidden" name="hd-devmode-action" value="<?php echo $devMode? 'off' : 'on'; ?>" />

					<table class="form-table">
						<tr>
							<th scope="row"><label>Current Status:</label></th>
							<td>
								<p<?php if ($devMode) : ?> style="margin-bottom: 15px;"<?php endif; ?>><span style="width: 100px; display: inline-block;"><?php echo $devMode? '<strong style="color: red;">Enabled</strong>' : 'Disabled'; ?></span>
								<input type="submit" class="button button-primary" value="<?php echo $devMode? 'Turn Off' : 'Turn On' ; ?>" style="width: 120px; margin-top: -5px;" /></p>
								<?php if ($devMode) : ?><p>Development mode will be disabled automatically after 3 hours from activation.</p><?php endif; ?></td>
						</tr>
					</table>

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
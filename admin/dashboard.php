<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Admin;

// Aliased namespaces
use \LittleBizzy\CloudFlare\Core;
use \LittleBizzy\CloudFlare\Helpers;

/**
 * Dashboard class
 *
 * @package CloudFlare
 * @subpackage Admin
 */
class Dashboard {



	/**
	 * Single class instance
	 */
	private static $instance;



	/**
	 * Plugin object
	 */
	private $plugin;



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

		// Copy plugin object
		$this->plugin = Helpers\Plugin::instance();
	}



	/**
	 * AJAX mode
	 */
	public function ajax() {

		// Check nonce
		if (empty($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], $this->plugin->path)) {
			return ['status' => 'error', 'reason' => 'Security verification error. Please reload this page and try again.'];
		}

		// Check DNS widget
		if (defined('CLOUDFLARE_WIDGET_DNS') && !CLOUDFLARE_WIDGET_DNS) {
			return ['status' => 'error', 'reason' => 'Widget DNS records deactivated by custom constant'];
		}

		// Update requests
		Core\DNS::instance()->update();

		// Update data
		Core\Data::instance()->load();

		// Buffering
		@ob_start();
		$this->widgetDNS();
		$html = @ob_get_clean();

		// Prepare response
		$response = ['status' => 'ok', 'html' => $html];

		// Done
		return $response;
	}



	/**
	 * Admin mode
	 */
	public function admin() {

		// Check DNS widget
		if (defined('CLOUDFLARE_WIDGET_DNS') && !CLOUDFLARE_WIDGET_DNS) {
			return;
		}

		// Add admin script
		wp_enqueue_script($this->plugin->prefix, plugins_url('assets/admin.js', $this->plugin->path), ['jquery'], $this->plugin->version, true);

		// Add dashboad widget
		wp_add_dashboard_widget($this->plugin->prefix.'_dns_dashboard_widget', 'DNS Records (CloudFlare)', [$this, 'widgetDNS']);
	}



	/**
	 * Display Widget content
	 */
	public function widgetDNS() {

		// Check AJAX mode and auto-start mode
		$isAJAX = (defined('DOING_AJAX') && DOING_AJAX);
		$isAuto = !empty($_POST['auto']);

		// Prepare nonce
		$nonce = wp_create_nonce($this->plugin->path);

		// Error display
		?><div class="<?php echo esc_attr($this->plugin->prefix); ?>-error" style="display: none; font-weight: bold; color: #dc3232; padding: 5px; margin-bottom: 5px;"></div><?php

		// Check DNS records data
		$DNSRecords = Core\Data::instance()->DNSRecords;
		if (empty($DNSRecords) || !is_array($DNSRecords)) {

			// Check current context
			if (!$isAJAX) {
				?><div class="<?php echo esc_attr($this->plugin->prefix); ?>-data" data-nonce="<?php echo esc_attr($nonce); ?>" data-auto="1">Loading...</div><?php
			}

		// Initialize
		} else {

			// Prepare date
			if (!empty($DNSRecords['timestamp'])) {
				$date = date_i18n('Y-m-d H:i', $DNSRecords['timestamp'] + (get_option('gmt_offset') * HOUR_IN_SECONDS));
			}

			// Wrapper class
			?><div class="<?php echo esc_attr($this->plugin->prefix); ?>-data" data-nonce="<?php echo esc_attr($nonce); ?>"><?php

				// No items
				if (empty($DNSRecords['items'])) {
					?><p>No DNS records found.</p>
					<p>Please check your <a href="<?php echo esc_attr(admin_url('options-general.php?page=cloudflare')); ?>">Cloudflare configuration</a>.</p><?php

				// With content
				} else {

					// Start table
					?><table class="striped"><?php

					// Enum records
					foreach ($DNSRecords['items'] as $item) {

						// Check content length
						$break = (strlen($item['content']) > 35);

						// DNS record row
						?><tr>
							<td><table style="width: 100%;">
								<tr>
									<td style="word-break: break-all; width: 205px;"><strong><?php echo esc_html($item['name']); ?></strong></td>
									<td style="word-break: break-all; width: 45px;"><?php echo esc_html($item['type']); ?></td>
									<td style="word-break: break-all;"><?php echo $break? '' : esc_html($item['content']); ?></td>
								</tr>
								<?php if ($break) : ?>
									<tr>
										<td colspan="3" style="word-break: break-all;"><?php echo esc_html($item['content']); ?></td>
									</tr>
								<?php endif; ?>
							</table></td>
						</tr><?php
					}

					// End table
					?></table><?php
				}

				?><div class="wp-clearfix" style="margin-top: 15px;">

					<?php if (!empty($date)) : ?>
						<div style="float: left;">Last update: <?php echo esc_html($date); ?></div>
					<?php endif; ?>

					<div style="float: right;">

						<a href="#" class="<?php echo esc_attr($this->plugin->prefix); ?>-data-update"<?php if ($isAJAX || ($isAuto && empty($DNSRecords['items']))) : ?> style="display: none;"<?php endif; ?>>Update now <span class="dashicons dashicons-update"></span></a>
						<span class="<?php echo esc_attr($this->plugin->prefix); ?>-data-loading" style="display: none;">Loading...</span>

						<?php if ($isAJAX && !$isAuto) : ?><strong class="<?php echo esc_attr($this->plugin->prefix); ?>-data-done">Updated!</strong><?php endif; ?>

					</div>

				</div><?php

			?></div><?php
		}
	}



}
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

		// Check DNS widget
		if (!defined('CLOUDFLARE_WIDGET_DNS') || CLOUDFLARE_WIDGET_DNS) {
			wp_add_dashboard_widget(Helpers\Plugin::instance()->prefix.'_dns_dashboard_widget', 'DNS Records (CloudFlare)', [$this, 'widgetDNS']);
		}
	}



	/**
	 * Display Widget content
	 */
	public function widgetDNS() {

		// Check DNS records data
		$DNSRecords = Core\Data::instance()->DNSRecords;
		if (empty($DNSRecords) || !is_array($DNSRecords)) {

		// No items
		} elseif (empty($DNSRecords['items'])) {

		// With content
		} else {

			// Start table
			?><table class="striped"><?php

			// Enum records
			foreach ($DNSRecords['items'] as $item) {

				// Check content length
				$break = (strlen($item['content']) > 40);

				// DNS record row
				?><tr>
					<td><table style="width: 100%;">
						<tr>
							<td style="width: 205px; word-break: break-all;"><strong><?php echo esc_html($item['name']); ?></strong></td>
							<td style="width: 45px;"><?php echo esc_html($item['type']); ?></td>
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
	}



}
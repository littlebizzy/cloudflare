<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Helpers;

/**
 * Plugin class
 *
 * @package CloudFlare
 * @subpackage Helpers
 */
class Plugin {



	/**
	 * Plugin activation
	 */
	public static function activation() {}



	/**
	 * Plugin deactivation
	 */
	public static function deactivation() {}



	/**
	 * Plugin uninstall
	 */
	public static function uninstall() {
		\LittleBizzy\CloudFlare\Core\Data::remove();
	}



}
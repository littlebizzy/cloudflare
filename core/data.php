<?php

// Subpackage namespace
namespace LittleBizzy\CloudFlare\Core;

/**
 * Data class
 *
 * @package CloudFlare
 * @subpackage Core
 */
final class Data {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Single class instance
	 */
	private static $instance;



	/**
	 * Detection flag
	 */
	private $isCF;



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
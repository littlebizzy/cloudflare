<?php
/*
Plugin Name: CloudFlare LittleBizzy
Plugin URI: https://www.littlebizzy.com/plugins/cloudflare
Description: Easily connect your WordPress website to CloudFlare's free optimization features, including one-click options to purge cache and enable 'dev' mode.
Version: 1.0.0
Author: LittleBizzy
Author URI: https://www.littlebizzy.com
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Prefix: CDFRLB
*/

// Plugin namespace
namespace LittleBizzy\CloudFlare;

// Avoid script calls via plugin URL
if (!function_exists('add_action'))
	die;

// Plugin constants
const FILE = __FILE__;
const PREFIX = 'cldflr';
const VERSION = '1.0.0';

// Loader
require_once dirname(FILE).'/helpers/autoload.php';

// Main class
Core\Core::instance();
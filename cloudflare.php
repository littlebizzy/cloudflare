<?php
/*
Plugin Name: CloudFlare
Plugin URI: https://www.littlebizzy.com/plugins/cloudflare
Description: Easily connect your WordPress website to free optimization features from CloudFlare, including one-click options to purge cache and enable dev mode.
Version: 1.0.2
Author: LittleBizzy
Author URI: https://www.littlebizzy.com
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Prefix: CLDFLR
*/

// Admin Notices module
require_once dirname(__FILE__).'/admin-notices.php';

// Plugin namespace
namespace LittleBizzy\CloudFlare;

// Block direct calls
if (!function_exists('add_action'))
	die;

// Plugin constants
const FILE = __FILE__;
const PREFIX = 'cldflr';
const VERSION = '1.0.2';

// Loader
require_once dirname(FILE).'/helpers/autoload.php';

// Main class
Core\Core::instance();

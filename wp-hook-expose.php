<?php
/**
 * Plugin Name:       WP Hook Expose
 * Plugin URI:        https://github.com/fabiancdng/wp-hook-expose
 * Description:       A simple and fast way to turn WordPress hooks into custom webhooks.
 * Version:           1.1.1
 * Requires at least: 6.0
 * Author:            Fabian Reinders
 * Author URI:        https://github.com/fabiancdng
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'vendor/autoload.php';

// Create a new instance of the plugin class and run it.
\WpHookExpose\Plugin::create_and_run();
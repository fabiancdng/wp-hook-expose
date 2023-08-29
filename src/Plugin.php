<?php
/** Plugin Class
 *
 * @package wp-hook-expose
 */

namespace WpHookExpose;

use WpHookExpose\Hooks\ProfileUpdate;
use WpHookExpose\Hooks\CategorySaved;
use WpHookExpose\Hooks\PostSaved;
use WpHookExpose\Hooks\UserRegister;

// If this file is accessed directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class Plugin as main plugin class.
 */
class Plugin {
	/**
	 * Create a new instance of the plugin class and run it.
	 */
	public static function create_and_run(): void {
		$plugin = new Plugin();
		$plugin->run();
	}

	/**
	 * Run the plugin (right when the main plugin file is loaded).
	 */
	public function run(): void {
		add_action( 'plugins_loaded', array( $this, 'initialize' ) );
	}

	/**
	 * Initialize the plugin's functionality by adding all hook- and filter calls.
	 */
	public function initialize(): void {
		// Register the admin settings in the WordPress dashboard.
		( new Options() )->register_options_page();


		// Register and subscribe events to their according WordPress hooks.
		( new PostSaved() )->subscribe_wp_hooks();
		( new UserRegister() )->subscribe_wp_hooks();
		( new ProfileUpdate() )->subscribe_wp_hooks();
		( new CategorySaved() )->subscribe_wp_hooks();
	}
}
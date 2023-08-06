<?php
/** Plugin Class
 *
 * @package wp-hook-expose
 */

namespace Fabiancdng\WP_Hook_Expose;

use Fabiancdng\WP_Hook_Expose\Options;
use Fabiancdng\WP_Hook_Expose\Hooks\Save_Post;

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
		// Add the plugin options page to the WordPress dashboard.
		$options = new Options();
		add_action( 'admin_menu', array( $options, 'add_options_page' ) );

		// Hook call to register the settings, settings sections and settings fields.
		add_action( 'admin_init', array( $options, 'register_settings' ) );

		// Handle the WordPress hook 'save_post' and send off the webhook request.
		$save_post_hook = new Save_Post();
		add_action( 'save_post', array( $save_post_hook, 'handle' ), 10, 3 );
	}
}
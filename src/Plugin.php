<?php
/** Plugin Class
 *
 * @package wp-hook-expose
 */

namespace WpHookExpose;

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
		// Instantiate WebhookController class (API for managing webhooks).
		$webhook_controller = new WebhookController();

		// Webhooks are stored in the WordPress options table, insure the option exists.
		$webhook_controller->create_webhook_option();

		// Instantiate EventController class and inject dependencies.
		// Register the events a webhook can listen to.
		$event_controller = new EventController();
		$event_controller->register_events();

		// Instantiate the Dashboard class and inject dependencies.
		// Initialize the dashboard page in the WordPress backend.
		$dashboard = new Dashboard( $webhook_controller );
		$dashboard->register_dashboard_pages();
	}
}
<?php
/** Plugin Class
 *
 * @package wp-hook-expose
 */

namespace WpHookExpose;

// If this file is accessed directly, abort.
use WpHookExpose\Dashboard\Dashboard;
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
use YahnisElsts\PluginUpdateChecker\v5p5\Vcs\Api;
use YahnisElsts\PluginUpdateChecker\v5p5\Vcs\GitHubApi;

defined( 'ABSPATH' ) || exit;

/**
 * Class Plugin as main plugin class.
 */
class Plugin {
	/**
	 * The WebhookController instance for managing webhooks set in the WP instance.
	 */
	private WebhookController $webhook_controller;

	/**
	 * The EventController instance for managing and binding webhooks to certain WP hooks.
	 */
	private EventController $event_controller;

	public function get_webhook_controller(): WebhookController {
		return $this->webhook_controller;
	}

	public function set_webhook_controller( WebhookController $webhook_controller ): void {
		$this->webhook_controller = $webhook_controller;
	}

	public function get_event_controller(): EventController {
		return $this->event_controller;
	}

	public function set_event_controller( EventController $event_controller ): void {
		$this->event_controller = $event_controller;
	}

	/**
	 * Creates a new instance of the plugin class and run it.
	 */
	public static function create_and_run(): void {
		$plugin = new Plugin();
		$plugin->run();
	}

	/**
	 * Runs the plugin (right when the main plugin file is loaded).
	 */
	public function run(): void {
		add_action( 'plugins_loaded', array( $this, 'initialize' ) );
	}

	/**
	 * Initializes the plugin's functionality by adding all hook- and filter calls.
	 */
	public function initialize(): void {
		$this->init_plugin_update_checker();
		$this->init_webhook_controller();
		$this->init_event_controller();
		$this->init_admin_dashboard();
	}

	/**
	 * Initialize the plugin update checker.
	 */
	private function init_plugin_update_checker(): void {
		// Initialize the plugin update checker.
		$plugin_update_checker = PucFactory::buildUpdateChecker(
			'https://github.com/fabiancdng/wp-hook-expose/',
			self::get_plugin_file(),
			'wp-hook-expose'
		);

		/**
		 * @var GitHubApi $puc_github_api
		 */
		$puc_github_api = $plugin_update_checker->getVcsApi();

		// Enable release assets for the plugin.
		$puc_github_api->enableReleaseAssets( '/.*wp-hook-expose\.zip.*/', Api::REQUIRE_RELEASE_ASSETS );
	}

	private function init_event_controller(): void {
		// Instantiate EventController class and inject dependencies.
		$event_controller = new EventController(
			$this->get_webhook_controller()
		);

		// Register the events a webhook can listen to.
		$event_controller->add_webhook_actions();

		$this->set_event_controller( $event_controller );
	}

	private function init_webhook_controller(): void {
		// Instantiate WebhookController class (API for managing webhooks).
		$webhook_controller = new WebhookController();

		// Webhooks are stored in the WordPress options table, insure the option exists.
		$webhook_controller->create_webhook_option();

		$this->set_webhook_controller( $webhook_controller );
	}

	private function init_admin_dashboard(): void {
		// Instantiate the Dashboard class and inject dependencies.
		$dashboard = new Dashboard(
			$this->get_webhook_controller()
		);

		// Initialize the dashboard page in the WordPress backend.
		$dashboard->register_dashboard_pages();
	}

	/**
	 * Get the path to the base of the plugin: /{base_to_WordPress}/wp-content/plugins/{plugin-slug}/
	 *
	 * @return string URL of plugin base.
	 */
	public static function get_plugin_base_dir(): string {
		return plugin_dir_path( __DIR__ );
	}

	/**
	 * Get the path to the plugin file: /{base_to_WordPress}/wp-content/plugins/{plugin-slug}/disable-wp-frontend.php
	 *
	 * @return string Path to plugin file.
	 */
	public static function get_plugin_file(): string {
		return self::get_plugin_base_dir() . '/wp-hook-expose.php';
	}
}
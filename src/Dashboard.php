<?php
/** Options Class
 *
 * @package wp-hook-expose
 */

namespace WpHookExpose;


// If this file is accessed directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class Options for adding a menu page in the WordPress dashboard to control the plugin.
 */
class Dashboard {
	/**
	 * WebhookController instance to manage webhooks.
	 */
	private WebhookController $webhook_controller;

	/**
	 * The constructor for the Dashboard class.
	 *
	 * @param WebhookController $webhook_controller WebhookController instance to manage webhooks.
	 */
	public function __construct( WebhookController $webhook_controller ) {
		$this->webhook_controller = $webhook_controller;
	}

	/**
	 * Calls the necessary hooks and filters to add the dashboard pages for WP Hook Expose.
	 */
	public function register_dashboard_pages(): void {
		// Hook call to add the options page.
		add_action( 'admin_menu', array( $this, 'add_dashboard_pages' ) );
	}

	/**
	 * Add the plugin dashboard pages to the WordPress dashboard.
	 */
	public function add_dashboard_pages(): void {
		// Register the dashboard in the WordPress menu.
		add_menu_page(
			__( 'WP Hook Expose', 'wp-hook-expose' ),
			__( 'WP Hook Expose', 'wp-hook-expose' ),
			'manage_options',
			'wp-hook-expose',
			function () {
				$this->render_dashboard( 'index' );
			},
			'dashicons-admin-plugins',
		);

		// Register dashboard page for viewing all webhooks.
		add_submenu_page(
			'wp-hook-expose',
			__( 'Webhooks', 'wp-hook-expose' ),
			__( 'Webhooks', 'wp-hook-expose' ),
			'manage_options',
			'wp-hook-expose',
			function () {
				$this->render_dashboard( 'webhooks' );
			}
		);

		// Register dashboard page for creating a new webhook.
		add_submenu_page(
			'wp-hook-expose',
			__( 'Add New', 'wp-hook-expose' ),
			__( 'Add New', 'wp-hook-expose' ),
			'manage_options',
			'wp-hook-expose-add-webhook',
			function () {
				$this->render_dashboard( 'add-webhook' );
			}
		);

		// Register dashboard page for plugin options.
		add_submenu_page(
			'wp-hook-expose',
			__( 'Options', 'wp-hook-expose' ),
			__( 'Options', 'wp-hook-expose' ),
			'manage_options',
			'wp-hook-expose-options',
			function () {
				$this->render_dashboard( 'options' );
			}
		);
	}

	/**
	 * Render the options page in the WordPress dashboard.
	 *
	 * @param string $context The context of the dashboard page to render.
	 */
	public function render_dashboard( string $context ): void {
		if ( 'webhooks' === $context ) {
			$webhook_list_table = new WebhookListTable( $webhook_controller );
			$webhook_list_table->prepare_items();
			$webhook_list_table->display();

			return;
		}

		if ( 'add-webhook' === $context ) {
			?>
            <div class="wrap">
                <h1><?php esc_html_e( 'Add New Webhook', 'wp-hook-expose' ); ?></h1>
                <form method="post" action="options.php">
					<?php
					settings_fields( 'wp_hook_expose' );
					do_settings_sections( 'wp_hook_expose' );
					submit_button( __( 'Save Webhook', 'wp-hook-expose' ) );
					?>
                </form>
            </div>
			<?php
			return;
		}

		?>
        <div class="wrap">
            <h1><?php esc_html_e( 'WP Hook Expose', 'wp-hook-expose' ); ?></h1>
        </div>
		<?php
	}
}

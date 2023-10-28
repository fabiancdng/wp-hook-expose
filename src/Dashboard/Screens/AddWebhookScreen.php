<?php
/** AddWebhookScreen Class
 *
 * @package wp-hook-expose
 */

namespace WpHookExpose\Dashboard\Screens;

// If this file is accessed directly, abort.
use WpHookExpose\WebhookController;

defined( 'ABSPATH' ) || exit;

/**
 * Class AddWebhookScreen for displaying the add webhook screen in the WordPress dashboard.
 */
class AddWebhookScreen extends Screen {

	/**
	 * Registers the settings section and fields for the plugin options.
	 */
	public function register(): void {
		// Add the page for the add webhook screen in the dashboard.
		add_submenu_page(
			'wp-hook-expose',
			__( 'Add New', 'wp-hook-expose' ),
			__( 'Add New', 'wp-hook-expose' ),
			'manage_options',
			'wp-hook-expose-add-webhook',
			array( $this, 'render' ),
		);

		// Register the settings section.
		add_settings_section(
			'wp_hook_expose_add_webhook',
			__( '', 'wp-hook-expose' ),
			function () {
				ob_start();
				?>
                <p><?php esc_html_e( 'Add a new webhook by providing a WordPress hook as an event.', 'wp-hook-expose' ); ?></p>
                <p><?php esc_html_e( 'The JSON-serialized payload of the hook will be sent to the URL you specify', 'wp-hook-expose' ); ?></p>
				<?php
				echo ob_get_clean();
			},
			'wp-hook-expose-add-webhook'
		);

		// Register the webhook name field.
		add_settings_field(
			'wp_hook_expose_webhook_name',
			__( 'Webhook Name', 'wp-hook-expose' ),
			function () {
				echo '<input type="text" name="wp_hook_expose[webhook_name]" id="wp_hook_expose_webhook_name" class="regular-text" />';
			},
			'wp-hook-expose-add-webhook',
			'wp_hook_expose_add_webhook'
		);

		// Register the webhook event field.
		add_settings_field(
			'wp_hook_expose_webhook_event',
			__( 'Webhook Event', 'wp-hook-expose' ),
			function () {
				echo '<input type="text" name="wp_hook_expose[webhook_event]" id="wp_hook_expose_webhook_event" class="regular-text" />';
			},
			'wp-hook-expose-add-webhook',
			'wp_hook_expose_add_webhook'
		);

		// Register the webhook URL field.
		add_settings_field(
			'wp_hook_expose_webhook_url',
			__( 'Webhook URL', 'wp-hook-expose' ),
			function () {
				echo '<input type="text" name="wp_hook_expose[webhook_url]" id="wp_hook_expose_webhook_url" class="regular-text" />';
			},
			'wp-hook-expose-add-webhook',
			'wp_hook_expose_add_webhook'
		);
	}

	/**
	 * Renders the add webhook screen.
	 */
	public function render(): void {
		// Handle the form submission.
		if ( isset( $_POST['wp_hook_expose'] ) ) {
			$webhook = $_POST['wp_hook_expose'];
			$this->webhook_controller->add_or_update_webhook(
				sanitize_title( $webhook['webhook_name'] ),
				$webhook['webhook_name'],
				$webhook['webhook_event'],
				$webhook['webhook_url'],
			);
		}
		?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Add New Webhook', 'wp-hook-expose' ); ?></h1>
            <form method="post">
				<?php
				settings_fields( 'wp_hook_expose_add_webhook' );
				do_settings_sections( 'wp-hook-expose-add-webhook' );
				submit_button( __( 'Save Webhook', 'wp-hook-expose' ) );
				?>
            </form>
        </div>
		<?php
	}
}

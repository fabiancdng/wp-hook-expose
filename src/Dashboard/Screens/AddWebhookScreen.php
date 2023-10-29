<?php
/** AddWebhookScreen Class
 *
 * @package wp-hook-expose
 */

namespace WpHookExpose\Dashboard\Screens;

// If this file is accessed directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class AddWebhookScreen for displaying the add webhook screen in the WordPress dashboard.
 */
class AddWebhookScreen extends Screen {

	/**
	 * Registers the settings section and fields for the plugin options.
	 */
	public function register(): void {
		// Handle the form submission.
		if ( isset( $_POST['wp_hook_expose_add_webhook'] ) ) {
			$webhook = $_POST['wp_hook_expose_add_webhook'];

			// Add the webhook.
			$status = $this->webhook_controller->add_webhook(
				sanitize_title( $webhook['webhook_name'] ),
				$webhook['webhook_name'],
				$webhook['webhook_event'],
				$webhook['webhook_url'],
			);

			// Redirect to the main dashboard page if the webhook was added successfully and display a success message.
			if ( $status ) {
				wp_redirect( admin_url( 'admin.php?page=wp-hook-expose&action=webhook-added' ) );
			} else {
				// Show an error message if the webhook could not be added.
				add_settings_error(
					'wp_hook_expose_add_webhook',
					'webhook-add-error',
					__( 'The webhook could not be added.', 'wp-hook-expose' ),
					'error'
				);
			}
		}

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
				?>
                <p><?php esc_html_e( 'Add a new webhook by providing a WordPress hook as an event.', 'wp-hook-expose' ); ?></p>
                <p><?php esc_html_e( 'All arguments passed to the hook will be sent as a JSON body to the URL you specify.', 'wp-hook-expose' ); ?></p>
				<?php
			},
			'wp-hook-expose-add-webhook'
		);

		// Register the webhook name field.
		add_settings_field(
			'wp_hook_expose_webhook_name',
			__( 'Webhook Name', 'wp-hook-expose' ),
			function () {
				?>
                <input type="text" name="wp_hook_expose_add_webhook[webhook_name]" id="wp_hook_expose_webhook_name" class="regular-text"/>
                <p class="description"><?php esc_html_e( 'The name of the webhook.', 'wp-hook-expose' ); ?></p>
				<?php
			},
			'wp-hook-expose-add-webhook',
			'wp_hook_expose_add_webhook'
		);

		// Register the webhook event field.
		add_settings_field(
			'wp_hook_expose_webhook_event',
			__( 'Webhook Event', 'wp-hook-expose' ),
			function () {
				?>
                <input type="text" name="wp_hook_expose_add_webhook[webhook_event]" id="wp_hook_expose_webhook_event" class="regular-text"/>
                <p class="description"><?php echo __( 'The WordPress hook the webhook binds to (for example <code>save_post</code>, <code>profile_update</code>).', 'wp-hook-expose' ); ?></p>
                <p class="description"><?php echo __( 'See the <a href="https://developer.wordpress.org/reference/hooks/" target="_blank">WordPress documentation</a> for a list of available hooks.', 'wp-hook-expose' ); ?></p>
                <p class="description"><?php esc_html_e( 'Third-party hooks are supported as well.', 'wp-hook-expose' ); ?></p>
				<?php
			},
			'wp-hook-expose-add-webhook',
			'wp_hook_expose_add_webhook'
		);

		// Register the webhook URL field.
		add_settings_field(
			'wp_hook_expose_webhook_url',
			__( 'Webhook URL', 'wp-hook-expose' ),
			function () {
				?>
                <input type="text" name="wp_hook_expose_add_webhook[webhook_url]" id="wp_hook_expose_webhook_url" class="regular-text"/>
                <p class="description"><?php esc_html_e( 'The URL to send a request to if the hook is fired (with the hook\'s arguments as a body).', 'wp-hook-expose' ); ?></p>
				<?php
			},
			'wp-hook-expose-add-webhook',
			'wp_hook_expose_add_webhook'
		);
	}

	/**
	 * Renders the add webhook screen.
	 */
	public function render(): void {
		?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Add New Webhook', 'wp-hook-expose' ); ?></h1>
            <form method="post">
				<?php
				settings_errors();
				settings_fields( 'wp_hook_expose_add_webhook' );
				do_settings_sections( 'wp-hook-expose-add-webhook' );
				submit_button( __( 'Save Webhook', 'wp-hook-expose' ) );
				?>
            </form>
        </div>
		<?php
	}
}

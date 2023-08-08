<?php
/** Options Class
 *
 * @package wp-hook-expose
 */

namespace Fabiancdng\WP_Hook_Expose;


// If this file is accessed directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class Options for adding a menu page in the WordPress dashboard to control the plugin.
 */
class Options {
	/**
	 * Add the plugin options page to the WordPress dashboard.
	 */
	public function add_options_page(): void {
		add_menu_page(
			__( 'WP Hook Expose', 'wp-hook-expose' ),
			__( 'WP Hook Expose', 'wp-hook-expose' ),
			'manage_options',
			'wp-hook-expose',
			array( $this, 'render_options_page' ),
			'dashicons-admin-plugins',
		);
	}

	/**
	 * Registers the settings, settings sections and settings fields.
	 */
	public function register_settings(): void {
		register_setting( 'wp_hook_expose', 'wp_hook_expose' );

		// Add a settings section for the "Post Saved" event.
		$this->register_section_event_webhook_post_saved();
	}

	/**
	 * Render the plugin options page.
	 */
	public function render_options_page(): void {
		?>
        <style>
            /* Line above the settings section's title to separate it from the previous section. */
            .wp-hook-expose-options h2 {
                border-top: 1px solid #ccc;
                padding-top: 25px;
            }
        </style>

        <div class="wrap wp-hook-expose-options">
            <h1><?php esc_html_e( 'WP Hook Expose', 'wp-hook-expose' ); ?></h1>
            <p><?php esc_html_e( 'This is the options page for the WP Hook Expose plugin.', 'wp-hook-expose' ); ?></p>
            <p><?php esc_html_e( 'In this section, you can map a given WordPress event to a URL.', 'wp-hook-expose' ); ?></p>

            <form method="post" action="options.php">
				<?php
				settings_fields( 'wp_hook_expose' );
				do_settings_sections( 'wp_hook_expose' );
				submit_button();
				?>
            </form>
        </div>
		<?php
	}

	/**
	 * Register the settings section and fields for the "Post Saved" event.
	 */
	public function register_section_event_webhook_post_saved(): void {
		// Register the settings section.
		add_settings_section(
			'wp_hook_expose_event_webhook_post_saved',
			__( 'Post Saved Webhook', 'wp-hook-expose' ),
			array( $this, 'render_section_event_webhook_post_saved' ),
			'wp_hook_expose'
		);

		// Register the settings field for the "Post Saved" webhook URL.
		add_settings_field(
			'wp_hook_expose_event_webhook_post_saved_url',
			__( 'Webhook URL', 'wp-hook-expose' ),
			array( $this, 'render_field_event_webhook_post_saved_url' ),
			'wp_hook_expose',
			'wp_hook_expose_event_webhook_post_saved'
		);

		// Register the settings field for the "Post Saved" webhook body.
		add_settings_field(
			'wp_hook_expose_event_webhook_post_saved_body',
			__( 'Webhook Request Body', 'wp-hook-expose' ),
			array( $this, 'render_field_event_webhook_post_saved_body' ),
			'wp_hook_expose',
			'wp_hook_expose_event_webhook_post_saved'
		);
	}

	/**
	 * Render the settings section for the "Post Saved" event.
	 */
	public function render_section_event_webhook_post_saved(): void {
		?>
        <p><?php esc_html_e( 'This section allows you to map the "Post Saved" event to a URL.', 'wp-hook-expose' ); ?></p>
        <p><?php esc_html_e( 'The URL will be registered as a webhook and POST requests will be sent to it as soon as the event occurs.', 'wp-hook-expose' ); ?></p>
		<?php
	}

	/**
	 * Render the settings field for the "Post Saved" webhook URL.
	 */
	public function render_field_event_webhook_post_saved_url(): void {
		$options = get_option( 'wp_hook_expose' );
		?>
        <input type="text" name="wp_hook_expose[event_webhook_post_saved][url]"
               value="<?php echo esc_attr( $options['event_webhook_post_saved']['url'] ?? '' ); ?>">
        <p class="description"><?php esc_html_e( 'The URL to which the POST request will be sent.', 'wp-hook-expose' ); ?></p>
		<?php
	}

	/**
	 * Render the settings field for the "Post Saved" webhook body.
	 */
	public function render_field_event_webhook_post_saved_body(): void {
		$options = get_option( 'wp_hook_expose' );
		?>
        <textarea name="wp_hook_expose[event_webhook_post_saved][body]" rows="10"
                  cols="50"><?php echo esc_textarea( $options['event_webhook_post_saved']['body'] ?? '' ); ?></textarea>
        <p class="description">
			<?php esc_html_e( 'The JSON body of the POST request.', 'wp-hook-expose' ); ?>
            <br>
			<?php esc_html_e( 'Will be merged with hook-specific data fields.', 'wp-hook-expose' ); ?>
        </p>
		<?php
	}
}
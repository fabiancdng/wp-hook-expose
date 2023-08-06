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

		// Add a settings section for the "Post Created" event.
		$this->register_section_event_webhook_post_created();

		// Add a settings section for the "Post Updated" event.
		$this->register_section_event_webhook_post_updated();
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
	 * Register the settings section and fields for the "Post Created" event.
	 */
	public function register_section_event_webhook_post_created(): void {
		// Register the settings section.
		add_settings_section(
			'wp_hook_expose_event_webhook_post_created',
			__( 'Post Created Webhook', 'wp-hook-expose' ),
			array( $this, 'render_section_event_webhook_post_created' ),
			'wp_hook_expose'
		);

		// Register the settings field for the "Post Created" webhook URL.
		add_settings_field(
			'wp_hook_expose_event_webhook_post_created_url',
			__( 'Webhook URL', 'wp-hook-expose' ),
			array( $this, 'render_field_event_webhook_post_created_url' ),
			'wp_hook_expose',
			'wp_hook_expose_event_webhook_post_created'
		);

		// Register the settings field for the "Post Created" webhook body.
		add_settings_field(
			'wp_hook_expose_event_webhook_post_created_body',
			__( 'Webhook Request Body', 'wp-hook-expose' ),
			array( $this, 'render_field_event_webhook_post_created_body' ),
			'wp_hook_expose',
			'wp_hook_expose_event_webhook_post_created'
		);
	}

	/**
	 * Register the settings section and fields for the "Post Updated" event.
	 */
	public function register_section_event_webhook_post_updated(): void {
		// Register the settings section.
		add_settings_section(
			'wp_hook_expose_event_webhook_post_updated',
			__( 'Post Updated Webhook', 'wp-hook-expose' ),
			array( $this, 'render_section_event_webhook_post_updated' ),
			'wp_hook_expose'
		);

		// Register the settings field for the "Post Updated" webhook URL.
		add_settings_field(
			'wp_hook_expose_event_webhook_post_updated_url',
			__( 'Webhook URL', 'wp-hook-expose' ),
			array( $this, 'render_field_event_webhook_post_updated_url' ),
			'wp_hook_expose',
			'wp_hook_expose_event_webhook_post_updated'
		);

		// Register the settings field for the "Post Updated" webhook body.
		add_settings_field(
			'wp_hook_expose_event_webhook_post_updated_body',
			__( 'Webhook Request Body', 'wp-hook-expose' ),
			array( $this, 'render_field_event_webhook_post_updated_body' ),
			'wp_hook_expose',
			'wp_hook_expose_event_webhook_post_updated'
		);
	}

	/**
	 * Render the settings section for the "Post Created" event.
	 */
	public function render_section_event_webhook_post_created(): void {
		?>
        <p><?php esc_html_e( 'This section allows you to map the "Post Created" event to a URL.', 'wp-hook-expose' ); ?></p>
        <p><?php esc_html_e( 'The URL will be registered as a webhook and POST requests will be sent to it as soon as the event occurs.', 'wp-hook-expose' ); ?></p>
		<?php
	}

	/**
	 * Render the settings section for the "Post Updated" event.
	 */
	public function render_section_event_webhook_post_updated(): void {
		?>
        <p><?php esc_html_e( 'This section allows you to map the "Post Updated" event to a URL.', 'wp-hook-expose' ); ?></p>
        <p><?php esc_html_e( 'The URL will be registered as a webhook and POST requests will be sent to it as soon as the event occurs.', 'wp-hook-expose' ); ?></p>
		<?php
	}

	/**
	 * Render the settings field for the "Post Created" webhook URL.
	 */
	public function render_field_event_webhook_post_created_url(): void {
		$options = get_option( 'wp_hook_expose' );
		?>
        <input type="text" name="wp_hook_expose[event_webhook_post_created][url]"
               value="<?php echo esc_attr( $options['event_webhook_post_created']['url'] ?? '' ); ?>">
        <p class="description"><?php esc_html_e( 'The URL to which the POST request will be sent.', 'wp-hook-expose' ); ?></p>
		<?php
	}

	/**
	 * Render the settings field for the "Post Created" webhook body.
	 */
	public function render_field_event_webhook_post_created_body(): void {
		$options = get_option( 'wp_hook_expose' );
		?>
        <textarea name="wp_hook_expose[event_webhook_post_created][body]" rows="10"
                  cols="50"><?php echo esc_textarea( $options['event_webhook_post_created']['body'] ?? '' ); ?></textarea>
        <p class="description">
			<?php esc_html_e( 'The JSON body of the POST request.', 'wp-hook-expose' ); ?>
            <br>
			<?php esc_html_e( 'Will be merged with hook-specific data fields.', 'wp-hook-expose' ); ?>
        </p>
		<?php
	}

	/**
	 * Render the settings field for the "Post Updated" webhook URL.
	 */
	public function render_field_event_webhook_post_updated_url(): void {
		$options = get_option( 'wp_hook_expose' );
		?>
        <input type="text" name="wp_hook_expose[event_webhook_post_updated][url]"
               value="<?php echo esc_attr( $options['event_webhook_post_updated']['url'] ?? '' ); ?>">
        <p class="description"><?php esc_html_e( 'The URL to which the POST request will be sent.', 'wp-hook-expose' ); ?></p>
		<?php
	}

	/**
	 * Render the settings field for the "Post Updated" webhook body.
	 */
	public function render_field_event_webhook_post_updated_body(): void {
		$options = get_option( 'wp_hook_expose' );
		?>
        <textarea name="wp_hook_expose[event_webhook_post_updated][body]" rows="10"
                  cols="50"><?php echo esc_textarea( $options['event_webhook_post_updated']['body'] ?? '' ); ?></textarea>
        <p class="description">
			<?php esc_html_e( 'The JSON body of the POST request.', 'wp-hook-expose' ); ?>
            <br>
			<?php esc_html_e( 'Will be merged with hook-specific data fields.', 'wp-hook-expose' ); ?>
        </p>
		<?php
	}
}
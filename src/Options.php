<?php
/** Options Class
 *
 * @package wp-hook-expose
 */

namespace Fabiancdng\WpHookExpose;


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
		$this->register_section_event_webhook( 'Post Created', 'post_saved' );

		// Add a setting section for the "Post Updated" event.
		$this->register_section_event_webhook( 'Post Updated', 'post_updated' );

		// Add a settings section for the "User Registered" event.
		$this->register_section_event_webhook( 'User Registered', 'user_registered' );

		// Add a settings section for the "User Profile Updated" event.
		$this->register_section_event_webhook( 'User Profile Updated', 'user_profile_updated' );
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

	public function register_section_event_webhook( string $event_title, string $event_slug ): void {
		// Register the settings section.
		add_settings_section(
			'wp_hook_expose_event_webhook_' . $event_slug,
			$event_title . ' Webhook',
			function () use ( $event_title, $event_slug ) {
				$this->render_section_event_webhook( $event_title, $event_slug );
			},
			'wp_hook_expose'
		);

		// Register the settings field for the "Post Saved" webhook URL.
		add_settings_field(
			'wp_hook_expose_event_webhook_' . $event_slug . '_url',
			__( 'Webhook URL', 'wp-hook-expose' ),
			function () use ( $event_title, $event_slug ) {
				$this->render_field_event_webhook_url( $event_title, $event_slug );
			},
			'wp_hook_expose',
			'wp_hook_expose_event_webhook_' . $event_slug
		);

		// Register the settings field for the "Post Saved" webhook body.
		add_settings_field(
			'wp_hook_expose_event_webhook_' . $event_slug . '_body',
			__( 'Webhook Request Body', 'wp-hook-expose' ),
			function () use ( $event_title, $event_slug ) {
				$this->render_field_event_webhook_body( $event_title, $event_slug );
			},
			'wp_hook_expose',
			'wp_hook_expose_event_webhook_' . $event_slug
		);
	}

	/**
	 * Render the settings section for the "Post Saved" event.
	 */
	public function render_section_event_webhook( string $event_title, string $event_slug ): void {
		?>
        <p>
			<?php
			sprintf(
				esc_html__( 'This section allows you to map the "%s" event to a URL.', 'wp-hook-expose' ),
				$event_title
			);
			?>
        </p>
        <p><?php esc_html_e( 'The URL will be registered as a webhook and POST requests will be sent to it as soon as the event occurs.', 'wp-hook-expose' ); ?></p>
		<?php
	}

	/**
	 * Render the settings field for the event webhook's URL.
	 */
	public function render_field_event_webhook_url( string $event_title, string $event_slug ): void {
		$options = get_option( 'wp_hook_expose' );
		?>
        <input type="text" name="wp_hook_expose[event_webhooks][<?php echo esc_attr( $event_slug ); ?>][url]"
               value="<?php echo esc_attr( $options['event_webhooks'][ $event_slug ]['url'] ?? '' ); ?>">
        <p class="description"><?php esc_html_e( 'The URL to which the POST request will be sent.', 'wp-hook-expose' ); ?></p>
		<?php
	}

	/**
	 * Render the settings field for the event webhook's body.
	 */
	public function render_field_event_webhook_body( string $event_title, string $event_slug ): void {
		$options = get_option( 'wp_hook_expose' );
		?>
        <textarea name="wp_hook_expose[event_webhooks][<?php echo esc_attr( $event_slug ); ?>][body]" rows="10"
                  cols="50"><?php echo esc_textarea( $options['event_webhooks'][ $event_slug ]['body'] ?? '' ); ?></textarea>
        <p class="description">
			<?php esc_html_e( 'The JSON body of the POST request.', 'wp-hook-expose' ); ?>
            <br>
			<?php esc_html_e( 'Will be merged with hook-specific data fields.', 'wp-hook-expose' ); ?>
        </p>
		<?php
	}
}
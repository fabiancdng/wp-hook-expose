<?php
/** Settings Class
 *
 * @package wp-hook-expose
 */

namespace WpHookExpose\Dashboard\Screens;

// If this file is accessed directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class Options for adding a menu page in the WordPress dashboard to control the plugin.
 */
class SettingsScreen extends Screen {
	/**
	 * Registers the settings section and fields for the plugin options.
	 */
	public function register(): void {
		add_submenu_page(
			'wp-hook-expose',
			__( 'Settings', 'wp-hook-expose' ),
			__( 'Settings', 'wp-hook-expose' ),
			'manage_options',
			'wp-hook-expose-settings',
			array( $this, 'render' )
		);

		// Register the settings section.
		add_settings_section(
			'wp_hook_expose_settings',
			__( '', 'wp-hook-expose' ),
			function () {
				echo '<p>' . __( 'Configure the settings for WP Hook Expose.', 'wp-hook-expose' ) . '</p>';
			},
			'wp-hook-expose-settings'
		);

		// Register the settings fields.
		register_setting( 'wp_hook_expose_settings', 'wp_hook_expose_settings' );

		// Register settings section for general webhook settings.
		add_settings_section(
			'wp_hook_expose_settings_authorization',
			__( 'Authorization', 'wp-hook-expose' ),
			function () {
				?>
                <p><?php esc_html_e( 'Configure the authorization settings for WP Hook Expose.', 'wp-hook-expose' ); ?></p>
				<?php
			},
			'wp-hook-expose-settings'
		);

		// Register the webhook secret settings field.
		add_settings_field(
			'wp_hook_expose_settings_webhook_secret',
			__( 'Webhook Secret', 'wp-hook-expose' ),
			function () {
				$webhook_secret = get_option( 'wp_hook_expose_settings' )['authorization']['webhook_secret'];
				?>
                <input type="text" name="wp_hook_expose_settings[authorization][webhook_secret]" value="<?php echo esc_attr( $webhook_secret ); ?>"/>
                <p class="description"><?php esc_html_e( 'The secret that is used to authenticate webhook requests.', 'wp-hook-expose' ); ?></p>
                <p class="description"><?php echo __( 'The webhook secret is appended to the JSON body of every webhook request as the field <code>wp_webhook_secret</code>.', 'wp-hook-expose' ); ?></p>
				<?php
			},
			'wp-hook-expose-settings',
			'wp_hook_expose_settings_authorization'
		);

		// Register settings section for logging settings.
		add_settings_section(
			'wp_hook_expose_settings_logging',
			__( 'Logging', 'wp-hook-expose' ),
			function () {
				?>
                <p><?php esc_html_e( 'Configure the logging settings for WP Hook Expose.', 'wp-hook-expose' ) ?></p>
				<?php
			},
			'wp-hook-expose-settings'
		);

		// Register the debug log settings field.
		add_settings_field(
			'wp_hook_expose_settings_debug_log',
			__( 'Debug Log', 'wp-hook-expose' ),
			function () {
				$debug_log = get_option( 'wp_hook_expose_settings' )['logging']['debug_log'] ?? '0';
				?>
                <label>
                    <input type="checkbox" name="wp_hook_expose_settings[logging][debug_log]" value="1" <?php checked( $debug_log, 1 ); ?>/>
					<?php esc_html_e( 'Enable debug logging.', 'wp-hook-expose' ); ?>
                </label>
                <p class="description"><?php echo __( 'Checking the box above will enable logs to the <a href="https://wordpress.org/documentation/article/debugging-in-wordpress/#wp_debug_log" target="_blank">WordPress debug log</a> for every outgoing request.', 'wp-hook-expose' ); ?></p>
				<?php
			},
			'wp-hook-expose-settings',
			'wp_hook_expose_settings_logging'
		);

		// Register setting for selecting what fields are saved in the 'last_execution' field of a webhook.
		add_settings_field(
			'wp_hook_expose_settings_retain_last_execution_data',
			__( 'Retain last execution data', 'wp-hook-expose' ),
			function () {
				$retain_last_execution_data = get_option( 'wp_hook_expose_settings' )['logging']['retain_last_execution_data'] ?? array( 'timestamp', 'response_status_code' );
				?>
                <ul>
                    <li>
                        <label>
                            <input type="checkbox" name="wp_hook_expose_settings[logging][retain_last_execution_data][]"
                                   value="timestamp" <?php checked( in_array( 'timestamp', $retain_last_execution_data ) ); ?>/>
							<?php esc_html_e( 'Store the timestamp of the last execution.', 'wp-hook-expose' ); ?>
                        </label>
                    </li>

                    <li>
                        <label>
                            <input type="checkbox" name="wp_hook_expose_settings[logging][retain_last_execution_data][]"
                                   value="response_status_code" <?php checked( in_array( 'response_status_code', $retain_last_execution_data ) ); ?>/>
							<?php esc_html_e( 'Store the response status code of the last execution.', 'wp-hook-expose' ); ?>
                        </label>
                    </li>
                </ul>
                <p class="description"><?php echo __( 'Select which fields should be saved in the <code>last_execution</code> field of a webhook.', 'wp-hook-expose' ); ?></p>
				<?php
			},
			'wp-hook-expose-settings',
			'wp_hook_expose_settings_logging'
		);


	}

	/**
	 * Renders the settings page in the dashboard.
	 */
	public function render(): void {
		?>
        <style>
            #wp-hook-expose-settings-screen form h2 {
                border-top: 1px solid #dcdc;
                padding-top: 25px;
            }
        </style>
        <div class="wrap" id="wp-hook-expose-settings-screen">
            <h1><?php esc_html_e( 'WP Hook Expose Settings', 'wp-hook-expose' ); ?></h1>

			<?php settings_errors(); ?>

            <form action="options.php" method="post">
				<?php
				settings_fields( 'wp_hook_expose_settings' );
				do_settings_sections( 'wp-hook-expose-settings' );
				submit_button();
				?>
            </form>
        </div>
		<?php
	}
}

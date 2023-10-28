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
			__( 'WP Hook Expose Settings', 'wp-hook-expose' ),
			function () {
				echo '<p>' . __( 'Configure the settings for WP Hook Expose.', 'wp-hook-expose' ) . '</p>';
			},
			'wp-hook-expose-settings'
		);
	}

	/**
	 * Renders the settings page in the dashboard.
	 */
	public function render(): void {
		?>
        <div class="wrap">
            <h1><?php esc_html_e( 'WP Hook Expose Settings', 'wp-hook-expose' ); ?></h1>
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

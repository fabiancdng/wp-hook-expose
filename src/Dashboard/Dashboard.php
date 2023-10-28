<?php
/** Dashboard Class
 *
 * @package wp-hook-expose
 */

namespace WpHookExpose\Dashboard;

use WpHookExpose\Dashboard\Screens\AddWebhookScreen;
use WpHookExpose\Dashboard\Screens\SettingsScreen;
use WpHookExpose\WebhookController;

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
	 * WebhookListTable instance to display webhooks in the dashboard.
	 */
	private WebhookListTable $webhook_list_table;

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
	 * Adds the plugin dashboard pages to the WordPress dashboard.
	 */
	public function add_dashboard_pages(): void {
		// Register the dashboard in the WordPress menu.
		add_menu_page(
			__( 'WP Hook Expose', 'wp-hook-expose' ),
			__( 'WP Hook Expose', 'wp-hook-expose' ),
			'manage_options',
			'wp-hook-expose',
			array( $this, 'render_main_dashboard_page' ),
			'dashicons-admin-plugins',
		);

		// Register main/default dashboard page for viewing all webhooks.
		$page_hook_suffix = add_submenu_page(
			'wp-hook-expose',
			__( 'Webhooks', 'wp-hook-expose' ),
			__( 'Webhooks', 'wp-hook-expose' ),
			'manage_options',
			'wp-hook-expose',
			array( $this, 'render_main_dashboard_page' ),
		);

		// Add hook to initialize WP_List_Table.
		add_action( "load-$page_hook_suffix", array( $this, 'initialize_webhook_list_table' ) );

		// Register dashboard page for creating a new webhook.
		$add_webhook_screen = new AddWebhookScreen( $this->webhook_controller );
		$add_webhook_screen->register();

		// Register dashboard page for plugin options.
		$settings_screen = new SettingsScreen( $this->webhook_controller );
		$settings_screen->register();
	}

	/**
	 * Initializes the WP_List_Table for displaying webhooks in the dashboard.
	 */
	public function initialize_webhook_list_table(): void {
		$this->webhook_list_table = new WebhookListTable( $this->webhook_controller );
	}

	/**
	 * Renders the homepage of the dashboard.
	 */
	public function render_main_dashboard_page(): void {
		?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php esc_html_e( 'Webhooks', 'wp-hook-expose' ); ?></h1>
            <a class="page-title-action" href="<?php echo esc_url( admin_url( 'admin.php?page=wp-hook-expose-add-webhook' ) ); ?>">
				<?php esc_html_e( 'Add New', 'wp-hook-expose' ); ?>
            </a>
            <hr class="wp-header-end">

            <p><?php esc_html_e( 'Here, you can manage all of your webhooks and view information about their last execution.', 'wp-hook-expose' ); ?></p>
			<?php
			$this->webhook_list_table->prepare_items();
			$this->webhook_list_table->display();
			?>
        </div>
		<?php
	}
}

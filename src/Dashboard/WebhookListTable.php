<?php
/** WebhookListTable Class
 *
 * @package wp-hook-expose
 */

namespace WpHookExpose\Dashboard;

// If this file is accessed directly, abort.
use WpHookExpose\WebhookController;

defined( 'ABSPATH' ) || exit;

/**
 * Class WebhookListTable for displaying a list of all webhooks in the WordPress dashboard.
 */
class WebhookListTable extends \WP_List_Table {
	/**
	 * WebhookController instance to manage webhooks.
	 */
	private WebhookController $webhook_controller;

	/**
	 * The constructor for the WebhookListTable class.
	 */
	public function __construct( WebhookController $webhook_controller ) {
		parent::__construct(
			array(
				'singular' => __( 'Webhook', 'wp-hook-expose' ),
				'plural'   => __( 'Webhooks', 'wp-hook-expose' ),
				'ajax'     => false,
			)
		);

		$this->webhook_controller = $webhook_controller;
	}

	/**
	 * Returns the table data.
	 *
	 * @return array $table_data The table data.
	 */
	public function table_data(): array {
		$table_data = array();

		// Get all webhooks from the WordPress options table.
		$webhooks = $this->webhook_controller->get_webhooks();

		// Loop through all webhooks and add them to the table data.
		foreach ( $webhooks as $webhook_slug => $webhook ) {
			$table_data[] = array(
				'name'             => '<b style="font-size: larger;">' . $webhook['name'] . '</b>',
				'slug'             => $webhook_slug,
				'event'            => '<code>' . $webhook['event'] . '</code>',
				'url'              => $webhook['url'],
				'last_executed_at' => $webhook['last_execution'] ? $webhook['last_execution']['timestamp'] . ' (<code>' . $webhook['last_execution']['response_status_code'] . '</code>)' : 'Never',
				'created_at'       => $webhook['created_at'],
			);
		}

		return $table_data;
	}

	/**
	 * Overrides the parent columns method. Defines the columns to use in your listing table.
	 *
	 * @return array $columns Array of columns.
	 */
	public function get_columns(): array {
		return array(
			'name'             => __( 'Name', 'wp-hook-expose' ),
			'event'            => __( 'Event', 'wp-hook-expose' ),
			'url'              => __( 'URL', 'wp-hook-expose' ),
			'last_executed_at' => __( 'Last Executed At', 'wp-hook-expose' ),
			'created_at'       => __( 'Created At', 'wp-hook-expose' ),
		);
	}

	/**
	 * Renders the column content.
	 *
	 * @param array  $item        The data of the table row.
	 * @param string $column_name The column name.
	 */
	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Renders the contents of the name column (with edit and delete action links).
	 *
	 * @param array $item The data of the table row.
	 */
	public function column_name( $item ): string {
		$actions = array(
			// 'edit'   => sprintf( '<a href="?page=%s&action=%s&webhook_slug=%s">Edit</a>', $_REQUEST['page'], 'edit', $item['slug'] ),
			'delete' => sprintf( '<a href="?page=%s&action=%s&webhook_slug=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['slug'] ),
		);

		return sprintf( '%1$s %2$s', $item['name'], $this->row_actions( $actions ) );
	}

	/**
	 * Handles actions for the table instructed via GET or POST and a page reload.
	 */
	public function handle_table_actions(): void {
		// Handle the delete action.
		if ( isset( $_GET['action'] ) ) {
			switch ( $_GET['action'] ) {
				case 'delete':
					// Delete the webhook with the specified slug.
					$status = $this->webhook_controller->delete_webhook( $_GET['webhook_slug'] );

					if ( $status ) {
						?>
                        <div class="notice notice-success is-dismissible">
                            <p><?php esc_html_e( 'Webhook deleted successfully.', 'wp-hook-expose' ); ?></p>
                        </div>
						<?php
					} else {
						?>
                        <div class="notice notice-error is-dismissible">
                            <p><?php esc_html_e( 'An error occurred while deleting the webhook.', 'wp-hook-expose' ); ?></p>
                        </div>
						<?php
					}
					break;

				case 'webhook-added':
					// A webhook was added and the user redirected here. Display a success message.
					?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php esc_html_e( 'Webhook added successfully.', 'wp-hook-expose' ); ?></p>
                    </div>
				<?php
			}
		}
	}

	/**
	 * Overrides the parent prepare_items method. Prepares the list of items for displaying.
	 */
	public function prepare_items(): void {
		// Handle table actions before loading the table.
		$this->handle_table_actions();

		// Define the columns to use in your listing table.
		$columns          = $this->get_columns();
		$hidden_columns   = array();
		$sortable_columns = array();

		$this->_column_headers = array(
			$columns,
			$hidden_columns,
			$sortable_columns,
		);

		$this->items = $this->table_data();
	}
}

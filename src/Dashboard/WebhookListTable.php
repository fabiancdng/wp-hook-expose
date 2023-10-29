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
				'name'           => $webhook['name'],
				'slug'           => $webhook_slug,
				'event'          => $webhook['event'],
				'url'            => $webhook['url'],
				'last_execution' => $webhook['last_execution'] ?? array(),
				'created_at'     => $webhook['created_at'],
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
			'name'           => __( 'Name', 'wp-hook-expose' ),
			'event'          => __( 'Event', 'wp-hook-expose' ),
			'url'            => __( 'URL', 'wp-hook-expose' ),
			'last_execution' => __( 'Last Execution', 'wp-hook-expose' ),
			'created_at'     => __( 'Created At', 'wp-hook-expose' ),
		);
	}

	/**
	 * Renders the column content.
	 *
	 * @param array  $item        The data of the table row.
	 * @param string $column_name The column name.
	 *
	 * @return string The column content.
	 */
	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Renders the contents of the name column (with edit and delete action links).
	 *
	 * @param array $item The data of the table row.
	 *
	 * @return string The contents of the name column.
	 */
	public function column_name( $item ): string {
		$name = '<b style="font-size: larger;">' . $item['name'] . '</b>';

		$actions = array(
			// 'edit'   => sprintf( '<a href="?page=%s&action=%s&webhook_slug=%s">Edit</a>', $_REQUEST['page'], 'edit', $item['slug'] ),
			'delete' => sprintf( '<a href="?page=%s&action=%s&webhook_slug=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['slug'] ),
		);

		return sprintf( '%1$s %2$s', $name, $this->row_actions( $actions ) );
	}

	/**
	 * Renders the contents of the event column.
	 *
	 * @param array $item The data of the table row.
	 *
	 * @return string The contents of the event column.
	 */
	public function column_event( $item ): string {
		return '<code>' . $item['event'] . '</code>';
	}

	/**
	 * Renders the contents of the last_execution column.
	 *
	 * @param array $item The data of the table row.
	 *
	 * @return string The contents of the last_execution column.
	 */
	public function column_last_execution( $item ): string {
		if ( ! empty( $item['last_execution']['timestamp'] ) ) {
			$last_executed_at = $item['last_execution']['timestamp'];
			ob_start();
			?>
            <p>
				<?php
				echo esc_html( $last_executed_at );

				if ( ! empty( $item['last_execution']['response_status_code'] ) ) {
					?>
                    (<code><?php echo esc_html( $item['last_execution']['response_status_code'] ); ?></code>)
					<?php
				}
				?>

            </p>
			<?php
			return ob_get_clean();
		}

		return esc_html__( 'Never', 'wp-hook-expose' );
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

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
				'name'             => '<b style="font-size: larger; padding: 10px;">' . $webhook['name'] . '</b> (<code>' . $webhook_slug . '</code>)',
				'event'            => $webhook['event'],
				'url'              => $webhook['url'],
				'last_executed_at' => $webhook['last_executed_at'] ?? 'Never',
				'created_at'       => $webhook['created_at'],
			);
		}

		return $table_data;
	}

	/**
	 * Defines what data to show on each column of the table.
	 *
	 * @param array  $item        The data of the table row.
	 * @param string $column_name The column name.
	 */
	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Render checkboxes for (bulk) actions.
	 *
	 * @param array $item The current item.
	 *
	 * @return string HTML for the checkbox.
	 */
	public function column_cb( $item ): string {
		return sprintf(
			'<input type="checkbox" name="inquiries[]" id="%1$s" value="%2$s" />',
			$item['id'],
			$item['id']
		);
	}

	/**
	 * Overrides the parent columns method. Defines the columns to use in your listing table.
	 *
	 * @return array $columns Array of columns.
	 */
	public function get_columns(): array {
		return array(
			'cb'               => '<input type="checkbox" />',
			'name'             => __( 'Name', 'wp-hook-expose' ),
			'event'            => __( 'Event', 'wp-hook-expose' ),
			'url'              => __( 'URL', 'wp-hook-expose' ),
			'last_executed_at' => __( 'Last Executed At', 'wp-hook-expose' ),
			'created_at'       => __( 'Created At', 'wp-hook-expose' ),
		);
	}

	/**
	 * Overrides the parent prepare_items method. Prepares the list of items for displaying.
	 */
	public function prepare_items(): void {
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

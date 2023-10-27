<?php
/** WebhookListTable Class
 *
 * @package wp-hook-expose
 */

namespace WpHookExpose;

// If this file is accessed directly, abort.
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
	 * Get the table data.
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
				'name'       => $webhook['name'],
				'event'      => $webhook['event'],
				'body'       => $webhook['body'],
				'created_at' => $webhook['created_at'],
			);
		}

		return $table_data;
	}

	/**
	 * Prepare the items for the table to process.
	 */
	public function prepare_items(): void {
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		// Get the data from the database.
		$data = $this->table_data();

		// Sort the data.
		usort( $data, array( $this, 'sort_data' ) );

		// Pagination.
		$per_page     = 10;
		$current_page = $this->get_pagenum();
		$total_items  = count( $data );

		// Only necessary because we have sample data.
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		// Set the pagination arguments.
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);

		// Set the items for the table to process.
		$this->items = $data;
	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table.
	 *
	 * @return array $columns Array of columns.
	 */
	public function get_columns(): array {
		return array(
			'cb'         => '<input type="checkbox" />',
			'name'       => __( 'Name', 'wp-hook-expose' ),
			'event'      => __( 'Event', 'wp-hook-expose' ),
			'body'       => __( 'URL', 'wp-hook-expose' ),
			'created_at' => __( 'Created At', 'wp-hook-expose' ),
		);
	}
}

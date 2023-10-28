<?php
/** WebhookController Class
 *
 * @package wp-hook-expose
 */

namespace WpHookExpose;

// If this file is accessed directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class WebhookController for providing an abstracted API to manage webhooks.
 */
class WebhookController {
	/**
	 * Creates the WordPress option storing the webhooks (if it doesn't exist yet).
	 */
	public function create_webhook_option(): void {
		$options = get_option( 'wp_hook_expose' );
		if ( false === $options || empty( $options['webhooks'] ) ) {
			add_option(
				'wp_hook_expose',
				array(
					'webhooks' => array(),
				)
			);
		}
	}

	/**
	 * Gets all webhooks stored in the WordPress options table.
	 */
	public function get_webhooks(): array {
		return get_option( 'wp_hook_expose' )['webhooks'];
	}

	/**
	 * Adds a webhook to the WordPress options table.
	 *
	 * @param string $slug  The slug of the webhook.
	 *                      This is used to identify the webhook in the WordPress options table.
	 * @param string $name  The name of the webhook.
	 * @param string $event The WordPress hook the webhook should listen to.
	 * @param string $url   The URL the webhook should send a POST request to.
	 *
	 * @return bool
	 */
	public function add_webhook( string $slug, string $name, string $event, string $url ): bool {
		// Get all webhooks from the WordPress options table.
		$webhooks = $this->get_webhooks();

		if ( ! isset( $webhooks[ $slug ] ) ) {
			// Add the new webhook to the webhooks array.
			$webhooks[ $slug ] = array(
				'name'             => sanitize_text_field( $name ),
				'event'            => sanitize_text_field( $event ),
				'url'              => esc_url_raw( $url ),
				'last_executed_at' => null,
				'created_at'       => date( 'Y-m-d H:i:s' ),
			);


			// Update the webhooks in the WordPress options table.
			return update_option(
				'wp_hook_expose',
				array(
					'webhooks' => $webhooks,
				)
			);
		}

		return false;
	}

	/**
	 * Updates a webhook in the WordPress options table.
	 *
	 * @param string $slug    The slug of the webhook.
	 *                        This is used to identify the webhook in the WordPress options table.
	 * @param array  $updates The updates to apply to the webhook.
	 *
	 * @return bool
	 */
	public function update_webhook( string $slug, array $updates ): bool {
		// Get all webhooks from the WordPress options table.
		$webhooks = $this->get_webhooks();

		if ( isset( $webhooks[ $slug ] ) ) {
			// Update the webhook in the webhooks array.
			$webhooks[ $slug ] = array_merge(
				$webhooks[ $slug ],
				$updates
			);

			// Update the webhooks in the WordPress options table.
			return update_option(
				'wp_hook_expose',
				array(
					'webhooks' => $webhooks,
				)
			);
		}

		return false;
	}

	/**
	 * Executes a webhook by sending the HTTP request to the specified URL.
	 *
	 * @param string $webhook_slug The slug of the webhook to execute.
	 * @param array  $body         The body to send along the request for the webhook.
	 */
	public function execute_webhook( string $webhook_slug, array $body ): void {
		$webhooks = $this->get_webhooks();

		if ( isset( $webhooks[ $webhook_slug ] ) ) {
			$webhook = $webhooks[ $webhook_slug ];

			error_log( 'Webhook ' . $webhook_slug . ' called: ' . wp_json_encode( $body, true ) );

			// Send the request to the webhook URL.
			$response = wp_remote_post(
				$webhook['url'],
				array(
					'body' => wp_json_encode( $body, true )
				)
			);

			// Log the response.
			error_log( 'Webhook ' . $webhook_slug . ' response: ' . wp_json_encode( $response, true ) );

			// Update the last executed at timestamp of the webhook and write it to the WordPress options table.
			$this->update_webhook(
				$webhook_slug,
				array(
					'last_execution' => array(
						'timestamp'            => date( 'Y-m-d H:i:s' ),
						'response_status_code' => wp_remote_retrieve_response_code( $response ),
					),
				)
			);

			return;
		}

		// Log an error message.
		error_log( 'Webhook ' . $webhook_slug . ' not found.' );
	}

	/**
	 * Deletes a webhook from the WordPress options table.
	 *
	 * @param string $slug The slug of the webhook to delete.
	 */
	public function delete_webhook( string $slug ): bool {
		// Get all webhooks from the WordPress options table.
		$webhooks = $this->get_webhooks();

		// Delete the webhook from the webhooks array.
		unset( $webhooks[ $slug ] );

		// Update the webhooks in the WordPress options table.
		return update_option(
			'wp_hook_expose',
			array(
				'webhooks' => $webhooks,
			)
		);
	}
}

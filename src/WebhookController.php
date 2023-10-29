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
				'url'              => sanitize_url( $url ),
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
	 * @param array  $args         The args of the WordPress hook to send in the request body.
	 */
	public function execute_webhook( string $webhook_slug, array $args ): void {
		$webhooks = $this->get_webhooks();

		// Get and parse the plugin settings.
		$plugin_settings = get_option( 'wp_hook_expose_settings' );

		// Authorization settings.
		$webhook_secret = $plugin_settings['authorization']['webhook_secret'] ?? '';

		// Logging settings.
		$debug_log_setting    = $plugin_settings['logging']['debug_log'] ?? '0';
		$is_debug_log_enabled = '1' === $debug_log_setting;

		if ( isset( $webhooks[ $webhook_slug ] ) ) {
			// The webhook to execute.
			$webhook = $webhooks[ $webhook_slug ];

			// The body to sent to the webhook URL.
			$body = array(
				'args' => $args,
			);

			// If a webhook secret is set in the plugin settings, add it to the body.
			if ( ! empty( $webhook_secret ) ) {
				$body['wp_webhook_secret'] = $webhook_secret;
			}

			// Log the request.
			if ( $is_debug_log_enabled ) {
				error_log( 'Webhook ' . $webhook_slug . ' called: ' . wp_json_encode( $body, true ) );
			}

			// Send the request to the webhook URL.
			$response = wp_remote_post(
				$webhook['url'],
				array(
					'body' => wp_json_encode( $body, true )
				)
			);

			// Log the response.
			if ( $is_debug_log_enabled ) {
				error_log( 'Webhook ' . $webhook_slug . ' response: ' . wp_json_encode( $response, true ) );
			}

			// Get array of data to retain last execution data in accordance with the plugin settings.
			$last_execution_data = array();
			if ( is_array( $response ) ) {
				$last_execution_data = $this->create_last_execution_data( $response, $plugin_settings );
			}


			// Update the last executed at timestamp of the webhook and write it to the WordPress options table.
			$this->update_webhook(
				$webhook_slug,
				array(
					'last_execution' => $last_execution_data,
				)
			);

			return;
		}

		// Log an error message.
		if ( $is_debug_log_enabled ) {
			error_log( 'Webhook ' . $webhook_slug . ' not found.' );
		}
	}

	/**
	 * Creates an array of data about the last execution of a webhook to store in the WordPress options table.
	 *
	 * Only in accordance with the settings set in the plugin settings.
	 *
	 * @param array $response        The response of the webhook request.
	 * @param array $plugin_settings The plugin settings (wp_hook_expose_settings option).
	 */
	private function create_last_execution_data( array $response, array $plugin_settings ): array {
		$retain_last_execution_data = $plugin_settings['logging']['retain_last_execution_data'] ?? array( 'timestamp', 'response_status_code' );

		// Information about this webhook run to store in the WordPress options table.
		$last_execution_data = array();

		// If the response is an array & the ['retain_last_execution_data']['timestamp'] setting is set, add the data to the last execution data array.
		if ( in_array( 'timestamp', $retain_last_execution_data, true ) ) {
			$last_execution_data['timestamp'] = date( 'Y-m-d H:i:s' );
		}

		// If the response is an array & the ['retain_last_execution_data']['response_status_code'] setting is set, add the data to the last execution data array.
		if ( in_array( 'response_status_code', $retain_last_execution_data, true ) ) {
			$last_execution_data['response_status_code'] = wp_remote_retrieve_response_code( $response );
		}

		return $last_execution_data;
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

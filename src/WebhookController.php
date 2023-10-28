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
	 * Adds a webhook to the WordPress options table (overwrite existing webhook, if it exists).
	 *
	 * @param string $slug  The slug of the webhook.
	 *                      This is used to identify the webhook in the WordPress options table.
	 * @param string $name  The name of the webhook.
	 * @param string $event The WordPress hook the webhook should listen to.
	 * @param string $url   The URL the webhook should send a POST request to.
	 */
	public function add_or_update_webhook( string $slug, string $name, string $event, string $url ): bool {
		// Get all webhooks from the WordPress options table.
		$webhooks = $this->get_webhooks();

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
}

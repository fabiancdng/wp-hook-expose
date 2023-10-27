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
	 * Create the WordPress option storing the webhooks (if it doesn't exist yet).
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
	 * Get all webhooks stored in the WordPress options table.
	 */
	public function get_webhooks(): array {
		return get_option( 'wp_hook_expose' )['webhooks'];
	}
}

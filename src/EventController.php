<?php
/** EventController Class
 *
 * @package wp-hook-expose
 */

namespace WpHookExpose;

defined( 'ABSPATH' ) || exit;

class EventController {
	/**
	 * WebhookController instance to manage webhooks.
	 */
	private WebhookController $webhook_controller;

	/**
	 * The actions added by the EventController for each webhook.
	 */
	private array $actions;

	/**
	 * The constructor for the EventController class.
	 *
	 * @param WebhookController $webhook_controller WebhookController instance to manage webhooks.
	 */
	public function __construct( WebhookController $webhook_controller ) {
		$this->webhook_controller = $webhook_controller;
	}

	/**
	 * Subscribes the webhook callbacks to the specified event/WordPress hook.
	 */
	public function add_webhook_actions(): void {
		// Get the webhooks from the WordPress options table.
		$webhooks = $this->webhook_controller->get_webhooks();

		// Loop through the webhooks and subscribe the webhook callback to the WordPress hook.
		foreach ( $webhooks as $webhook_slug => $webhook ) {
			// Add action to the EventController.
			$this->actions[ $webhook_slug ] = function ( ...$args ) use ( $webhook_slug, $webhook ) {
				$this->webhook_callback( $webhook_slug, $webhook, $args );
			};

			// Add action to WordPress.
			add_action(
				$webhook['event'],
				$this->actions[ $webhook_slug ],
				10,
				PHP_INT_MAX
			);
		}
	}

	/**
	 * The callback for the webhook
	 *
	 * Sends off the request to the specified URL of the webhook.
	 *
	 * @param string $webhook_slug The slug of the webhook.
	 * @param array  $webhook      The webhook data.
	 * @param array  $args         The arguments passed to the WordPress hook.
	 */
	public function webhook_callback( string $webhook_slug, array $webhook, array $args ): void {
		// Execute the webhook.
		$this->webhook_controller->execute_webhook( $webhook_slug, $args );

		// Remove the action.
		remove_action(
			$webhook['event'],
			$this->actions[ $webhook_slug ],
			10
		);
	}
}

<?php

namespace WpHookExpose\Dashboard\Screens;

use WpHookExpose\WebhookController;

abstract class Screen {
	/**
	 * WebhookController instance to manage webhooks.
	 */
	protected WebhookController $webhook_controller;

	/**
	 * The constructor for the Screen class.
	 *
	 * @param WebhookController $webhook_controller WebhookController instance to manage webhooks.
	 */
	public function __construct( WebhookController $webhook_controller ) {
		$this->webhook_controller = $webhook_controller;
	}
}
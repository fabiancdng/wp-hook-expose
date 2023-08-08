<?php
/** UserRegistered Class
 *
 * @package wp-hook-expose
 */

namespace Fabiancdng\WpHookExpose\Hooks;


// If this file is accessed directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class UserRegistered for handling the WordPress hook 'user_register' and sending off the webhook request.
 */
class UserRegister {
	/**
	 * Handle the WordPress hook 'user_register' and send off the webhook request.
	 *
	 * @param int   $user_id   The ID of the user that was just registered.
	 * @param array $user_data The user data that was just registered.
	 */
	public function handle( int $user_id, array $user_data ): void {
		$options              = get_option( 'wp_hook_expose' );
		$webhook_url          = $options['event_webhooks']['user_registered']['url'];
		$webhook_request_body = json_decode( $options['event_webhooks']['user_registered']['body'], true );

		// Check if $webhook_url is a valid URL and $webhook_request_body is an array.
		if ( empty( $webhook_url ) || ! filter_var( $webhook_url, FILTER_VALIDATE_URL ) ) {
			return;
		}

		if ( empty( $webhook_request_body || ! is_array( $webhook_request_body ) ) ) {
			return;
		}
		// Merge body with hook data.
		$body = array_merge(
			$webhook_request_body,
			array(
				'user_id'   => $user_id,
				'user_data' => $user_data,
			)
		);

		// Send the webhook request.
		wp_remote_post(
			$webhook_url,
			array(
				'body' => $body,
			)
		);
	}
}
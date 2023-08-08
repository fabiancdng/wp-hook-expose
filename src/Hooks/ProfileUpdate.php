<?php
/** ProfileUpdate Class
 *
 * @package wp-hook-expose
 */

namespace Fabiancdng\WpHookExpose\Hooks;

use WP_User;

// If this file is accessed directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class ProfileUpdate for handling the WordPress hook 'profile_update' and sending off the webhook request.
 */
class ProfileUpdate {
	/**
	 * Handle the WordPress hook 'profile_update' and send off the webhook request.
	 *
	 * @param int   $user_id   The ID of the user that was just registered.
	 * @param array $user_data The user data that was just registered.
	 */
	public function handle( int $user_id, WP_User $old_user_data, array $user_data ): void {
		$options              = get_option( 'wp_hook_expose' );
		$webhook_url          = $options['event_webhooks']['user_profile_updated']['url'];
		$webhook_request_body = json_decode( $options['event_webhooks']['user_profile_updated']['body'], true );

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
				'user_id'       => $user_id,
				'old_user_data' => $old_user_data,
				'new_user_data' => $user_data,
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

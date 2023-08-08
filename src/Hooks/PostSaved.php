<?php

/** Save_Post Class
 *
 * @package wp-hook-expose
 */

namespace Fabiancdng\WpHookExpose\Hooks;


// If this file is accessed directly, abort.
use WP_Post;

defined( 'ABSPATH' ) || exit;

/**
 * Class Save_Post for handling the WordPress hook 'save_post' and sending off the webhook request.
 */
class PostSaved {
	/**
	 * Subscribe the 'handle' method to the according WordPress hooks.
	 */
	public function subscribe_wp_hooks(): void {
		add_action( 'save_post', array( $this, 'handle' ), 10, 3 );
	}

	/**
	 * Handle the WordPress hook 'save_post' and send off the webhook request.
	 *
	 * @param int     $post_id The ID of the post that was just saved.
	 * @param WP_Post $post    The post object that was just saved.
	 * @param bool    $update  Whether this is an existing post being updated or not.
	 */
	public function handle( int $post_id, WP_Post $post, bool $update ): void {
		if ( $update ) {
			$this->handle_post_create( $post_id, $post );
		} else {
			$this->handle_post_update( $post_id, $post );
		}
	}

	/**
	 * The saved post is a new post, send the webhook request for "Post Created".
	 *
	 * Option: wp_hook_expose[event_webhooks][post_created]
	 *
	 * @param int     $post_id The ID of the post that was just saved.
	 * @param WP_Post $post    The post object that was just saved.
	 */
	public function handle_post_create( int $post_id, WP_Post $post ): void {
		$options              = get_option( 'wp_hook_expose' );
		$webhook_url          = $options['event_webhooks']['post_created']['url'];
		$webhook_request_body = json_decode( $options['event_webhooks']['post_created']['body'], true );

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
				'post_id' => $post_id,
				'post'    => $post->to_array(),
			)
		);

		// Send the POST request.
		wp_remote_post(
			$webhook_url,
			array(
				'body' => wp_json_encode( $body )
			),
		);
	}

	/**
	 * The saved post is an existing post that has been updated, send the webhook request for "Post Updated".
	 *
	 * Option: wp_hook_expose[event_webhooks][post_updated]
	 *
	 * @param int     $post_id The ID of the post that was just saved.
	 * @param WP_Post $post    The post object that was just saved.
	 */
	public function handle_post_update( int $post_id, WP_Post $post ): void {
		$options              = get_option( 'wp_hook_expose' );
		$webhook_url          = $options['event_webhooks']['post_updated']['url'];
		$webhook_request_body = json_decode( $options['event_webhooks']['post_updated']['body'], true );

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
				'post_id' => $post_id,
				'post'    => $post->to_array(),
			)
		);

		// Send the post request.
		wp_remote_post(
			$webhook_url,
			array(
				'body' => wp_json_encode( $body )
			),
		);
	}
}

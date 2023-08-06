<?php

/** Save_Post Class
 *
 * @package wp-hook-expose
 */

namespace Fabiancdng\WP_Hook_Expose\Hooks;


// If this file is accessed directly, abort.
use WP_Post;

defined( 'ABSPATH' ) || exit;

/**
 * Class Save_Post for handling the WordPress hook 'save_post' and sending off the webhook request.
 */
class Save_Post_Hook {
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
	 * Option: wp_hook_expose['event_webhook_post_created']
	 *
	 * @param int     $post_id The ID of the post that was just saved.
	 * @param WP_Post $post    The post object that was just saved.
	 */
	public function handle_post_create( int $post_id, WP_Post $post ): void {
		$webhook_url = get_option( 'wp_hook_expose' )['event_webhook_post_created'];

		// Check if $webhook_url is a valid URL.
		if ( empty( $webhook_url ) || ! filter_var( $webhook_url, FILTER_VALIDATE_URL ) ) {
			return;
		}

		// Send the post request.
		wp_remote_post(
			$webhook_url,
			array(
				'body' => array(
					'post_id' => get_the_ID(),
					'post'    => $post->to_array(),
				),
			)
		);
	}

	/**
	 * The saved post is an existing post that has been updated, send the webhook request for "Post Updated".
	 *
	 * Option: wp_hook_expose['event_webhook_post_updated']
	 *
	 * @param int     $post_id The ID of the post that was just saved.
	 * @param WP_Post $post    The post object that was just saved.
	 */
	public function handle_post_update( int $post_id, WP_Post $post ): void {
		$webhook_url = get_option( 'wp_hook_expose' )['event_webhook_post_updated'];

		// Check if $webhook_url is a valid URL.
		if ( empty( $webhook_url ) || ! filter_var( $webhook_url, FILTER_VALIDATE_URL ) ) {
			return;
		}

		// Send the post request.
		wp_remote_post(
			$webhook_url,
			array(
				'body' => array(
					'post_id' => get_the_ID(),
					'post'    => $post->to_array(),
				),
			)
		);
	}
}

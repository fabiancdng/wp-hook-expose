<?php
/** CategorySaved Class
 *
 * @package wp-hook-expose
 */

namespace WpHookExpose\Events;

// If this file is accessed directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class TaxonomySaved for handling the WordPress hook 'saved_${taxonomy}' and sending off the webhook request.
 */
class CategorySaveEvent {
	/**
	 * Subscribe the 'handle' method to the according WordPress hooks.
	 */
	public function subscribe_wp_hooks(): void {
		add_action( 'saved_category', array( $this, 'handle' ), 10, 4 );
	}

	/**
	 * Handle the WordPress hook 'saved_${taxonomy}' and send off the webhook request.
	 *
	 * @param int  $term_id The ID of the term that was just saved.
	 * @param int  $tt_id   The term taxonomy ID.
	 * @param bool $update  Whether this is an existing term being updated or not.
	 */
	public function handle( int $term_id, int $tt_id, bool $update, array $args ): void {
		if ( $update ) {
			$this->handle_category_update(
				array(
					'term_id' => $term_id,
					'tt_id'   => $tt_id,
					'update'  => $update,
					'args'    => $args,
				)
			);
		} else {
			$this->handle_category_create(
				array(
					'term_id' => $term_id,
					'tt_id'   => $tt_id,
					'update'  => $update,
					'args'    => $args,
				)
			);
		}
	}

	/**
	 * Send the webhook request for "Category Created" as the saved taxonomy term is a new one.
	 *
	 * @param array $taxonomy_values The hook's values to be added to the webhook request body.
	 */
	public function handle_category_create( array $taxonomy_values ): void {
		$options              = get_option( 'wp_hook_expose' );
		$webhook_url          = $options['event_webhooks']['category_created']['url'];
		$webhook_request_body = json_decode( $options['event_webhooks']['category_created']['body'], true );

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
			$taxonomy_values
		);

		// Send the webhook request.
		error_log(
			wp_json_encode(
				wp_remote_post(
					$webhook_url,
					array(
						'body' => wp_json_encode( $body )
					),
				)
			)
		);
	}

	/**
	 * Send the webhook request for "Category Updated" as the saved taxonomy term is an existing one.
	 *
	 * @param array $taxonomy_values The hook's values to be added to the webhook request body.
	 */
	public function handle_category_update( array $taxonomy_values ): void {
		$options              = get_option( 'wp_hook_expose' );
		$webhook_url          = $options['event_webhooks']['category_updated']['url'];
		$webhook_request_body = json_decode( $options['event_webhooks']['category_updated']['body'], true );

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
			$taxonomy_values
		);

		// Send the webhook request.
		// Send the post request.
		error_log(
			wp_json_encode(
				wp_remote_post(
					$webhook_url,
					array(
						'body' => wp_json_encode( $body )
					),
				)
			)
		);
	}
}
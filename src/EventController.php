<?php
/** EventController Class
 *
 * @package wp-hook-expose
 */

namespace WpHookExpose;

// If this file is accessed directly, abort.
use WpHookExpose\Events\CategorySaveEvent;
use WpHookExpose\Events\PostSaveEvent;
use WpHookExpose\Events\ProfileUpdateEvent;
use WpHookExpose\Events\UserRegisterEvent;

defined( 'ABSPATH' ) || exit;

class EventController {
	/**
	 * Register all events a webhook can listen to by calling the `subscribe_wp_hooks()` method on each event class.
	 */
	public function register_events(): void {
		// Register and subscribe events to their according WordPress hooks.
		( new PostSaveEvent() )->subscribe_wp_hooks();
		( new UserRegisterEvent() )->subscribe_wp_hooks();
		( new ProfileUpdateEvent() )->subscribe_wp_hooks();
		( new CategorySaveEvent() )->subscribe_wp_hooks();
	}
}

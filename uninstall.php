<?php
// Exit if WordPress uninstall constant is not defined.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete the plugin options.
delete_option( 'wp_hook_expose_settings' );

// Delete all webhooks, their configuration, and their logs.
delete_option( 'wp_hook_expose' );
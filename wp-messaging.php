<?php
/**
 * Plugin Name: WP Messaging
 * Description: Limitless Communication: All-in-One, super scalable, messaging Solution for WordPress.
 * Version: 1.0.0
 * Author: Souptik Datta
 * Author URI: https://souptik.dev
 * Text Domain: wp-messaging
 *
 * @package wp-messaging
 */

namespace Souptik\WPMessaging;

define( 'SD_WP_MESSAGING_PATH', untrailingslashit( __DIR__ ) );

// Load Composer autoloader if available.
if ( file_exists( SD_WP_MESSAGING_PATH . '/vendor/autoload.php' ) ) {
	require_once SD_WP_MESSAGING_PATH . '/vendor/autoload.php';
}

// Load plugin files.
require_once SD_WP_MESSAGING_PATH . '/inc/namespace.php';

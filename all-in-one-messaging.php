<?php
/**
 * Plugin Name: All in One Messaging
 * Description: Limitless Communication: All-in-One, super scalable, messaging Solution for WordPress.
 * Version: 1.0.0
 * Author: Souptik Datta
 * Author URI: https://souptik.dev
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: all-in-one-messaging
 *
 * @package all-in-one-messaging
 */

namespace Souptik\AIOMessaging;

define( 'SD_AIO_MESSAGING_PATH', untrailingslashit( __DIR__ ) );

// Load Composer autoloader if available.
if ( file_exists( SD_AIO_MESSAGING_PATH . '/vendor/autoload.php' ) ) {
	require_once SD_AIO_MESSAGING_PATH . '/vendor/autoload.php';
}

// Load plugin files.
require_once SD_AIO_MESSAGING_PATH . '/inc/namespace.php';

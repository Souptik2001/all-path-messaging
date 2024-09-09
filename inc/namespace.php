<?php
/**
 * Namespace functions.
 *
 * @package wp-messaging
 */

namespace Souptik\WPMessaging;

// Bootstrap the plugin!
spl_autoload_register( __NAMESPACE__ . '\\autoload' );
bootstrap();

/**
 * Autoloader.
 *
 * @param  string $class_name Class name.
 *
 * @return void
 */
function autoload( string $class_name = '' ): void {
	// Check if namespace is correct.
	if ( 0 !== strpos( $class_name, __NAMESPACE__ ) ) {
		return;
	}

	// Format the namespace.
	$path          = SD_WP_MESSAGING_PATH . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR;
	$prefix_length = strlen( __NAMESPACE__ );
	$class_name    = substr( $class_name, $prefix_length + 1 );
	$class_name    = strtolower( $class_name );
	$file          = '';
	$last_ns_pos   = strripos( $class_name, '\\' );

	// Add the namespace.
	if ( false !== $last_ns_pos ) {
		$namespace  = substr( $class_name, 0, $last_ns_pos );
		$class_name = substr( $class_name, $last_ns_pos + 1 );
		$file       = str_replace( '\\', DIRECTORY_SEPARATOR, $namespace ) . DIRECTORY_SEPARATOR;
	}

	// Create the file name.
	$file .= 'class-' . str_replace( '_', '-', $class_name ) . '.php';
	$path .= $file;

	// Load the file.
	if ( file_exists( $path ) ) {
		require_once $path;
	}
}

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Load different messaging service classes.
	require_once SD_WP_MESSAGING_PATH . '/inc/sms/namespace.php';

	// Admin stuff.
	add_action(
		'init',
		function () {
			Admin::get_instance()->setup();
		}
	);
}

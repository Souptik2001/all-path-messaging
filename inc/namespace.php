<?php
/**
 * Namespace functions.
 *
 * @package all-in-one-messaging
 */

namespace Souptik\AIOMessaging;

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
	$path          = SD_AIO_MESSAGING_PATH . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR;
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
	require_once SD_AIO_MESSAGING_PATH . '/inc/sms/namespace.php';
	require_once SD_AIO_MESSAGING_PATH . '/inc/email/namespace.php';

	// Admin stuff.
	add_action(
		'init',
		function () {
			Admin::get_instance()->setup();
		}
	);
}

/**
 * Get all messaging services.
 *
 * Add new service like this -
 * add_filter(
 *     'wp_messaging_services',
 *     function ( array $services = [] ): array {
 *         $services[] = [
 *             'name'            => __( 'SMS: All in One Messaging', 'all-in-one-messaging' ),
 *             'menu_slug'       => str_replace( '_', '-', SLUG ),
 *             'menu_capability' => apply_filters( 'wp_messaging_sms_user_capability', 'manage_options' ),
 *             'menu_renderer'   => [ Admin::get_instance(), 'options_page' ],
 *         ];
 *
 *         return $services;
 *     }
 * );
 *
 * @return array{
 *     name: string,
 *     menu_slug: string,
 *     menu_capability: string,
 *     menu_renderer: mixed,
 * }[]
 */
function get_services(): array {
	// Get all services.
	$services = (array) apply_filters( 'wp_messaging_services', [] );

	// Filtered services.
	$filtered_services = [];

	// Loop over the services.
	foreach ( $services as $data ) {
		// Return early if invalid service.
		if ( ! is_array( $data ) || empty( $data['name'] ) || empty( $data['menu_slug'] ) || empty( $data['menu_capability'] ) || empty( $data['menu_renderer'] ) ) {
			continue;
		}

		// Add the service.
		$filtered_services[] = [
			'name'            => $data['name'],
			'menu_slug'       => $data['menu_slug'],
			'menu_capability' => $data['menu_capability'],
			'menu_renderer'   => $data['menu_renderer'],
		];
	}

	// Return the services.
	return $filtered_services;
}

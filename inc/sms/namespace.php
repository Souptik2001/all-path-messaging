<?php
/**
 * SMS: Namespace functions.
 *
 * @package wp-messaging
 */

namespace Souptik\WPMessaging\SMS;

use Utopia\Messaging\Messages\SMS;
use WP_Error;

const SLUG = 'wp_messaging_sms';

// Load different sms adapters.
require_once SD_WP_MESSAGING_PATH . '/inc/sms/adapters/twilio/namespace.php';

// Bootstrap the module!
spl_autoload_register( __NAMESPACE__ . '\\autoload' );
bootstrap();

/**
 * Autoloader.
 *
 * @param string $class_name Class name.
 *
 * @return void
 */
function autoload( string $class_name = '' ): void {
	// Check if namespace is correct.
	if ( 0 !== strpos( $class_name, __NAMESPACE__ ) ) {
		return;
	}

	// Format the namespace.
	$path          = SD_WP_MESSAGING_PATH . DIRECTORY_SEPARATOR . 'inc/sms' . DIRECTORY_SEPARATOR;
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
	// Add this service to the list of available services.
	add_filter(
		'wp_messaging_services',
		function ( array $services = [] ): array {
			$services[] = [
				'name'            => __( 'SMS: WP Messaging', 'wp-messaging' ),
				'menu_slug'       => str_replace( '_', '-', SLUG ),
				'menu_capability' => apply_filters( 'wp_messaging_sms_user_capability', 'manage_options' ),
				'menu_renderer'   => [ Admin::get_instance(), 'options_page' ],
			];

			// Return the services.
			return $services;
		}
	);
}

/**
 * Get all adapters.
 *
 * Add new adapter like this -
 * add_filter(
 *     \Souptik\WPMessaging\SMS\SLUG . '_adapters',
 *     function( array $adapters = [] ): array {
 *          $adapters[ 'your_adapter' ] = [
 *              'name'    => __( 'Your Adapter', 'wp-messaging' ),
 *              'adapter' => new Your_Adapter_Class(),
 *          ];
 *
 *          return $adapters;
 *     }
 * );
 *
 * @return array<string, mixed[]>
 */
function get_adapters(): array {
	// Return the adapters.
	return apply_filters( SLUG . '_adapters', [] );
}

/**
 * Send the message.
 *
 * @param string[] $to    Array of mobile numbers.
 * @param string   $message Message to send.
 * @param string   $adapter Adapter to use.
 *
 * @return array{
 *     deliveredTo: int,
 *     type: string,
 *     results: array<array<string, mixed>>
 * }|array<string, array{
 *     deliveredTo: int,
 *     type: string,
 *     results: array<array<string, mixed>>
 * }>|WP_Error
 */
function send( array $to = [], string $message = '', string $adapter = '' ): array|WP_Error {
	// Initialize the message.
	$message = new SMS(
		to: $to,
		content: $message
	);

	// Get all adapters.
	$adapters = get_adapters();

	// Get the active adapter - Should this be a filter?
	$active_adapter = get_option( SLUG . '_adapter', '' );

	// Override the settings adapter by the parameter's adapter.
	if ( empty( $adapter ) ) {
		$adapter = $active_adapter;
	}

	// Return early if adapter not found.
	if ( empty( $adapters[ $adapter ] ) || empty( $adapters[ $adapter ]['adapter'] ) ) {
		return new WP_Error( 'adapter_not_found', __( 'Adapter not found.', 'wp-messaging' ) );
	}

	// Get the adapter.
	$adapter_object = $adapters[ $adapter ]['adapter']->get_adapter();

	// Send the message.
	return $adapter_object->send( $message );
}

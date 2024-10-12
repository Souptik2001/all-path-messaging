<?php
/**
 * SMS: Namespace functions.
 *
 * @package all-in-one-messaging
 */

namespace Souptik\AIOMessaging\SMS;

use Souptik\AIOMessaging\SMS\Adapters\SMS_Adapter;
use Utopia\Messaging\Messages\SMS;
use WP_Error;

const SLUG = 'wp_messaging_sms';

// Load different sms adapters.
require_once SD_AIO_MESSAGING_PATH . '/inc/sms/adapters/twilio/namespace.php';
require_once SD_AIO_MESSAGING_PATH . '/inc/sms/adapters/telesign/namespace.php';

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
	$path          = SD_AIO_MESSAGING_PATH . DIRECTORY_SEPARATOR . 'inc/sms' . DIRECTORY_SEPARATOR;
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
				'name'            => __( 'SMS: All in One Messaging', 'all-in-one-messaging' ),
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
 *     \Souptik\AIOMessaging\SMS\SLUG . '_adapters',
 *     function( array $adapters = [] ): array {
 *          $adapters[ 'your_adapter' ] = [
 *              'name'    => __( 'Your Adapter', 'all-in-one-messaging' ),
 *              'adapter' => new Your_Adapter_Class(),
 *              'options' => Your_Adapter_Class::get_settings_fields(),
 *          ];
 *
 *          return $adapters;
 *     }
 * );
 *
 * @return array<string, array{
 *     name: string,
 *     adapter: SMS_Adapter,
 *     options: array<string, array{
 *         label: string,
 *         type: string,
 *         sanitize_callback: string,
 *     }>
 * }>
 */
function get_adapters(): array {
	// Get all adapters.
	$adapters = (array) apply_filters( SLUG . '_adapters', [] );

	// Filtered adapters.
	$filtered_adapters = [];

	// Loop over the adapters.
	foreach ( $adapters as $adapter => $data ) {
		// Return early if invalid adapter.
		if ( ! is_array( $data ) || empty( $data['name'] ) || empty( $data['adapter'] ) || ! $data['adapter'] instanceof SMS_Adapter ) {
			continue;
		}

		// Build the adapter options.
		$adapter_options = [];

		// Check if options are present.
		if ( ! empty( $data['options'] ) && is_array( $data['options'] ) ) {
			// Insert the options.
			foreach ( $data['options'] as $option => $option_data ) {
				// Skip if any of the data is missing.
				if ( empty( strval( $option ) ) || empty( $option_data['label'] ) || empty( $option_data['type'] ) || empty( $option_data['sanitize_callback'] ) ) {
					continue;
				}

				// Add the option.
				$adapter_options[ strval( $option ) ] = [
					'label'             => strval( $option_data['label'] ),
					'type'              => strval( $option_data['type'] ),
					'sanitize_callback' => strval( $option_data['sanitize_callback'] ),
				];
			}
		}

		// Add the adapter.
		$filtered_adapters[ strval( $adapter ) ] = [
			'name'    => $data['name'],
			'adapter' => $data['adapter'],
			'options' => $adapter_options,
		];
	}

	// Return the adapters.
	return $filtered_adapters;
}

/**
 * Send the message.
 *
 * @param string[] $to      Array of mobile numbers.
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
	// Get all adapters.
	$adapters = get_adapters();

	// Get the active adapter - Should this be a filter?
	$active_adapter = strval( get_option( SLUG . '_active_adapter', '' ) );

	// Override the settings adapter by the parameter's adapter.
	if ( empty( $adapter ) ) {
		$adapter = $active_adapter;
	}

	// Return early if adapter not found.
	if ( empty( $adapters[ $adapter ] ) ) {
		return new WP_Error( 'adapter_not_found', __( 'Adapter not found.', 'all-in-one-messaging' ) );
	}

	// Get the adapter.
	$adapter_object = $adapters[ $adapter ]['adapter']->get_adapter();

	// Return early if adapter not configured properly.
	if ( null === $adapter_object ) {
		return new WP_Error( 'adapter_not_configured', __( 'Adapter not configured.', 'all-in-one-messaging' ) );
	}

	// Initialize the message.
	$message = new SMS(
		to: $to,
		content: $message
	);

	// Send the message.
	return $adapter_object->send( $message );
}

<?php
/**
 * Email: Namespace functions.
 *
 * @package wp-messaging
 */

namespace Souptik\WPMessaging\Email;

use Souptik\WPMessaging\Email\Adapters\Email_Adapter;
use Utopia\Messaging\Messages\Email;
use WP_Error;

const SLUG = 'wp_messaging_email';

// Load different email adapters.
require_once SD_WP_MESSAGING_PATH . '/inc/email/adapters/mailgun/namespace.php';

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
	$path          = SD_WP_MESSAGING_PATH . DIRECTORY_SEPARATOR . 'inc/email' . DIRECTORY_SEPARATOR;
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
				'name'            => __( 'Email: WP Messaging', 'wp-messaging' ),
				'menu_slug'       => str_replace( '_', '-', SLUG ),
				'menu_capability' => apply_filters( 'wp_messaging_email_user_capability', 'manage_options' ),
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
 *     \Souptik\WPMessaging\Email\SLUG . '_adapters',
 *     function( array $adapters = [] ): array {
 *          $adapters[ 'your_adapter' ] = [
 *              'name'    => __( 'Your Adapter', 'wp-messaging' ),
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
 *     adapter: Email_Adapter,
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
		if ( ! is_array( $data ) || empty( $data['name'] ) || empty( $data['adapter'] ) || ! $data['adapter'] instanceof Email_Adapter ) {
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
 * @param string   $from_name  From name.
 * @param string   $from_email From email.
 * @param string[] $to         Array of emails.
 * @param string   $subject    Message to send.
 * @param string   $body       Body of the message.
 * @param string   $adapter    Adapter to use.
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
function send( string $from_name = '', string $from_email = '', array $to = [], string $subject = '', string $body = '', string $adapter = '' ): array|WP_Error {
	// Initialize the message.
	$message = new Email(
		to: $to,
		subject: $subject,
		content: $body,
		fromName: $from_name,
		fromEmail: $from_email,
	);

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
		return new WP_Error( 'adapter_not_found', __( 'Adapter not found.', 'wp-messaging' ) );
	}

	// Get the adapter.
	$adapter_object = $adapters[ $adapter ]['adapter']->get_adapter();

	// Return early if adapter not configured properly.
	if ( null === $adapter_object ) {
		return new WP_Error( 'adapter_not_configured', __( 'Adapter not configured.', 'wp-messaging' ) );
	}

	// Send the message.
	return $adapter_object->send( $message );
}

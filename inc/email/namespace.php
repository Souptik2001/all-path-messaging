<?php
/**
 * Email: Namespace functions.
 *
 * @package wp-messaging
 */

namespace Souptik\WPMessaging\Email;

use Souptik\WPMessaging\Email\Adapters\Email_Adapter;
use Utopia\Messaging\Messages\Email;
use Utopia\Messaging\Messages\Email\Attachment;
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

	// Hijack wp_mail.
	add_filter( 'pre_wp_mail', __NAMESPACE__ . '\\hijack_wp_mail', 10, 2 );
}

/**
 * Hijacks wp-mail execution and sends the email using the selected adapter.
 *
 * This function tries to mimic the wp_mail function as much as possible with the available hooks and filters.
 *
 * **Note:** This might have some edge cases with all edge cases not covered. Please report if you find any.
 *
 * @param ?bool                $success     Whether to preempt sending the email. Default null to continue with normal behavior, boolean to override.
 * @param array<string, mixed> $attributes Email attributes.
 *
 * @return bool
 */
function hijack_wp_mail( ?bool $success = null, array $attributes = [] ): bool {
	// Return early if invalid attributes.
	if ( empty( $attributes['to'] ) ) {
		// Mail fails.
		return false;
	}

	// Email meta.
	$cc              = [];
	$bcc             = [];
	$reply_to_name   = '';
	$replay_to_email = '';

	// Email home and destination.
	$from_name  = '';
	$from_email = '';
	$to         = $attributes['to'];

	// Convert `to` to array if not array.
	if ( ! is_array( $attributes ) ) {
		$to = explode( ',', strval( $attributes['to'] ) );
	}

	// Attachments.
	$attachments = $attributes['attachments'] ?? [];

	// Convert `attachments` to array if not array.
	if ( ! is_array( $attachments ) ) {
		$attachments = explode( "\n", str_replace( "\r\n", "\n", strval( $attributes['attachments'] ) ) );
	}

	// Headers.
	$headers = $attributes['headers'] ?? [];

	// Convert `headers` to array if not array.
	if ( ! is_array( $headers ) ) {
		/*
		 * Explode the headers out, so this function can take
		 * both string headers and an array of headers.
		 */
		$headers = explode( "\n", str_replace( "\r\n", "\n", strval( $headers ) ) );
	}

	// Parse headers.
	if ( ! empty( $headers ) ) {
		// Loop over the headers.
		foreach ( (array) $headers as $header ) {
			// Skip if header doesn't contain `:`.
			if ( ! str_contains( $header, ':' ) ) {
				continue;
			}

			// Explode the header out.
			list( $name, $content ) = explode( ':', trim( $header ), 2 );

			// Cleanup crew.
			$name    = trim( $name );
			$content = trim( $content );

			// Do required action for each header.
			switch ( strtolower( $name ) ) {
				// From header.
				case 'from':
					// Get the from values.
					$from       = parse_email_and_name( $content );
					$from_name  = $from['name'];
					$from_email = $from['email'];
					break;

				// CC header.
				case 'cc':
					// Set the cc.
					$cc = array_map(
						function ( $email ) {
							// Return parsed email and name.
							return parse_email_and_name( $email );
						},
						explode( ',', $content )
					);
					break;

				// BCC header.
				case 'bcc':
					// Set the bcc.
					$bcc = array_map(
						function ( $email ) {
							// Return parsed email and name.
							return parse_email_and_name( $email );
						},
						explode( ',', $content )
					);
					break;

				// Reply to header.
				case 'reply-to':
					// Get all the reply to values.
					$reply_to_array = explode( ',', $content );

					// We currently support only one reply to.
					if ( ! empty( $reply_to_array[0] ) ) {
						$reply_to        = parse_email_and_name( $reply_to_array[0] );
						$reply_to_name   = $reply_to['name'];
						$replay_to_email = $reply_to['email'];
					}
					break;
			}
		}
	}

	// If we don't have a name from the input headers.
	if ( empty( $from_name ) ) {
		$from_name = 'WordPress';
	}

	/*
	 * If we don't have an email from the input headers, default to wordpress@$sitename
	 * Some hosts will block outgoing mail from this address if it doesn't exist,
	 * but there's no easy alternative. Defaulting to admin_email might appear to be
	 * another option, but some hosts may refuse to relay mail from an unknown domain.
	 * See https://core.trac.wordpress.org/ticket/5007.
	 */
	if ( empty( $from_email ) ) {
		// Get the site name.
		$sitename   = wp_parse_url( network_home_url(), PHP_URL_HOST );
		$from_email = '';

		// Check if sitename is present.
		if ( null !== $sitename ) {
			$sitename = strval( $sitename );

			// Get the site domain and get rid of www.
			if ( str_starts_with( $sitename, 'www.' ) ) {
				$sitename = substr( $sitename, 4 );
			}

			// Construct the form email.
			$from_email = 'wordpress@' . $sitename;
		}
	}

	/**
	 * Filters the email address to send from.
	 *
	 * @param string $from_email Email address to send from.
	 */
	$from_email = apply_filters( 'wp_mail_from', $from_email );

	/**
	 * Filters the name to associate with the "from" email address.
	 *
	 * @param string $from_name Name associated with the "from" email address.
	 */
	$from_name = apply_filters( 'wp_mail_from_name', $from_name );

	// Prepare the email destinations.
	$to = array_map(
		function ( $value ) {
			return strval( $value );
		},
		(array) $to
	);

	// Send the mail!
	$success = send(
		$to,
		strval( $attributes['subject'] ?? '' ),
		strval( $attributes['message'] ?? '' ),
		strval( $from_name ),
		strval( $from_email ),
		[
			'cc'             => $cc,
			'bcc'            => $bcc,
			'attachments'    => $attachments,
			'reply_to_name'  => $reply_to_name,
			'reply_to_email' => $replay_to_email,
		]
	);

	// Return early if email failed.
	if ( $success instanceof WP_Error ) {
		return false;
	}

	// Convert to desired data structure.
	if ( ! isset( $success['deliveredTo'] ) ) {
		$success = array_values( $success );

		// Return early if no response.
		if ( empty( $success ) ) {
			return false;
		}

		// Get the response payload.
		$success = $success[0];
	}

	// Check for error conditions.
	if (
		empty( $success['deliveredTo'] )
		|| empty( $success['results'] )
	) {
		return false;
	}

	// Get the results.
	$results = $success['results'];

	// Check for error conditions.
	if (
		empty( $results[0] )
		|| (
			! empty( $results[0] )
			&& is_array( $results[0] )
			&& (
				'success' !== $results[0]['status']
				|| ! empty( $results[0]['error'] )
			)
		)
	) {
		return false;
	}

	// Return success.
	return true;
}

/**
 * Separates out email and name from a single value.
 *
 * You can pass values like -
 * - `Test name <test@souptik.dev>`
 *   - name will be`Test name`
 *   - email will be `test@soupti.dev
 * - `test@souptik.dev`
 *   - name will be`test@souptik.dev`
 *   - email will be `test@soupti.dev
 *
 * @param string $email_and_name Email and name in a string.
 *
 * @return array{
 *     name: string,
 *     email: string,
 * }
 */
function parse_email_and_name( string $email_and_name = '' ): array {
	// Name and email.
	$name  = '';
	$email = '';

	// Check for format.
	$bracket_position = strpos( $email_and_name, '<' );

	// Check if bracket is present - i.e both name and email is present.
	if ( false !== $bracket_position ) {
		// Text before the bracketed email is the name.
		if ( $bracket_position > 0 ) {
			$name = substr( $email_and_name, 0, $bracket_position );
			$name = str_replace( '"', '', $name );
			$name = trim( $name );
		}

		// Text inside the bracket is the email.
		$email = substr( $email_and_name, $bracket_position + 1 );
		$email = str_replace( '>', '', $email );
		$email = trim( $email );

		// Avoid setting an empty email.
	} elseif ( '' !== trim( $email_and_name ) ) {
		$email = trim( $email_and_name );
	}

	// If name is empty, set it to email.
	if ( empty( $name ) ) {
		$name = $email;
	}

	// Return.
	return [
		'name'  => $name,
		'email' => $email,
	];
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
 * @param string[] $to         Array of emails.
 * @param string   $subject    Message to send.
 * @param string   $body       Body of the message.
 * @param string   $from_name  From name.
 * @param string   $from_email From email.
 * @param mixed[]  $headers    Headers to send.
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
function send( array $to = [], string $subject = '', string $body = '', string $from_name = '', string $from_email = '', array $headers = [], string $adapter = '' ): array|WP_Error {
	// Get all adapters.
	$adapters = get_adapters();

	// Override the settings adapter by the parameter's adapter.
	if ( empty( $adapter ) ) {
		$adapter = strval( get_option( SLUG . '_active_adapter', '' ) );
	}

	// Get from name.
	if ( empty( $from_name ) ) {
		$from_name = strval( get_option( SLUG . '_from_name', '' ) );
	}

	// Get from email.
	if ( empty( $from_email ) ) {
		$from_email = strval( get_option( SLUG . '_from_email', '' ) );
	}

	// Return early if adapter not found.
	if ( empty( $adapters[ $adapter ] ) ) {
		return new WP_Error( 'adapter_not_found', __( 'Adapter not found.', 'wp-messaging' ) );
	}

	// Check for email common settings.
	if ( empty( $from_name ) || empty( $from_email ) ) {
		return new WP_Error( 'email_common_settings_not_configured', __( 'Email common settings not configured properly.', 'wp-messaging' ) );
	}

	// Get the adapter.
	$adapter_object = $adapters[ $adapter ]['adapter']->get_adapter();

	// Return early if adapter not configured properly.
	if ( null === $adapter_object ) {
		return new WP_Error( 'adapter_not_configured', __( 'Adapter not configured.', 'wp-messaging' ) );
	}

	// Get the headers.
	$headers = [
		'reply_to_name'  => strval( $headers['reply_to_name'] ?? '' ),
		'reply_to_email' => strval( $headers['reply_to_email'] ?? '' ),
		'cc'             => array_filter( (array) ( $headers['cc'] ?? [] ), 'is_array' ),
		'bcc'            => array_filter( (array) ( $headers['bcc'] ?? [] ), 'is_array' ),
		'attachments'    => (array) ( $headers['attachments'] ?? [] ),
	];

	// Attachments.
	$attachments = [];

	// Build the attachments.
	foreach ( $headers['attachments'] as $key => $attachment ) {
		// Skip if empty attachment.
		if ( empty( $attachment ) || ! is_string( $attachment ) ) {
			continue;
		}

		// Build the attachment.
		$attachments[] = new Attachment(
			name: is_string( $key ) ? $key : $attachment,
			path: $attachment,
			type: '',
		);
	}

	// Initialize the message.
	$message = new Email(
		to: $to,
		subject: $subject,
		content: $body,
		fromName: $from_name,
		fromEmail: $from_email,
		replyToName: $headers['reply_to_name'],
		replyToEmail: $headers['reply_to_email'],
		cc: $headers['cc'],
		bcc: $headers['bcc'],
		attachments: $attachments,
		html: true,
	);

	// Send the message.
	return $adapter_object->send( $message );
}

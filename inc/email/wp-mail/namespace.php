<?php
/**
 * Email: WP-Mail: Namespace functions.
 *
 * @package all-path-messaging
 */

namespace Souptik\AllPathMessaging\Email\WPMail;

use WP_Error;

use function Souptik\AllPathMessaging\Email\send;

use const Souptik\AllPathMessaging\Email\SLUG;

// Bootstrap the module!
bootstrap();

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Hijack wp_mail.
	add_filter( 'pre_wp_mail', __NAMESPACE__ . '\\hijack_wp_mail', 1, 2 );
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
 * @return ?bool
 */
function hijack_wp_mail( ?bool $success = null, array $attributes = [] ): ?bool {
	// Check if we need to hijack.
	if ( true !== boolval( get_option( SLUG . '_hijack_wp_mail', false ) ) ) {
		return $success;
	}

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

<?php
/**
 * Mailgun adapter: Namespace functions.
 *
 * @package wp-messaging
 */

namespace Souptik\WPMessaging\Email\Adapters\Mailgun;

use const Souptik\WPMessaging\Email\SLUG as EMAIL_SLUG;

const SLUG = 'mailgun';

// Bootstrap the module!
bootstrap();

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Add this adapter to the list of available adapters.
	add_filter(
		EMAIL_SLUG . '_adapters',
		function ( array $adapters = [] ): array {
			// Add the adapter.
			$adapters[ SLUG ] = [
				'name'    => __( 'Mailgun', 'wp-messaging' ),
				'adapter' => new Adapter(),
				'options' => Adapter::get_settings_fields(),
			];

			// Return the adapters.
			return $adapters;
		}
	);
}

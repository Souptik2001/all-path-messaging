<?php
/**
 * Mailgun adapter: Namespace functions.
 *
 * @package all-path-messaging
 */

namespace Souptik\AllPathMessaging\Email\Adapters\Mailgun;

use const Souptik\AllPathMessaging\Email\SLUG as EMAIL_SLUG;

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
				'name'    => __( 'Mailgun', 'all-path-messaging' ),
				'adapter' => new Adapter(),
				'options' => Adapter::get_settings_fields(),
			];

			// Return the adapters.
			return $adapters;
		}
	);
}

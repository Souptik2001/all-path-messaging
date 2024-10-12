<?php
/**
 * Brevo adapter: Namespace functions.
 *
 * @package all-in-one-messaging
 */

namespace Souptik\AIOMessaging\Email\Adapters\Brevo;

use const Souptik\AIOMessaging\Email\SLUG as EMAIL_SLUG;

const SLUG = 'brevo';

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
				'name'    => __( 'Brevo', 'all-in-one-messaging' ),
				'adapter' => new Adapter(),
				'options' => Adapter::get_settings_fields(),
			];

			// Return the adapters.
			return $adapters;
		}
	);
}

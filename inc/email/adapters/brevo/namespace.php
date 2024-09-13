<?php
/**
 * Brevo adapter: Namespace functions.
 *
 * @package wp-messaging
 */

namespace Souptik\WPMessaging\Email\Adapters\Brevo;

use const Souptik\WPMessaging\Email\SLUG as EMAIL_SLUG;

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
				'name'    => __( 'Brevo', 'wp-messaging' ),
				'adapter' => new Adapter(),
				'options' => Adapter::get_settings_fields(),
			];

			// Return the adapters.
			return $adapters;
		}
	);
}

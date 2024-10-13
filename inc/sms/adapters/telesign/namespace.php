<?php
/**
 * Telesign adapter: Namespace functions.
 *
 * @package all-path-messaging
 */

namespace Souptik\AllPathMessaging\SMS\Adapters\Telesign;

use const Souptik\AllPathMessaging\SMS\SLUG as SMS_SLUG;

const SLUG = 'telesign';

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
		SMS_SLUG . '_adapters',
		function ( array $adapters = [] ): array {
			// Add the adapter.
			$adapters[ SLUG ] = [
				'name'    => __( 'Telesign', 'all-path-messaging' ),
				'adapter' => new Adapter(),
				'options' => Adapter::get_settings_fields(),
			];

			// Return the adapters.
			return $adapters;
		}
	);
}

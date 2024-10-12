<?php
/**
 * Twilio adapter: Namespace functions.
 *
 * @package all-in-one-messaging
 */

namespace Souptik\AIOMessaging\SMS\Adapters\Twilio;

use const Souptik\AIOMessaging\SMS\SLUG as SMS_SLUG;

const SLUG = 'twilio';

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
				'name'    => __( 'Twilio', 'all-in-one-messaging' ),
				'adapter' => new Adapter(),
				'options' => Adapter::get_settings_fields(),
			];

			// Return the adapters.
			return $adapters;
		}
	);
}

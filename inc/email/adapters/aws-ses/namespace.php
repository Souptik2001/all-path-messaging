<?php
/**
 * AWS SES adapter: Namespace functions.
 *
 * @package all-in-one-messaging
 */

namespace Souptik\AIOMessaging\Email\Adapters\AWS_SES;

use const Souptik\AIOMessaging\Email\SLUG as EMAIL_SLUG;

const SLUG = 'aws_ses';

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
				'name'    => __( 'AWS SES', 'all-in-one-messaging' ),
				'adapter' => new Adapter(),
				'options' => Adapter::get_settings_fields(),
			];

			// Return the adapters.
			return $adapters;
		}
	);
}

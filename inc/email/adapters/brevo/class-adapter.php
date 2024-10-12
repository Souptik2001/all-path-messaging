<?php
/**
 * Brevo adapter: Adapter class.
 *
 * @package all-in-one-messaging
 */

namespace Souptik\WPMessaging\Email\Adapters\Brevo;

use Souptik\WPMessaging\Email\Adapters\Email_Adapter;
use Utopia\Messaging\Adapter\Email;

use const Souptik\WPMessaging\Email\SLUG as EMAIL_SLUG;

/**
 * Adapter class.
 */
class Adapter extends Email_Adapter {
	/**
	 * Get the adapter.
	 *
	 * @return ?Email Email object or null.
	 */
	public function get_adapter(): ?Email {
		// Get settings.
		$settings = $this->get_settings();

		// Return early if in-valid settings.
		if ( empty( $settings['api_key'] ) ) {
			return null;
		}

		// Return the adapter.
		return new Brevo( $settings['api_key'] );
	}

	/**
	 * Get adapter settings.
	 *
	 * @return array{
	 *     api_key: string,
	 * }
	 */
	public function get_settings(): array {
		// Return the adapter settings.
		return [
			'api_key' => strval( get_option( EMAIL_SLUG . '_' . SLUG . '_api_key', '' ) ),
		];
	}

	/**
	 * Get settings fields.
	 *
	 * @return array<string, array{
	 *     label: string,
	 *     type: string,
	 *     sanitize_callback: string,
	 * }>
	 */
	public static function get_settings_fields(): array {
		// Return the settings fields.
		return [
			EMAIL_SLUG . '_' . SLUG . '_api_key' => [
				'label'             => __( 'API KEY', 'all-in-one-messaging' ),
				'type'              => 'password',
				'sanitize_callback' => 'sanitize_text_field',
			],
		];
	}
}

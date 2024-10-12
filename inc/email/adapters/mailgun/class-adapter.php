<?php
/**
 * Mailgun adapter: Adapter class.
 *
 * @package all-in-one-messaging
 */

namespace Souptik\WPMessaging\Email\Adapters\Mailgun;

use Souptik\WPMessaging\Email\Adapters\Email_Adapter;
use Utopia\Messaging\Adapter\Email;
use Utopia\Messaging\Adapter\Email\Mailgun;

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
		if ( empty( $settings['api_key'] ) || empty( $settings['domain'] ) ) {
			return null;
		}

		// Return the adapter.
		return new Mailgun( $settings['api_key'], $settings['domain'] );
	}

	/**
	 * Get adapter settings.
	 *
	 * @return array{
	 *     api_key: string,
	 *     domain: string,
	 * }
	 */
	public function get_settings(): array {
		// Return the adapter settings.
		return [
			'api_key' => strval( get_option( EMAIL_SLUG . '_' . SLUG . '_api_key', '' ) ),
			'domain'  => strval( get_option( EMAIL_SLUG . '_' . SLUG . '_domain', '' ) ),
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
			EMAIL_SLUG . '_' . SLUG . '_domain'  => [
				'label'             => __( 'Domain', 'all-in-one-messaging' ),
				'type'              => 'text',
				'sanitize_callback' => 'sanitize_text_field',
			],
		];
	}
}

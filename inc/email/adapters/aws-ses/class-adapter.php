<?php
/**
 * AWS SES adapter: Adapter class.
 *
 * @package all-path-messaging
 */

namespace Souptik\AllPathMessaging\Email\Adapters\AWS_SES;

use Souptik\AllPathMessaging\Email\Adapters\Email_Adapter;
use Utopia\Messaging\Adapter\Email;

use const Souptik\AllPathMessaging\Email\SLUG as EMAIL_SLUG;

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
		if ( empty( $settings['region'] ) || empty( $settings['key'] ) || empty( $settings['secret'] ) ) {
			return null;
		}

		// Return the adapter.
		return new AWS_SES( $settings['region'], $settings['key'], $settings['secret'] );
	}

	/**
	 * Get adapter settings.
	 *
	 * @return array{
	 *     region: string,
	 *     key: string,
	 *     secret: string,
	 * }
	 */
	public function get_settings(): array {
		// Return the adapter settings.
		return [
			'region' => strval( get_option( EMAIL_SLUG . '_' . SLUG . '_region', '' ) ),
			'key'    => strval( get_option( EMAIL_SLUG . '_' . SLUG . '_key', '' ) ),
			'secret' => strval( get_option( EMAIL_SLUG . '_' . SLUG . '_secret', '' ) ),
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
			EMAIL_SLUG . '_' . SLUG . '_region' => [
				'label'             => __( 'Region', 'all-path-messaging' ),
				'type'              => 'text',
				'sanitize_callback' => 'sanitize_text_field',
			],
			EMAIL_SLUG . '_' . SLUG . '_key'    => [
				'label'             => __( 'Key', 'all-path-messaging' ),
				'type'              => 'text',
				'sanitize_callback' => 'sanitize_text_field',
			],
			EMAIL_SLUG . '_' . SLUG . '_secret' => [
				'label'             => __( 'Secret', 'all-path-messaging' ),
				'type'              => 'password',
				'sanitize_callback' => 'sanitize_text_field',
			],
		];
	}
}

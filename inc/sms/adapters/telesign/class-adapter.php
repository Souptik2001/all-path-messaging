<?php
/**
 * Telesign adapter: Adapter class.
 *
 * @package all-path-messaging
 */

namespace Souptik\AllPathMessaging\SMS\Adapters\Telesign;

use Souptik\AllPathMessaging\SMS\Adapters\SMS_Adapter;
use Utopia\Messaging\Adapter\SMS;
use Utopia\Messaging\Adapter\SMS\Telesign;

use const Souptik\AllPathMessaging\SMS\SLUG as SMS_SLUG;

/**
 * Adapter class.
 */
class Adapter extends SMS_Adapter {
	/**
	 * Get the adapter.
	 *
	 * @return ?SMS SMS object or null.
	 */
	public function get_adapter(): ?SMS {
		// Get settings.
		$settings = $this->get_settings();

		// Return early if in-valid settings.
		if ( empty( $settings['customer_id'] ) || empty( $settings['api_key'] ) ) {
			return null;
		}

		// Return the adapter.
		return new Telesign( $settings['customer_id'], $settings['api_key'] );
	}

	/**
	 * Get adapter settings.
	 *
	 * @return array{
	 *     customer_id: string,
	 *     api_key: string,
	 * }
	 */
	public function get_settings(): array {
		// Return the adapter settings.
		return [
			'customer_id' => strval( get_option( SMS_SLUG . '_' . SLUG . '_customer_id', '' ) ),
			'api_key'     => strval( get_option( SMS_SLUG . '_' . SLUG . '_api_key', '' ) ),
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
			SMS_SLUG . '_' . SLUG . '_customer_id' => [
				'label'             => __( 'Customer ID', 'all-path-messaging' ),
				'type'              => 'password',
				'sanitize_callback' => 'sanitize_text_field',
			],
			SMS_SLUG . '_' . SLUG . '_api_key'     => [
				'label'             => __( 'API Key', 'all-path-messaging' ),
				'type'              => 'password',
				'sanitize_callback' => 'sanitize_text_field',
			],
		];
	}
}

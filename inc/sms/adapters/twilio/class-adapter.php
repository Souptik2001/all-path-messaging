<?php
/**
 * Twilio adapter: Adapter class.
 *
 * @package wp-messaging
 */

namespace Souptik\WPMessaging\SMS\Adapters\Twilio;

use Souptik\WPMessaging\SMS\Adapters\SMS_Adapter;
use Utopia\Messaging\Adapter\SMS;
use Utopia\Messaging\Adapter\SMS\Twilio;

use const Souptik\WPMessaging\SMS\SLUG as SMS_SLUG;

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
		if ( empty( $settings['account_sid'] ) || empty( $settings['auth_token'] ) || empty( $settings['from'] ) ) {
			return null;
		}

		// Return the adapter.
		return new Twilio( $settings['account_sid'], $settings['auth_token'], $settings['from'] );
	}

	/**
	 * Get adapter settings.
	 *
	 * @return array{
	 *     account_sid: string,
	 *     auth_token: string,
	 *     from: string,
	 * }
	 */
	public function get_settings(): array {
		// Return the adapter settings.
		return [
			'account_sid' => get_option( SMS_SLUG . '_' . SLUG . '_account_sid', '' ),
			'auth_token'  => get_option( SMS_SLUG . '_' . SLUG . '_auth_token', '' ),
			'from'        => get_option( SMS_SLUG . '_' . SLUG . '_from', '' ),
		];
	}

	/**
	 * Get settings fields.
	 *
	 * @return array<string, string>
	 */
	public static function get_settings_fields(): array {
		// Return the settings fields.
		return [
			SMS_SLUG . '_' . SLUG . '_account_sid' => [
				'label'             => __( 'Account SID', 'wp-messaging' ),
				'type'              => 'password',
				'sanitize_callback' => 'sanitize_text_field',
			],
			SMS_SLUG . '_' . SLUG . '_auth_token'  => [
				'label'             => __( 'Auth Token', 'wp-messaging' ),
				'type'              => 'password',
				'sanitize_callback' => 'sanitize_text_field',
			],
			SMS_SLUG . '_' . SLUG . '_from'        => [
				'label'             => __( 'From phone number', 'wp-messaging' ),
				'type'              => 'text',
				'sanitize_callback' => 'sanitize_text_field',
			],
		];
	}
}

<?php
/**
 * Dummy adapter: Adapter class.
 *
 * @package all-in-one-messaging
 */

namespace Souptik\AIOMessaging\Email\Adapters\Dummy;

use Souptik\AIOMessaging\Email\Adapters\Email_Adapter;
use Utopia\Messaging\Adapter\Email;

use const Souptik\AIOMessaging\Email\SLUG as EMAIL_SLUG;

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
		if ( empty( $settings['api_key_or_something'] ) || empty( $settings['something_else'] ) ) {
			return null;
		}

		/**
		 * Return the adapter.
		 *
		 * If Utopia provides a adapter for this provider, then use that, else create and use that (as demonstrated in this example).
		 *
		 * If Utopia provides the adapter, then the dummy class file will not be present.
		 */
		return new Dummy( $settings['api_key_or_something'], $settings['something_else'] );
	}

	/**
	 * Get adapter settings.
	 *
	 * @return array{
	 *     api_key_or_something: string,
	 *     something_else: string,
	 * }
	 */
	public function get_settings(): array {
		// Return the adapter settings.
		return [
			'api_key_or_something' => strval( get_option( EMAIL_SLUG . '_' . SLUG . '_api_key_or_something', '' ) ),
			'something_else'       => strval( get_option( EMAIL_SLUG . '_' . SLUG . '_something_else', '' ) ),
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
			EMAIL_SLUG . '_' . SLUG . '_api_key_or_something' => [
				'label'             => __( 'API KEY or Something else', 'all-in-one-messaging' ),
				'type'              => 'password',
				'sanitize_callback' => 'sanitize_text_field',
			],
			EMAIL_SLUG . '_' . SLUG . '_something_else'       => [
				'label'             => __( 'Something else', 'all-in-one-messaging' ),
				'type'              => 'text',
				'sanitize_callback' => 'sanitize_text_field',
			],
		];
	}
}

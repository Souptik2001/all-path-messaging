<?php
/**
 * SMS Adapter: SMS_Adapter class.
 *
 * @package all-in-one-messaging
 */

namespace Souptik\WPMessaging\SMS\Adapters;

use Utopia\Messaging\Adapter\SMS;

/**
 * SMS_Adapter class.
 */
abstract class SMS_Adapter {
	/**
	 * Get the adapter.
	 *
	 * @return ?SMS SMS object or null.
	 */
	abstract public function get_adapter(): ?SMS;

	/**
	 * Get adapter settings.
	 *
	 * @return array<string, string>
	 */
	abstract public function get_settings(): array;

	/**
	 * Get settings fields.
	 *
	 * @return array<string, array{
	 *     label: string,
	 *     type: string,
	 *     sanitize_callback: string,
	 * }>
	 */
	abstract public static function get_settings_fields(): array;
}

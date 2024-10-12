<?php
/**
 * Email Adapter: Email_Adapter class.
 *
 * @package all-in-one-messaging
 */

namespace Souptik\WPMessaging\Email\Adapters;

use Utopia\Messaging\Adapter\Email;

/**
 * Email_Adapter class.
 */
abstract class Email_Adapter {
	/**
	 * Get the adapter.
	 *
	 * @return ?Email Email object or null.
	 */
	abstract public function get_adapter(): ?Email;

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

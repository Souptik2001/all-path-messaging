<?php
/**
 * SMS: Admin side things.
 *
 * @package wp-messaging
 */

namespace Souptik\WPMessaging\SMS;

class Admin {

	private static $_instance = null;

	/**
	 * Get current instance.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Options page.
	 *
	 * @return void
	 */
	public function options_page() {
		// Load template.
		load_template( SD_WP_MESSAGING_PATH . '/inc/sms/templates/options.php' );
	}

}
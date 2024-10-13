<?php
/**
 * Admin side things.
 *
 * @package all-path-messaging
 */

namespace Souptik\AllPathMessaging;

/**
 * Admin class.
 */
class Admin {

	/**
	 * Current instance.
	 *
	 * @var ?self
	 */
	private static $instance = null;

	/**
	 * Get current instance.
	 *
	 * @return self
	 */
	public static function get_instance(): self {
		// Check if instance is already present.
		if ( ! self::$instance ) {
			// Create the self instance.
			self::$instance = new self();
		}

		// Return the instance.
		return self::$instance;
	}

	/**
	 * Setup hooks and filters.
	 *
	 * @return void
	 */
	public function setup(): void {
		// Setup hooks.
		add_action( 'admin_menu', [ $this, 'admin_menu_item' ] );
	}

	/**
	 * Add admin menu item.
	 *
	 * @return void
	 */
	public function admin_menu_item(): void {
		// Get the available services.
		$available_services = get_services();

		// Loop through all available services and create a menu item for each.
		foreach ( $available_services as $available_service ) {
			// Add the Tools sub-pages.
			add_management_page(
				$available_service['name'],
				$available_service['name'],
				$available_service['menu_capability'],
				$available_service['menu_slug'],
				$available_service['menu_renderer']
			);
		}
	}
}

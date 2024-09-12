<?php
/**
 * Email: Admin side things.
 *
 * @package wp-messaging
 */

namespace Souptik\WPMessaging\Email;

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
	 * Options page.
	 *
	 * @return void
	 */
	public function options_page(): void {
		// Check for POST.
		if (
			isset( $_POST[ SLUG . '_nonce' ] )
			&& wp_verify_nonce( sanitize_key( $_POST[ SLUG . '_nonce' ] ), SLUG . '_options' )
		) {
			// Set the active adapter.
			update_option( SLUG . '_active_adapter', sanitize_text_field( $_POST[ SLUG . '_active_adapter' ] ?? '' ) );

			// Set common settings.
			update_option( SLUG . '_from_name', sanitize_text_field( $_POST[ SLUG . '_from_name' ] ?? '' ) );
			update_option( SLUG . '_from_email', sanitize_email( $_POST[ SLUG . '_from_email' ] ?? '' ) );
			update_option( SLUG . '_hijack_wp_mail', 'yes' === sanitize_text_field( $_POST[ SLUG . '_hijack_wp_mail' ] ?? 'no' ) );

			// Get all the adapters.
			$adapters = get_adapters();

			// Loop over all the adapters.
			foreach ( $adapters as $adapter ) {
				// Check if options are present.
				if ( ! empty( $adapter['options'] ) ) {
					// Loop over all the settings.
					foreach ( $adapter['options'] as $option_key => $option ) {
						// Update the option.
						update_option( $option_key, call_user_func( $option['sanitize_callback'], $_POST[ $option_key ] ) );
					}
				}
			}

			// Output settings update message.
			echo '<div class="updated"><p>' . esc_html__( 'Options saved.', 'wp-messaging' ) . '</p></div>';
		}

		// Load template.
		load_template( SD_WP_MESSAGING_PATH . '/inc/email/templates/options.php' );
	}

	/**
	 * Adapters settings template.
	 *
	 * @return void
	 */
	public function adapters_settings_template(): void {
		// Load the template.
		load_template( SD_WP_MESSAGING_PATH . '/inc/email/templates/adapters.php' );
	}
}

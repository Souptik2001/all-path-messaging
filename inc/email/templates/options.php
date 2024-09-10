<?php
/**
 * Email options template.
 *
 * @package wp-messaging
 */

namespace Souptik\WPMessaging\Email;

?>

<div class="wrap">

	<h2>
		<?php esc_html_e( 'Email Options: WP Messaging', 'wp-messaging' ); ?>
	</h2>

	<div class="card">
		<form method="post" action="">
			<table class="form-table">
				<tbody>
					<?php Admin::get_instance()->adapters_settings_template(); ?>
				</tbody>
			</table>
			<?php wp_nonce_field( SLUG . '_options', SLUG . '_nonce' ); ?>
			<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Save', 'wp-messaging' ); ?>" type="submit"></p>
		</form>
	</div> <!-- .card -->

</div> <!-- .wrap -->

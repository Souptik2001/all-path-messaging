<?php
/**
 * SMS options template.
 *
 * @package all-in-one-messaging
 */

namespace Souptik\WPMessaging\SMS;

?>

<div class="wrap">

	<h2>
		<?php esc_html_e( 'SMS Options: All in One Messaging', 'all-in-one-messaging' ); ?>
	</h2>

	<div class="card">
		<form method="post" action="">
			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<h3><?php esc_html_e( 'Adapter Settings', 'all-in-one-messaging' ); ?></h3>
						</th>
					</tr>

					<?php Admin::get_instance()->adapters_settings_template(); ?>
				</tbody>
			</table>
			<?php wp_nonce_field( SLUG . '_options', SLUG . '_nonce' ); ?>
			<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Save', 'all-in-one-messaging' ); ?>" type="submit"></p>
		</form>
	</div> <!-- .card -->

</div> <!-- .wrap -->

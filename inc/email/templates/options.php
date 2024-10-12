<?php
/**
 * Email options template.
 *
 * @package all-in-one-messaging
 */

namespace Souptik\AIOMessaging\Email;

?>

<div class="wrap">

	<h2>
		<?php esc_html_e( 'Email Options: All in One Messaging', 'all-in-one-messaging' ); ?>
	</h2>

	<div class="card">
		<form method="post" action="">
			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<h3><?php esc_html_e( 'Common Settings', 'all-in-one-messaging' ); ?></h3>
						</th>
					</tr>
					<tr>
						<th scope="row">
							<label for="email_from_name"><?php esc_html_e( 'From Name', 'all-in-one-messaging' ); ?></label>
						</th>
						<td>
							<input
								name="<?php echo esc_attr( SLUG . '_from_name' ); ?>"
								id="email_from_name"
								type="text"
								value="<?php echo esc_attr( strval( get_option( SLUG . '_from_name', '' ) ) ); ?>"
								class="regular-text"
							>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="email_from_address"><?php esc_html_e( 'From Address', 'all-in-one-messaging' ); ?></label>
						</th>
						<td>
							<input
								name="<?php echo esc_attr( SLUG . '_from_email' ); ?>"
								id="email_from_address"
								type="email"
								value="<?php echo esc_attr( strval( get_option( SLUG . '_from_email', '' ) ) ); ?>"
								class="regular-text"
							>
						</td>
					</tr>
					<tr>
					<th scope="row">
							<label for="override_wp_mail">
								<?php esc_html_e( 'Override wp_mail functionality', 'all-in-one-messaging' ); ?>
							</label>
						</th>
						<td>
							<input
								type="checkbox"
								id="override_wp_mail"
								name="<?php echo esc_attr( SLUG . '_hijack_wp_mail' ); ?>"
								value="yes"
								<?php checked( boolval( get_option( SLUG . '_hijack_wp_mail', false ) ), true ); ?>
							>
						</td>
					</tr>

					<tr>
						<th>
							<h3><?php esc_html_e( 'Adapters Settings', 'all-in-one-messaging' ); ?></h3>
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

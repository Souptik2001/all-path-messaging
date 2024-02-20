<div class="wrap">

	<h2><?php esc_html_e( 'SMS Options: WP Messaging', 'wp-messaging' ); ?></h2>

	<div class="card">
		<form method="post" action="">
			<table class="form-table">
				<tbody>
				</tbody>
			</table>
			<?php wp_nonce_field( 'wp_messaging_sms_options', 'wp_messaging_nonce' ); ?>
			<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Save', 'wp-messaging' ); ?>" type="submit"></p>
		</form>
	</div> <!-- .card -->

</div> <!-- .wrap -->
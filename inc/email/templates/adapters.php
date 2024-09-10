<?php
/**
 * Email adapters settings template.
 *
 * @package wp-messaging
 */

namespace Souptik\WPMessaging\Email;

// Get all the adapters.
$adapters = get_adapters();

// Return early if not adapters found.
if ( ! is_array( $adapters ) ) {
	return;
}

// Loop over all the adapters.
foreach ( $adapters as $key => $adapter ) {
	?>
		<tr>
			<th><?php echo esc_html( $adapter['name'] ); ?></th>
		</tr>
		<?php
		// Check if options are present.
		if ( ! empty( $adapter['options'] ) ) {
			// Loop over all the settings.
			foreach ( $adapter['options'] as $option_key => $option ) {
				?>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $option_key ); ?>"><?php echo esc_html( $option['label'] ); ?></label></th>
						<td>
							<?php
							if ( in_array( $option['type'], [ 'text', 'password' ], true ) ) {
								?>
										<input type="<?php echo esc_attr( $option['type'] ); ?>" id="<?php echo esc_attr( $option_key ); ?>" name="<?php echo esc_attr( $option_key ); ?>" value="<?php echo esc_attr( strval( get_option( $option_key, '' ) ) ); ?>" class="regular-text">
									<?php
							}
							?>
						</td>
					</tr>
				<?php
			}
		}
}

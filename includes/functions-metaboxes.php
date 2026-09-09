<?php
/**
 * Metabox functions.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\WidgetAnnouncements;

/**
 * Add repeat announcement metabox to sidebar.
 *
 * @since 1.0.0
 */
function add_sidebar_metabox() {
	add_meta_box(
		'azrcrv-wa-box',
		esc_html__( 'Repeat announcement', 'azrcrv-wa' ),
		__NAMESPACE__ . '\\generate_sidebar_metabox',
		array( 'widget-announcement' ),
		'side',
		'default'
	);
}

/**
 * Generate repeat announcement sidebar metabox.
 *
 * @since 1.0.0
 */
function generate_sidebar_metabox() {
	global $post;

	wp_nonce_field( basename( PLUGIN_FILE ), 'azrcrv-wa-sidebar-nonce' );

	$repeat = get_post_meta( $post->ID, '_azrcrv_wa_repeat', true );

	$instance = array(
		'first'  => esc_html__( '1st', 'azrcrv-wa' ),
		'second' => esc_html__( '2nd', 'azrcrv-wa' ),
		'third'  => esc_html__( '3rd', 'azrcrv-wa' ),
		'fourth' => esc_html__( '4th', 'azrcrv-wa' ),
	);

	$days = array(
		'Sunday'    => esc_html__( 'Sunday', 'azrcrv-wa' ),
		'Monday'    => esc_html__( 'Monday', 'azrcrv-wa' ),
		'Tuesday'   => esc_html__( 'Tuesday', 'azrcrv-wa' ),
		'Wednesday' => esc_html__( 'Wednesday', 'azrcrv-wa' ),
		'Thursday'  => esc_html__( 'Thursday', 'azrcrv-wa' ),
		'Friday'    => esc_html__( 'Friday', 'azrcrv-wa' ),
		'Saturday'  => esc_html__( 'Saturday', 'azrcrv-wa' ),
	);

	$months = array(
		1  => esc_html__( 'Jan', 'azrcrv-wa' ),
		2  => esc_html__( 'Feb', 'azrcrv-wa' ),
		3  => esc_html__( 'Mar', 'azrcrv-wa' ),
		4  => esc_html__( 'Apr', 'azrcrv-wa' ),
		5  => esc_html__( 'May', 'azrcrv-wa' ),
		6  => esc_html__( 'Jun', 'azrcrv-wa' ),
		7  => esc_html__( 'Jul', 'azrcrv-wa' ),
		8  => esc_html__( 'Aug', 'azrcrv-wa' ),
		9  => esc_html__( 'Sept', 'azrcrv-wa' ),
		10 => esc_html__( 'Oct', 'azrcrv-wa' ),
		11 => esc_html__( 'Nov', 'azrcrv-wa' ),
		12 => esc_html__( 'Dec', 'azrcrv-wa' ),
	);

	$repeat_type = isset( $repeat['type'] ) ? $repeat['type'] : 'none';
	?>

	<fieldset>

		<p>
			<input type="radio" id="none" name="repeat-type" value="none" <?php checked( $repeat_type, 'none' ); ?> />
			<label for="none"><?php esc_html_e( 'No repeat', 'azrcrv-wa' ); ?></label>
		</p>

		<p>
			<input type="radio" id="monthly" name="repeat-type" value="monthly" <?php checked( $repeat_type, 'monthly' ); ?> />
			<label for="monthly"><?php esc_html_e( 'Repeat monthly', 'azrcrv-wa' ); ?></label>
		</p>

		<p>
			<input type="radio" id="annual" name="repeat-type" value="annual" <?php checked( $repeat_type, 'annual' ); ?> />
			<label for="annual"><?php esc_html_e( 'Repeat annually', 'azrcrv-wa' ); ?></label>
		</p>

		<p>
			<input type="radio" id="goodfriday" name="repeat-type" value="goodfriday" <?php checked( $repeat_type, 'goodfriday' ); ?> />
			<label for="goodfriday"><?php esc_html_e( 'Repeat on Good Friday', 'azrcrv-wa' ); ?></label>
		</p>

		<p>
			<input type="radio" id="eastersunday" name="repeat-type" value="eastersunday" <?php checked( $repeat_type, 'eastersunday' ); ?> />
			<label for="eastersunday"><?php esc_html_e( 'Repeat on Easter Sunday', 'azrcrv-wa' ); ?></label>
		</p>

		<p>
			<input type="radio" id="eastermonday" name="repeat-type" value="eastermonday" <?php checked( $repeat_type, 'eastermonday' ); ?> />
			<label for="eastermonday"><?php esc_html_e( 'Repeat on Easter Monday', 'azrcrv-wa' ); ?></label>
		</p>

		<p>
			<input type="radio" id="monthnday" name="repeat-type" value="monthnday" <?php checked( $repeat_type, 'monthnday' ); ?> />
			<label for="monthnday"><?php esc_html_e( 'Repeat monthly on <em>n day</em> of month', 'azrcrv-wa' ); ?></label>

			<span class="azrcrv-wa-repeat-selects">
				<select name="month-repeat-instance">
					<?php foreach ( $instance as $instance_number => $instance_name ) : ?>
						<option value="<?php echo esc_attr( $instance_number ); ?>" <?php selected( isset( $repeat['month-repeat']['instance'] ) ? $repeat['month-repeat']['instance'] : '', $instance_number ); ?>>
							<?php echo esc_html( $instance_name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				&nbsp;
				<select name="month-repeat-day">
					<?php foreach ( $days as $day_number => $day_name ) : ?>
						<option value="<?php echo esc_attr( $day_number ); ?>" <?php selected( isset( $repeat['month-repeat']['day'] ) ? $repeat['month-repeat']['day'] : '', $day_number ); ?>>
							<?php echo esc_html( $day_name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</span>
		</p>

		<p>
			<input type="radio" id="annualnday" name="repeat-type" value="annualnday" <?php checked( $repeat_type, 'annualnday' ); ?> />
			<label for="annualnday"><?php esc_html_e( 'Repeat annually on <em>n day</em> of month', 'azrcrv-wa' ); ?></label>

			<span class="azrcrv-wa-repeat-selects">
				<select name="annual-repeat-instance">
					<?php foreach ( $instance as $instance_number => $instance_name ) : ?>
						<option value="<?php echo esc_attr( $instance_number ); ?>" <?php selected( isset( $repeat['annual-repeat']['instance'] ) ? $repeat['annual-repeat']['instance'] : '', $instance_number ); ?>>
							<?php echo esc_html( $instance_name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				&nbsp;
				<select name="annual-repeat-day">
					<?php foreach ( $days as $day_number => $day_name ) : ?>
						<option value="<?php echo esc_attr( $day_number ); ?>" <?php selected( isset( $repeat['annual-repeat']['day'] ) ? $repeat['annual-repeat']['day'] : '', $day_number ); ?>>
							<?php echo esc_html( $day_name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				&nbsp;
				<select name="annual-repeat-month">
					<?php foreach ( $months as $month_number => $month_name ) : ?>
						<option value="<?php echo esc_attr( $month_number ); ?>" <?php selected( isset( $repeat['annual-repeat']['month'] ) ? $repeat['annual-repeat']['month'] : '', $month_number ); ?>>
							<?php echo esc_html( $month_name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</span>
		</p>

	</fieldset>

	<?php
}

/**
 * Save repeat announcement sidebar metabox.
 *
 * @since 1.0.0
 *
 * @param int $post_id The post ID.
 */
function save_sidebar_metabox( $post_id ) {

	if ( ! isset( $_POST['azrcrv-wa-sidebar-nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['azrcrv-wa-sidebar-nonce'] ), basename( PLUGIN_FILE ) ) ) {
		return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	$post_type = get_post_type( $post_id );

	if ( 'widget-announcement' === $post_type ) {
		update_post_meta(
			$post_id,
			'_azrcrv_wa_repeat',
			array(
				'type'          => sanitize_text_field( wp_unslash( $_POST['repeat-type'] ) ),
				'month-repeat'  => array(
					'instance' => sanitize_text_field( wp_unslash( $_POST['month-repeat-instance'] ) ),
					'day'      => sanitize_text_field( wp_unslash( $_POST['month-repeat-day'] ) ),
				),
				'annual-repeat' => array(
					'instance' => sanitize_text_field( wp_unslash( $_POST['annual-repeat-instance'] ) ),
					'day'      => sanitize_text_field( wp_unslash( $_POST['annual-repeat-day'] ) ),
					'month'    => sanitize_text_field( wp_unslash( $_POST['annual-repeat-month'] ) ),
				),
			)
		);
	}
}


<?php
/**
 * Widget class and registration.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\WidgetAnnouncements;

/**
 * Register widget.
 *
 * @since 1.0.0
 */
function create_widget() {
	register_widget( __NAMESPACE__ . '\\Announcements_Widget' );
}

/**
 * Widget class.
 *
 * @since 1.0.0
 */
class Announcements_Widget extends \WP_Widget {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct(
			'azrcrv-wa',
			esc_html__( 'Widget Announcements by azurecurve', 'azrcrv-wa' ),
			array(
				'description' => esc_html__( 'Announcements in a widget', 'azrcrv-wa' ),
			)
		);
	}

	/**
	 * Display widget form in admin.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Current widget settings.
	 */
	public function form( $instance ) {

		$options = get_option_with_defaults( PLUGIN_HYPHEN );

		$widget_category = ! empty( $instance['category'] ) ? esc_attr( $instance['category'] ) : '';
		$width           = ! empty( $instance['width'] ) ? esc_attr( $instance['width'] ) : $options['widget']['width'];
		$height          = ! empty( $instance['height'] ) ? esc_attr( $instance['height'] ) : $options['widget']['height'];

		$categories = get_categories(
			array(
				'orderby'    => 'name',
				'hide_empty' => false,
				'taxonomy'   => 'announcement-category',
			)
		);
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>">
				<?php esc_html_e( 'Category:', 'azrcrv-wa' ); ?>
				<select id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>">
					<?php foreach ( $categories as $category ) : ?>
						<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php selected( $widget_category, $category->term_id ); ?>>
							<?php echo esc_html( $category->name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'width' ) ); ?>">
				<?php esc_html_e( 'Width:', 'azrcrv-wa' ); ?>&nbsp;
				<input type="number" id="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'width' ) ); ?>" class="small-text" value="<?php echo esc_attr( $width ); ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'height' ) ); ?>">
				<?php esc_html_e( 'Height:', 'azrcrv-wa' ); ?>&nbsp;
				<input type="number" id="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'height' ) ); ?>" class="small-text" value="<?php echo esc_attr( $height ); ?>" />
			</label>
		</p>

		<?php
	}

	/**
	 * Validate and sanitise widget form input.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance New settings.
	 * @param array $old_instance Old settings.
	 * @return array Updated settings.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance             = $old_instance;
		$instance['category'] = (int) $new_instance['category'];
		$instance['width']    = (int) $new_instance['width'];
		$instance['height']   = (int) $new_instance['height'];

		return $instance;
	}

	/**
	 * Display widget on front end.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args     Widget display arguments.
	 * @param array $instance Widget settings.
	 */
	public function widget( $args, $instance ) {

		$options = get_option_with_defaults( PLUGIN_HYPHEN );

		$width  = ! empty( $instance['width'] ) ? (int) $instance['width'] : (int) $options['widget']['width'];
		$height = ! empty( $instance['height'] ) ? (int) $instance['height'] : (int) $options['widget']['height'];

		// Explicit variable assignments replacing extract().
		$before_widget = $args['before_widget'];
		$after_widget  = $args['after_widget'];
		$before_title  = $args['before_title'];
		$after_title   = $args['after_title'];

		$year  = (int) current_time( 'Y' );
		$today = current_time( 'Y-m-d' );

		$announcements = get_posts(
			array(
				'post_type'   => 'widget-announcement',
				'numberposts' => -1,
				'orderby'     => 'date',
				'order'       => 'ASC',
				'tax_query'   => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array(
						'taxonomy'         => 'announcement-category',
						'field'            => 'term_id',
						'terms'            => $instance['category'],
						'include_children' => false,
					),
				),
				'post_status' => 'publish',
			)
		);

		foreach ( $announcements as $announcement ) {

			$repeat_details = get_post_meta( $announcement->ID, '_azrcrv_wa_repeat', true );
			$repeat_type    = isset( $repeat_details['type'] ) ? $repeat_details['type'] : 'none';

			$post_date_obj = date_create( $announcement->post_date );
			if ( ! $post_date_obj ) {
				continue;
			}

			$matches = (
				// Today — one-off.
				( date_format( $post_date_obj, 'Y-m-d' ) === $today && 'none' === $repeat_type )
				||
				// Annual repeat.
				( date_format( $post_date_obj, 'm-d' ) === current_time( 'm-d' ) && 'annual' === $repeat_type )
				||
				// Monthly repeat.
				( date_format( $post_date_obj, 'd' ) === current_time( 'd' ) && 'monthly' === $repeat_type )
				||
				// nth day of month repeat.
				( 'monthnday' === $repeat_type && $today === date( 'Y-m-d', strtotime( $repeat_details['month-repeat']['instance'] . ' ' . $repeat_details['month-repeat']['day'] . ' of ' . current_time( 'Y-m' ) ) ) )
				||
				// nth day of month annual repeat.
				( 'annualnday' === $repeat_type && $today === date( 'Y-m-d', strtotime( $repeat_details['annual-repeat']['instance'] . ' ' . $repeat_details['annual-repeat']['day'] . ' of ' . $year . '-' . $repeat_details['annual-repeat']['month'] ) ) )
				||
				// Good Friday.
				( 'goodfriday' === $repeat_type && date( 'Y-m-d', strtotime( '+' . ( easter_days( $year ) - 2 ) . ' days', strtotime( $year . '-03-21 12:00:00' ) ) ) === $today )
				||
				// Easter Sunday.
				( 'eastersunday' === $repeat_type && date( 'Y-m-d', easter_date( $year ) ) === $today )
				||
				// Easter Monday.
				( 'eastermonday' === $repeat_type && date( 'Y-m-d', strtotime( '+' . ( easter_days( $year ) + 1 ) . ' days', strtotime( $year . '-03-21 12:00:00' ) ) ) === $today )
			);

			if ( ! $matches ) {
				continue;
			}

			// Display widget title.
			echo $before_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $before_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wp_kses_post( apply_filters( 'widget_title', $announcement->post_title ) );
			echo $after_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			// Display widget body.
			$toggle_showhide_active = (
				1 === (int) $options['toggle-showhide']['integrate']
				&& is_plugin_active( 'azrcrv-toggle-showhide/azrcrv-toggle-showhide.php' )
				&& function_exists( 'azrcrv_tsh_display_toggle' )
			);

			$content = $announcement->post_content;
			$excerpt = $announcement->post_excerpt;

			if ( $toggle_showhide_active ) {
				$atts    = array( 'style' => 2 );
				$content = azrcrv_tsh_display_toggle( $atts, $content );
				$excerpt = azrcrv_tsh_display_toggle( $atts, $excerpt );
			}

			echo '<p>' . wp_kses_post( $content ) . '</p>';

			if ( has_post_thumbnail( $announcement->ID ) ) {
				$image = wp_get_attachment_image(
					get_post_thumbnail_id( $announcement->ID ),
					array( $width, $height ),
					'',
					array(
						'class' => 'img-responsive aligncenter',
						'alt'   => get_the_title( $announcement->ID ),
					)
				);
				echo '<div class="azrcrv-wa">' . $image . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			echo '<p>' . wp_kses_post( $excerpt ) . '</p>';

			echo $after_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

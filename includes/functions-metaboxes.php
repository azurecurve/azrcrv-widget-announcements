<?php
/**
 * Metabox functions.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\WidgetAnnouncements;

/**
 * Create the post tweet metabox.
 *
 * @since 1.2.0
 */
function create_tweet_metabox() {

	if ( ! is_plugin_active( 'azrcrv-to-twitter/azrcrv-to-twitter.php' ) ) {
		return;
	}

	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	if ( 1 === (int) $options['to-twitter']['integrate'] ) {
		add_meta_box(
			'azrcrv_wa_tweet_metabox',
			esc_html__( 'Tweet', 'azrcrv-wa' ),
			__NAMESPACE__ . '\\render_tweet_metabox',
			'widget-announcement',
			'normal',
			'default'
		);
	}
}

/**
 * Render the post tweet metabox markup.
 *
 * @since 1.2.0
 */
function render_tweet_metabox() {
	global $post;

	$post_tweet = get_post_meta( $post->ID, '_azrcrv_wa_post_tweet', true );
	$post_media = get_post_meta( $post->ID, '_azrcrv_wa_post_tweet_media', true );
	$no_image   = esc_url( plugins_url( '../assets/images/no-image.svg', __FILE__ ) );

	$tweet_media = array();
	for ( $media_loop = 0; $media_loop <= 3; $media_loop++ ) {
		if ( isset( $post_media[ $media_loop ] ) ) {
			$tweet_media[ $media_loop ] = array(
				'image' => esc_url( $post_media[ $media_loop ] ),
				'value' => esc_url( $post_media[ $media_loop ] ),
			);
		} else {
			$tweet_media[ $media_loop ] = array(
				'image' => $no_image,
				'value' => '',
			);
		}
	}
	?>

	<fieldset>
		<div class="azrcrv-wa-metabox-tweet">
			<p>
				<input
					type="text"
					name="post_tweet"
					id="post_tweet"
					class="large-text"
					value="<?php echo esc_attr( $post_tweet ); ?>"
				/>
			</p>
			<p><?php esc_html_e( 'To regenerate tweet, blank the field and update post.', 'azrcrv-wa' ); ?></p>

			<p class="azrcrv-wa-media-instructions">
				<?php esc_html_e( 'Select up to four images to include with tweet; if the Use Featured Image option is marked and a featured image is set, only the first three media images will be used.', 'azrcrv-wa' ); ?>
			</p>

			<div class="azrcrv-wa-media-grid">
				<?php foreach ( $tweet_media as $media_key => $media ) : ?>
					<?php $key = $media_key + 1; ?>
					<div class="azrcrv-wa-media-item">
						<img src="<?php echo esc_url( $media['image'] ); ?>" id="tweet-image-<?php echo esc_attr( $key ); ?>" class="azrcrv-wa-media-preview" />
						<input type="hidden" name="tweet-selected-image-<?php echo esc_attr( $key ); ?>" id="tweet-selected-image-<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $media['value'] ); ?>" class="regular-text" />
						<input type="button" id="azrcrv-wa-upload-image-<?php echo esc_attr( $key ); ?>" class="button upload" value="<?php esc_attr_e( 'Upload', 'azrcrv-wa' ); ?>" />
						&nbsp;
						<input type="button" id="azrcrv-wa-remove-image-<?php echo esc_attr( $key ); ?>" class="button remove" value="<?php esc_attr_e( 'Remove', 'azrcrv-wa' ); ?>" />
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</fieldset>

	<?php
	wp_nonce_field( 'azrcrv_wa_form_tweet_metabox_nonce', 'azrcrv_wa_form_tweet_metabox_process' );
}

/**
 * Save the post tweet metabox.
 *
 * @since 1.2.0
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 */
function save_tweet_metabox( $post_id, $post ) {

	if ( ! isset( $_POST['azrcrv_wa_form_tweet_metabox_process'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_key( $_POST['azrcrv_wa_form_tweet_metabox_process'] ), 'azrcrv_wa_form_tweet_metabox_nonce' ) ) {
		return $post->ID;
	}

	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return $post->ID;
	}

	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	if ( 0 === strlen( $_POST['post_tweet'] ) ) {

		$autopost_tweet  = get_post_meta( $post->ID, '_azrcrv_wa_tweet', true );
		$hashtags_string = isset( $autopost_tweet['hashtags'] ) ? $autopost_tweet['hashtags'] : '';

		$tweet_format = $options['to-twitter']['tweet-format'];
		if ( empty( $tweet_format ) ) {
			$tweet_format = '%t %h';
		}

		$post_tweet = str_replace( '%t', $post->post_title, $tweet_format );
		$post_tweet = str_replace( '%h', $hashtags_string, $post_tweet );

		if ( function_exists( 'azrcrv_tt_get_option' ) ) {
			$tt_options = azrcrv_tt_get_option( 'azrcrv-tt' );
			if ( isset( $tt_options['prefix_tweets_with_dot'] ) && 1 === (int) $tt_options['prefix_tweets_with_dot'] ) {
				if ( substr( $post_tweet, 0, 1 ) === '@' ) {
					$post_tweet = '.' . $post_tweet;
				}
			}
		}
	} else {
		$post_tweet = sanitize_text_field( wp_unslash( $_POST['post_tweet'] ) );
	}

	$media = array();
	for ( $media_loop = 1; $media_loop <= 4; $media_loop++ ) {
		if ( isset( $_POST[ 'tweet-selected-image-' . $media_loop ] ) && strlen( $_POST[ 'tweet-selected-image-' . $media_loop ] ) >= 1 ) {
			$media[] = esc_url_raw( wp_unslash( $_POST[ 'tweet-selected-image-' . $media_loop ] ) );
		}
	}

	update_post_meta( $post->ID, '_azrcrv_wa_post_tweet', $post_tweet );
	update_post_meta( $post->ID, '_azrcrv_wa_post_tweet_media', $media );
}

/**
 * Create the post tweet history metabox.
 *
 * @since 1.2.0
 */
function create_tweet_history_metabox() {
	global $post;

	if ( ! is_plugin_active( 'azrcrv-to-twitter/azrcrv-to-twitter.php' ) ) {
		return;
	}

	if ( ! isset( $post->ID ) || ! metadata_exists( 'post', $post->ID, '_azrcrv_tt_tweet_history' ) ) {
		return;
	}

	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	if ( 1 === (int) $options['to-twitter']['integrate'] ) {
		add_meta_box(
			'azrcrv_wa_tweet_history_metabox',
			esc_html__( 'Tweet History', 'azrcrv-wa' ),
			__NAMESPACE__ . '\\render_tweet_history_metabox',
			'widget-announcement',
			'normal',
			'default'
		);
	}
}

/**
 * Render the post tweet history metabox markup.
 *
 * @since 1.2.0
 */
function render_tweet_history_metabox() {
	global $post;
	?>

	<fieldset>
		<div class="azrcrv-wa-metabox-tweet-history">
			<?php if ( metadata_exists( 'post', $post->ID, '_azrcrv_tt_tweet_history' ) ) : ?>
				<strong><?php esc_html_e( 'Previous Tweets', 'azrcrv-wa' ); ?></strong><br />
				<?php foreach ( array_reverse( get_post_meta( $post->ID, '_azrcrv_tt_tweet_history', true ) ) as $key => $tweet ) : ?>
					<?php
					$tweet_detail = is_array( $tweet ) ? $tweet['tweet'] : $tweet;
					$tweet_date   = isset( $tweet['key'] ) ? $tweet['key'] : strtotime( $key );
					$tweet_date   = date_i18n( 'd/m/Y H:i', $tweet_date );

					if ( empty( $tweet['status'] ) ) {
						$status = '';
					} elseif ( 200 === (int) $tweet['status'] ) {
						$status = ' ' . esc_html( $tweet['status'] ) . ' ';
					} else {
						$status = ' <span class="azrcrv-wa-tweet-error">' . esc_html( $tweet['status'] ) . '</span> ';
					}

					$tweet_link = '';
					if ( isset( $tweet['author'] ) && strlen( $tweet['author'] ) > 0 ) {
						$tweet_link = '<a href="https://twitter.com/' . esc_attr( $tweet['author'] ) . '/status/' . esc_attr( $tweet['tweet_id'] ) . '" style="text-decoration: none;"><span class="dashicons dashicons-twitter"></span></a>&nbsp;';
					}
					?>
					&bull;&nbsp;<?php echo esc_html( $tweet_date ); ?> - <?php echo wp_kses_post( $status ); ?> - <em><?php echo wp_kses_post( $tweet_link . esc_html( $tweet_detail ) ); ?></em><br />
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</fieldset>

	<?php
}

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

/**
 * Add To Twitter autopost metabox to sidebar.
 *
 * @since 1.2.0
 */
function add_to_twitter_sidebar_metabox() {

	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	if ( 1 !== (int) $options['to-twitter']['integrate'] ) {
		return;
	}

	if ( ! is_plugin_active( 'azrcrv-to-twitter/azrcrv-to-twitter.php' ) ) {
		return;
	}

	add_meta_box(
		'azrcrv-wa-to-twitter-box',
		esc_html__( 'Autopost Tweet', 'azrcrv-wa' ),
		__NAMESPACE__ . '\\generate_to_twitter_sidebar_metabox',
		'widget-announcement',
		'side',
		'default'
	);
}

/**
 * Generate To Twitter sidebar metabox.
 *
 * @since 1.2.0
 */
function generate_to_twitter_sidebar_metabox() {
	global $post;

	$options        = get_option_with_defaults( PLUGIN_HYPHEN );
	$autopost_tweet = get_post_meta( $post->ID, '_azrcrv_wa_tweet', true );

	if ( is_array( $autopost_tweet ) ) {
		$use_featured_image = $autopost_tweet['use-featured-image'];
		$tweet              = $autopost_tweet['tweet'];
		$tweet_time         = $autopost_tweet['tweet-time'];
		$retweet            = $autopost_tweet['retweet'];
		$retweet_time       = $autopost_tweet['retweet-time'];
		$hashtags           = $autopost_tweet['hashtags'];
	} else {
		$use_featured_image = $options['to-twitter']['use-featured-image'];
		$tweet              = $options['to-twitter']['tweet'];
		$retweet            = $options['to-twitter']['retweet'];
		$tweet_time         = $options['to-twitter']['tweet-time'];
		$retweet_time       = $options['to-twitter']['retweet-time'];
		$hashtags           = '';
	}

	wp_nonce_field( basename( PLUGIN_FILE ), 'azrcrv-wa-to-twitter-sidebar-nonce' );
	?>

	<p>
		<label>
			<input type="checkbox" name="use-featured-image" value="1" <?php checked( 1, (int) $use_featured_image ); ?> />
			<?php esc_html_e( 'Use featured image as tweet media image 1?', 'azrcrv-wa' ); ?>
		</label>
	</p>

	<p>
		<label>
			<input type="checkbox" name="tweet" value="1" <?php checked( 1, (int) $tweet ); ?> />
			<?php esc_html_e( 'Tweet announcement?', 'azrcrv-wa' ); ?>
		</label>
	</p>

	<p>
		<?php esc_html_e( 'Tweet Time:', 'azrcrv-wa' ); ?>
		<input type="time" id="tweet-time" name="tweet-time" value="<?php echo esc_attr( $tweet_time ); ?>" required />
	</p>

	<p>
		<label>
			<input type="checkbox" name="retweet" value="1" <?php checked( 1, (int) $retweet ); ?> />
			<?php esc_html_e( 'Retweet announcement?', 'azrcrv-wa' ); ?>
		</label>
	</p>

	<p>
		<?php esc_html_e( 'Retweet Time:', 'azrcrv-wa' ); ?>
		<input type="time" id="retweet-time" name="retweet-time" value="<?php echo esc_attr( $retweet_time ); ?>" required />
	</p>

	<p>
		<label for="hashtags"><?php esc_html_e( 'Hashtags', 'azrcrv-wa' ); ?></label><br />
		<input name="hashtags" type="text" class="widefat" value="<?php echo esc_attr( $hashtags ); ?>" />
	</p>

	<?php
}

/**
 * Save To Twitter sidebar metabox.
 *
 * @since 1.2.0
 *
 * @param int $post_id The post ID.
 */
function save_to_twitter_sidebar_metabox( $post_id ) {

	if ( ! isset( $_POST['azrcrv-wa-to-twitter-sidebar-nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['azrcrv-wa-to-twitter-sidebar-nonce'] ), basename( PLUGIN_FILE ) ) ) {
		return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	$post_type = get_post_type( $post_id );

	if ( 'widget-announcement' !== $post_type ) {
		return;
	}

	$use_featured_image = isset( $_POST['use-featured-image'] ) ? 1 : 0;
	$tweet              = isset( $_POST['tweet'] ) ? 1 : 0;
	$tweet_time         = preg_replace( '([^0-9-:-])', '', wp_unslash( $_POST['tweet-time'] ) );
	$retweet            = isset( $_POST['retweet'] ) ? 1 : 0;
	$retweet_time       = preg_replace( '([^0-9-:-])', '', wp_unslash( $_POST['retweet-time'] ) );
	$hashtags           = sanitize_text_field( wp_unslash( $_POST['hashtags'] ) );

	$autopost_tweet = get_post_meta( $post_id, '_azrcrv_wa_tweet', true );

	if ( ! is_array( $autopost_tweet ) ) {
		$autopost_tweet = array(
			'tweeted-date'   => '1900-01-01',
			'retweeted-date' => '1900-01-01',
		);
	}

	$autopost_tweet['use-featured-image'] = $use_featured_image;
	$autopost_tweet['tweet']              = $tweet;
	$autopost_tweet['tweet-time']         = $tweet_time;
	$autopost_tweet['retweet']            = $retweet;
	$autopost_tweet['retweet-time']       = $retweet_time;
	$autopost_tweet['hashtags']           = $hashtags;

	update_post_meta( $post_id, '_azrcrv_wa_tweet', $autopost_tweet );
}

<?php
/**
 * Twitter integration functions.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\WidgetAnnouncements;

/**
 * Handle post status transition to 'publish'.
 *
 * @since 1.2.0
 *
 * @param string  $new_status New post status.
 * @param string  $old_status Old post status.
 * @param WP_Post $post       Post object.
 */
function post_status_transition( $new_status, $old_status, $post ) {

	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	if (
		'widget-announcement' === $post->post_type
		&& is_plugin_active( 'azrcrv-to-twitter/azrcrv-to-twitter.php' )
		&& 1 === (int) $options['to-twitter']['integrate']
		&& 'publish' === $new_status
		&& 'publish' !== $old_status
	) {
		check_tweet( $post->ID, $post );
	}
}

/**
 * Check and schedule tweet when post is published.
 *
 * @since 1.0.0
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 */
function check_tweet( $post_id, $post ) {
	remove_action( 'wp_insert_post', __NAMESPACE__ . '\\check_tweet', 12 );

	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	if (
		'widget-announcement' === $post->post_type
		&& is_plugin_active( 'azrcrv-to-twitter/azrcrv-to-twitter.php' )
		&& 1 === (int) $options['to-twitter']['integrate']
		&& 'publish' === $post->post_status
	) {
		check_tweet_today( $post_id, $post->post_date );
	}
}

/**
 * Execute a scheduled tweet or retweet for an announcement.
 *
 * @since 1.2.0
 *
 * @param int    $post_id The post ID.
 * @param string $type    'tweet' or 'retweet'.
 */
function perform_tweet_announcement( $post_id, $type ) {

	$post = get_post( $post_id );
	if ( ! $post || 'publish' !== $post->post_status ) {
		return;
	}

	$autopost_tweet = get_post_meta( $post_id, '_azrcrv_wa_tweet', true );

	if ( 'tweet' === $type ) {
		$autopost_tweet['tweeted-date'] = current_time( 'Y-m-d' );
	}
	if ( 'retweet' === $type ) {
		$autopost_tweet['retweeted-date'] = current_time( 'Y-m-d' );
	}
	update_post_meta( $post_id, '_azrcrv_wa_tweet', $autopost_tweet );

	$post_tweet   = get_post_meta( $post_id, '_azrcrv_wa_post_tweet', true );
	$media_to_use = array();

	if ( 1 === (int) $autopost_tweet['use-featured-image'] && has_post_thumbnail( $post_id ) ) {
		$media_to_use[] = get_the_post_thumbnail_url( $post_id, 'full' );
	}

	$post_media = get_post_meta( $post_id, '_azrcrv_wa_post_tweet_media', true );

	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	if ( 'retweet' === $type ) {
		$prefix = $options['to-twitter']['retweet-prefix'];
		if ( strlen( $prefix ) > 0 ) {
			$prefix .= ' ';
		}
	} else {
		$prefix = '';
	}

	$post_tweet = $prefix . $post_tweet;

	$parameters = array( 'status' => $post_tweet );

	if ( isset( $post_media ) && is_array( $post_media ) ) {
		$media_pos = 0;
		foreach ( $post_media as $media ) {
			$media_pos++;
			if ( $media_pos === 4 && count( $media_to_use ) > 0 && 1 === (int) $autopost_tweet['use-featured-image'] ) {
				break;
			} else {
				$media_to_use[] = $media;
			}
		}
		$parameters['media-urls'] = $media_to_use;
	} elseif ( count( $media_to_use ) > 0 ) {
		$parameters['media-urls'] = $media_to_use;
	}

	if ( ! function_exists( 'azrcrv_tt_post_tweet' ) ) {
		return;
	}

	$tweet_result = azrcrv_tt_post_tweet( $parameters );

	if ( ! function_exists( 'azrcrv_tt_get_option' ) ) {
		return;
	}

	$tt_options = azrcrv_tt_get_option( 'azrcrv-tt' );

	if ( isset( $tt_options['record_tweet_history'] ) && 1 === (int) $tt_options['record_tweet_history'] ) {
		$tweet_history = get_post_meta( $post_id, '_azrcrv_tt_tweet_history', true );
		if ( ! is_array( $tweet_history ) ) {
			$tweet_history = array();
		}
		$tweet_history[] = array(
			'key'       => time(),
			'date'      => current_time( 'Y-m-d' ),
			'time'      => current_time( 'H:i' ),
			'tweet_id'  => isset( $tweet_result['id'] ) ? $tweet_result['id'] : '',
			'author'    => isset( $tweet_result['screen_name'] ) ? $tweet_result['screen_name'] : '',
			'tweet'     => $post_tweet,
			'status'    => isset( $tweet_result['status'] ) ? $tweet_result['status'] : '',
		);
		update_post_meta( $post_id, '_azrcrv_tt_tweet_history', $tweet_history );
	}
}

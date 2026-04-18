<?php
/**
 * Cron functions.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\WidgetAnnouncements;

/**
 * Clear cron hourly check on plugin deactivation.
 *
 * @since 1.2.0
 */
function clear_cron_hourly() {
	wp_clear_scheduled_hook( 'azrcrv_wa_cron_hourly_check' );
}

/**
 * Clear cron for a single announcement.
 *
 * @since 1.2.0
 *
 * @param string $cron_name The cron hook name.
 * @param int    $post_id   The post ID.
 * @param string $type      The tweet type ('tweet' or 'retweet').
 */
function clear_cron_single( $cron_name, $post_id, $type ) {
	wp_clear_scheduled_hook( $cron_name, array( $post_id, $type ) );
}

/**
 * Perform cron hourly check across all announcements.
 *
 * @since 1.2.0
 */
function perform_cron_check() {

	$announcements = get_posts(
		array(
			'post_type'   => 'widget-announcement',
			'numberposts' => -1,
			'orderby'     => 'date',
			'order'       => 'ASC',
			'post_status' => 'publish',
		)
	);

	foreach ( $announcements as $announcement ) {
		check_tweet_today( $announcement->ID, $announcement->post_date );
	}
}

/**
 * Check whether a tweet should be scheduled for a given announcement today.
 *
 * @since 1.2.0
 *
 * @param int    $post_id   The post ID.
 * @param string $post_date The post date string.
 */
function check_tweet_today( $post_id, $post_date ) {

	$year           = (int) current_time( 'Y' );
	$today          = current_time( 'Y-m-d' );
	$repeat_details = get_post_meta( $post_id, '_azrcrv_wa_repeat', true );

	if ( ! is_array( $repeat_details ) ) {
		return;
	}

	$post_date_obj = date_create( $post_date );
	if ( ! $post_date_obj ) {
		return;
	}

	$matches = (
		// Today — one-off.
		( date_format( $post_date_obj, 'Y-m-d' ) === $today && 'none' === $repeat_details['type'] )
		||
		// Annual repeat.
		( date_format( $post_date_obj, 'm-d' ) === current_time( 'm-d' ) && 'annual' === $repeat_details['type'] )
		||
		// Monthly repeat.
		( date_format( $post_date_obj, 'd' ) === current_time( 'd' ) && 'monthly' === $repeat_details['type'] )
		||
		// nth day of month repeat.
		( 'monthnday' === $repeat_details['type'] && $today === date( 'Y-m-d', strtotime( $repeat_details['month-repeat']['instance'] . ' ' . $repeat_details['month-repeat']['day'] . ' of ' . current_time( 'Y-m' ) ) ) )
		||
		// nth day of month annual repeat.
		( 'annualnday' === $repeat_details['type'] && $today === date( 'Y-m-d', strtotime( $repeat_details['annual-repeat']['instance'] . ' ' . $repeat_details['annual-repeat']['day'] . ' of ' . $year . '-' . $repeat_details['annual-repeat']['month'] ) ) )
		||
		// Good Friday.
		( 'goodfriday' === $repeat_details['type'] && date( 'Y-m-d', strtotime( '+' . ( easter_days( $year ) - 2 ) . ' days', strtotime( $year . '-03-21 12:00:00' ) ) ) === $today )
		||
		// Easter Sunday.
		( 'eastersunday' === $repeat_details['type'] && date( 'Y-m-d', easter_date( $year ) ) === $today )
		||
		// Easter Monday.
		( 'eastermonday' === $repeat_details['type'] && date( 'Y-m-d', strtotime( '+' . ( easter_days( $year ) + 1 ) . ' days', strtotime( $year . '-03-21 12:00:00' ) ) ) === $today )
	);

	if ( ! $matches ) {
		return;
	}

	$autopost_tweet = get_post_meta( $post_id, '_azrcrv_wa_tweet', true );

	if ( ! is_array( $autopost_tweet ) ) {
		return;
	}

	if ( 1 === (int) $autopost_tweet['tweet'] && $autopost_tweet['tweeted-date'] < $today ) {
		$cron_name = 'azrcrv_wa_cron_tweet_announcement';
		$cron_type = 'tweet';
		clear_cron_single( $cron_name, $post_id, $cron_type );
		wp_schedule_single_event( strtotime( $autopost_tweet['tweet-time'] ), $cron_name, array( $post_id, $cron_type ) );
	}

	if ( 1 === (int) $autopost_tweet['retweet'] && $autopost_tweet['retweeted-date'] < $today ) {
		$cron_name = 'azrcrv_wa_cron_tweet_announcement';
		$cron_type = 'retweet';
		clear_cron_single( $cron_name, $post_id, $cron_type );
		wp_schedule_single_event( strtotime( $autopost_tweet['retweet-time'] ), $cron_name, array( $post_id, $cron_type ) );
	}
}

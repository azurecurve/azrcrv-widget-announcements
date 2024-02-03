<?php
/**
 * ------------------------------------------------------------------------------
 * Plugin Name:		Widget Announcements
 * Description:		Announce holidays, events, achievements and notable historical figures in a widget.
 * Version:			1.5.5
 * Requires CP:		1.0
 * Author:			azurecurve
 * Author URI:		https://development.azurecurve.co.uk/classicpress-plugins/
 * Plugin URI:		https://development.azurecurve.co.uk/classicpress-plugins/azrcrv-widget-announcements/
 * Donate link:		https://development.azurecurve.co.uk/support-development/
 * Text Domain:		widget-announcements
 * Domain Path:		/languages
 * License:			GPLv2 or later
 * License URI:		http://www.gnu.org/licenses/gpl-2.0.html
 * ------------------------------------------------------------------------------
 * This is free software released under the terms of the General Public License,
 * version 2, or later. It is distributed WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Full
 * text of the license is available at https://www.gnu.org/licenses/gpl-2.0.html.
 * ------------------------------------------------------------------------------
 */

// Prevent direct access.
if (!defined('ABSPATH')){
	die();
}

// include plugin menu
require_once(dirname( __FILE__).'/pluginmenu/menu.php');
add_action('admin_init', 'azrcrv_create_plugin_menu_wa');

// include update client
require_once(dirname(__FILE__).'/libraries/updateclient/UpdateClient.class.php');

/**
 * Setup actions, filters and shortcodes.
 *
 * @since 1.0.0
 *
 */
// register activation hook
register_activation_hook(__FILE__, 'azrcrv_wa_create_cron_hourly');
// register deactivation hook
register_deactivation_hook( __FILE__, 'azrcrv_wa_clear_cron_hourly' );

// add actions
add_action('admin_menu', 'azrcrv_wa_create_admin_menu');
add_action('init', 'azrcrv_wa_create_cust_taxonomy_for_custom_post');
add_action('init', 'azrcrv_wa_create_custom_post_type');
add_action('add_meta_boxes', 'azrcrv_wa_create_tweet_metabox');
add_action('save_post', 'azrcrv_wa_save_tweet_metabox', 11, 2);
add_action('add_meta_boxes', 'azrcrv_wa_create_tweet_history_metabox');
add_action('admin_menu', 'azrcrv_wa_add_sidebar_metabox');
add_action('save_post', 'azrcrv_wa_save_sidebar_metabox', 10, 1);
add_action('admin_menu', 'azrcrv_wa_add_to_twitter_sidebar_metabox');
add_action('save_post', 'azrcrv_wa_save_to_twitter_sidebar_metabox', 10, 1);
add_action('wp_insert_post', 'azrcrv_wa_check_tweet', 12, 2);
add_action('plugins_loaded', 'azrcrv_wa_load_languages');
add_action('wp_enqueue_scripts', 'azrcrv_wa_load_css');
add_action('widgets_init', 'azrcrv_wa_create_widget');
add_action('current_screen', 'azrcrv_wa_current_screen_callback');
add_action('admin_post_azrcrv_wa_save_options', 'azrcrv_wa_save_options');
add_action('azrcrv_wa_cron_hourly_check', 'azrcrv_wa_perform_cron_check');
add_action('azrcrv_wa_cron_tweet_announcement', 'azrcrv_wa_perform_tweet_announcement', 10, 2);
add_action('transition_post_status', 'azrcrv_wa_post_status_transition', 13, 3);
add_action('admin_enqueue_scripts', 'azrcrv_wa_load_admin_style');
add_action('admin_enqueue_scripts', 'azrcrv_wa_load_admin_jquery');
add_action('admin_enqueue_scripts', 'azrcrv_wa_media_uploader');

// add filters
add_filter('plugin_action_links', 'azrcrv_wa_add_plugin_action_link', 10, 2);
add_filter('codepotent_update_manager_image_path', 'azrcrv_wa_custom_image_path');
add_filter('codepotent_update_manager_image_url', 'azrcrv_wa_custom_image_url');

/**
 * Custom plugin image path.
 *
 * @since 1.12.0
 *
 */
function azrcrv_wa_custom_image_path($path){
    if (strpos($path, 'azrcrv-widget-announcements') !== false){
        $path = plugin_dir_path(__FILE__).'assets/pluginimages';
    }
    return $path;
}

/**
 * Custom plugin image url.
 *
 * @since 1.12.0
 *
 */
function azrcrv_wa_custom_image_url($url){
    if (strpos($url, 'azrcrv-widget-announcements') !== false){
        $url = plugin_dir_url(__FILE__).'assets/pluginimages';
    }
    return $url;
}

/**
 * Load language files.
 *
 * @since 1.0.0
 *
 */
function azrcrv_wa_load_languages(){
    $plugin_rel_path = basename(dirname(__FILE__)).'/languages';
    load_plugin_textdomain('widget-announcements', false, $plugin_rel_path);
}

/**
 * Load plugin css.
 *
 * @since 1.0.0
 *
 */
function azrcrv_wa_load_css(){
	wp_enqueue_style('azrcrv-wa', plugins_url('assets/css/style.css', __FILE__));
}

/**
 * Load admin css.
 *
 * @since 1.2.0
 *
 */
function azrcrv_wa_load_admin_style(){
	
	global $pagenow;
	
	if ($pagenow == 'admin.php' AND $_GET['page'] == 'azrcrv-wa'){
		wp_register_style('azrcrv-wa-admin-css', plugins_url('assets/css/admin.css', __FILE__), false, '1.0.0');
		wp_enqueue_style('azrcrv-wa-admin-css');
	}
}

/**
 * Load admin jQuery.
 *
 * @since 1.0.0
 *
 */
function azrcrv_wa_load_admin_jquery(){
	
	global $pagenow;
	
	if ($pagenow == 'admin.php' AND $_GET['page'] == 'azrcrv-wa'){
		wp_enqueue_script('azrcrv-wa-admin-jquery', plugins_url('assets/jquery/admin.js', __FILE__), array('jquery'));
	}
}

/**
 * Load media uploaded.
 *
 * @since 1.0.0
 *
 */
function azrcrv_wa_media_uploader(){
	global $post_type;
	
	if(function_exists('wp_enqueue_media')){
		wp_enqueue_media();
	}else{
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');
	}
}

/**
 * Get options including defaults.
 *
 * @since 1.1.0
 *
 */
function azrcrv_wa_get_option($option_name){
 
	$defaults = array(
						'widget' => array(
											'width' => 300,
											'height' => 300,
										),
						'to-twitter' => array(
												'integrate' => 0,
												'tweet' => 0,
												'retweet' => 0,
												'retweet-prefix' => 'ICYMI:',
												'tweet-format' => '%t %h',
												'tweet-time' => '10:00',
												'retweet-time' => '16:00',
												'use-featured-image' => 1,
											),
						'toggle-showhide' => array(
												'integrate' => 0,
											),
					);

	$options = get_option($option_name, $defaults);

	$options = azrcrv_wa_recursive_parse_args($options, $defaults);

	return $options;

}

/**
 * Recursively parse options to merge with defaults.
 *
 * @since 1.1.0
 *
 */
function azrcrv_wa_recursive_parse_args( $args, $defaults ) {
	$new_args = (array) $defaults;

	foreach ( $args as $key => $value ) {
		if ( is_array( $value ) && isset( $new_args[ $key ] ) ) {
			$new_args[ $key ] = azrcrv_wa_recursive_parse_args( $value, $new_args[ $key ] );
		}
		else {
			$new_args[ $key ] = $value;
		}
	}

	return $new_args;
}

/**
 * Create custom snippet post type.
 *
 * @since 1.0.0
 *
 */
function azrcrv_wa_create_cust_taxonomy_for_custom_post() {

	register_taxonomy(
						'announcement-category',
						'widget-announcement',
						array(
						'label' => esc_html__( 'Category' ),
						'rewrite' => array( 'slug' => 'announcement-category' ),
						'hierarchical' => true,
					)
	);

}

/**
 * Create custom announcement post type.
 *
 * @since 1.0.0
 *
 */
function azrcrv_wa_create_custom_post_type(){
	register_post_type('widget-announcement',
		array(
				'labels' => array(
									'name' => esc_html__('Announcements', 'widget-announcements'),
									'singular_name' => esc_html__('Announcement', 'widget-announcements'),
									'add_new' => esc_html__('Add New', 'widget-announcements'),
									'add_new_item' => esc_html__('Add New Announcement', 'widget-announcements'),
									'edit' => esc_html__('Edit', 'widget-announcements'),
									'edit_item' => esc_html__('Edit Announcement', 'widget-announcements'),
									'new_item' => esc_html__('New Announcement', 'widget-announcements'),
									'view' => esc_html__('View', 'widget-announcements'),
									'view_item' => esc_html__('View Announcement', 'widget-announcements'),
									'search_items' => esc_html__('Search Announcement', 'widget-announcements'),
									'not_found' => esc_html__('No Announcement found', 'widget-announcements'),
									'not_found_in_trash' => esc_html__('No Announcement found in Trash', 'widget-announcements'),
									'parent' => esc_html__('Parent Announcement', 'widget-announcements')
								),
			'public' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'menu_position' => 50,
			'supports' => array('title', 'revisions', 'editor', 'excerpt', 'thumbnail'),
			'taxonomies' => array('announcement-category'),
			'menu_icon' => 'dashicons-megaphone',
			'has_archive' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_admin_bar' => true,
			'show_in_nav_menus' => false,
			'show_in_rest' => false,
		)
	);
}

/**
 * Make sure labels only changes for this post type
 *
 * @since 1.0.1
 *
 */
function azrcrv_wa_current_screen_callback($screen) {
    if( is_object($screen) && $screen->post_type == 'widget-announcement' ) {
        add_filter( 'gettext', 'azrcrv_wa_admin_post_excerpt_change_labels', 99, 3 );
    }
}

/**
 * Change labels in the excerpt box
 *
 * @since 1.0.0
 *
 */ 
function azrcrv_wa_admin_post_excerpt_change_labels($translation, $original){
	if ('Excerpt' == $original){
		return esc_html__('Text after announcement', 'widget-announcements');
	}else{
		$pos = strpos($original, 'Excerpts are optional hand-crafted summaries of your');

		if ($pos !== false){
			return  '';
		}
	}
	
	return $translation;
}

/**
 * Create the post tweet metabox
 *
 * @since 1.2.0
 *
 */
function azrcrv_wa_create_tweet_metabox() {
	
	$to_twitter_enabled = azrcrv_wa_is_plugin_active('azrcrv-to-twitter/azrcrv-to-twitter.php');
	
	if ($to_twitter_enabled){
		
		$options = azrcrv_wa_get_option('azrcrv-wa');
		
		if ($options['to-twitter']['integrate'] == 1){
			add_meta_box(
				'azrcrv_wa_tweet_metabox', // Metabox ID
				'Tweet', // Title to display
				'azrcrv_wa_render_tweet_metabox', // Function to call that contains the metabox content
				'widget-announcement', // Post type to display metabox on
				'normal', // Where to put it (normal = main colum, side = sidebar, etc.)
				'default' // Priority relative to other metaboxes
			);
		}
	}
}

/**
 * Render the post tweet metabox markup
 *
 * @since 1.2.0
 *
 */
function azrcrv_wa_render_tweet_metabox() {
	// Variables
	global $post; // Get the current post data
	$post_tweet = get_post_meta($post->ID, '_azrcrv_wa_post_tweet', true); // Get the saved values
	$post_media = get_post_meta($post->ID, '_azrcrv_wa_post_tweet_media', true); // Get the saved values
	
	?>

		<fieldset>
			<div>
				<table style="width: 100%; border-collapse: collapse;">
					<tr>
						<td style="width: 100%;">
							<p>
								<input
									type="text"
									name="post_tweet"
									id="post_tweet"
									class="large-text"
									value="<?php echo esc_attr($post_tweet); ?>"
								>
							</p>
							<p>
								<?php printf(esc_html__('To regenerate tweet blank the field and update post.', 'widget-announcements'), '%s'); ?>
							</p>
		
							<p>
								<?php
									$no_image = plugin_dir_url(__FILE__).'assets/images/no-image.svg';
									$tweet_media = array();
									for ($media_loop = 0; $media_loop <= 3; $media_loop++){
										if (isset($post_media[$media_loop])){
											$tweet_media[$media_loop] = array(
																				'image' => $post_media[$media_loop],
																				'value' => $post_media[$media_loop],
																			);
										}else{
											$tweet_media[$media_loop] = array(
																				'image' => $no_image,
																				'value' => '',
																			);
										}
									}
								?>
								
								<p style="clear: both; " />
								
								<div style="width: 100%; display: block; ">
									<div style="width: 100%; display: block; padding-bottom: 12px; ">
										<?php esc_html_e('Select up to four images to include with tweet; if the <em>Use Featured Image</em> option is marked and a featured image set, only the first three media images from below will be used.', 'widget-announcements'); ?>
									</div>
									<?php
										foreach ($tweet_media AS $media_key => $media){
											$key = $media_key + 1;
											echo '<div style="float: left; width: 170px; text-align: center; ">';
												echo '<img src="'.$media['image'].'" id="tweet-image-'.$key.'" style="width: 160px;"><br />';
												echo '<input type="hidden" name="tweet-selected-image-'.$key.'" id="tweet-selected-image-'.$key.'" value="'.$media['value'].'" class="regular-text" />';
												echo '<input type="button" id="azrcrv-wa-upload-image-'.$key.'" class="button upload" value="'.esc_html__('Upload', 'widget-announcements').'" />&nbsp;';
												echo '<input type="button" id="azrcrv-wa-remove-image-'.$key.'" class="button remove" value="'.esc_html__( 'Remove', 'widget-announcements').'" />';
											echo '</div>';
										}
									?>
								</div>
								
								<p style="clear: both; padding-bottom: 6px; " />
							</p>
						<td>
					</tr>
				</table>
			</div>
		</fieldset>

	<?php
	// Security field
	// This validates that submission came from the
	// actual dashboard and not the front end or
	// a remote server.
	wp_nonce_field('azrcrv_wa_form_tweet_metabox_nonce', 'azrcrv_wa_form_tweet_metabox_process');
}

/**
 * Save the post tweet metabox
 * @param  Number $post_id The post ID
 * @param  Array  $post    The post data
 *
 * @since 1.2.0
 *
 */
function azrcrv_wa_save_tweet_metabox( $post_id, $post ) {

	// Verify that our security field exists. If not, bail.
	if ( !isset( $_POST['azrcrv_wa_form_tweet_metabox_process'] ) ) return;

	// Verify data came from edit/dashboard screen
	if ( !wp_verify_nonce( $_POST['azrcrv_wa_form_tweet_metabox_process'], 'azrcrv_wa_form_tweet_metabox_nonce' ) ) {
		return $post->ID;
	}

	// Verify user has permission to edit post
	if (!current_user_can( 'edit_post', $post->ID)){
		return $post->ID;
	}
	
	$tt_options = azrcrv_tt_get_option('azrcrv-tt');
	$options = azrcrv_wa_get_option('azrcrv-wa');
	
	if (strlen($_POST['post_tweet']) == 0){
		
		$autopost_tweet = get_post_meta($post->ID, '_azrcrv_wa_tweet', true);
		$hashtags_string = $autopost_tweet['hashtags'];
		
		$tweet = $post->post_title;
		
		$post_tweet = $options['to-twitter']['tweet-format'];
		
		if (!isset($post_tweet)||$post_tweet == ''){
			$post_tweet = '%t %h';
		}
		
		$post_tweet = str_replace('%t', $tweet, $post_tweet);
		$post_tweet = str_replace('%h', $hashtags_string, $post_tweet);
		
		if ($tt_options['prefix_tweets_with_dot'] == 1){
			if (substr($post_tweet, 0, 1) == '@'){
				$post_tweet = '.'.$post_tweet;
			}
		}
	}else{
		/**
		 * Sanitize the submitted data
		 */
		$post_tweet = sanitize_text_field($_POST['post_tweet']);
	}
	
	$media = array();
	for ($media_loop = 1; $media_loop <= 4; $media_loop++){
		if(strlen($_POST['tweet-selected-image-'.$media_loop]) >= 1){
			$media[] = $_POST['tweet-selected-image-'.$media_loop];
		}
	}
	
	// Save our submissions to the database
	update_post_meta($post->ID, '_azrcrv_wa_post_tweet', $post_tweet);
	update_post_meta($post->ID, '_azrcrv_wa_post_tweet_media', $media);

}

/**
 * Create the post tweet history metabox
 *
 * @since 1.2.0
 *
 */
function azrcrv_wa_create_tweet_history_metabox() {
	
	global $post; // Get the current post data
	
	$to_twitter_enabled = azrcrv_wa_is_plugin_active('azrcrv-to-twitter/azrcrv-to-twitter.php');
	
	if ($to_twitter_enabled){
		if(metadata_exists('post', $post->ID, '_azrcrv_tt_tweet_history')) {
		
			$options = azrcrv_wa_get_option('azrcrv-wa');
			
			if ($options['to-twitter']['integrate'] == 1){
				add_meta_box(
					'azrcrv_wa_tweet_history_metabox', // Metabox ID
					'Tweet History', // Title to display
					'azrcrv_wa_render_tweet_history_metabox', // Function to call that contains the metabox content
					'widget-announcement', // Post type to display metabox on
					'normal', // Where to put it (normal = main colum, side = sidebar, etc.)
					'default' // Priority relative to other metaboxes
				);
			}
		}
	}
}

/**
 * Render the post tweet history metabox markup
 *
 * @since 1.2.0
 *
 */
function azrcrv_wa_render_tweet_history_metabox() {
	// Variables
	global $post; // Get the current post data
	
	?>

		<fieldset>
			<div>
				<table style="width: 100%; border-collapse: collapse;">
					<tr>
						<td style="width: 100%;">
							<p>
							<?php
							if(metadata_exists('post', $post->ID, '_azrcrv_tt_tweet_history')) {
								echo '<strong>'.esc_html__('Previous Tweets', 'widget-announcements').'</strong><br />';
								foreach(array_reverse(get_post_meta($post->ID, '_azrcrv_tt_tweet_history', true )) as $key => $tweet){
									if (is_array($tweet)){ $tweet_detail = $tweet['tweet']; }else{ $tweet_detail = $tweet; }
									
									if (isset($tweet['key'])){ $tweet_date = $tweet['key']; }else{ $tweet_date = strtotime($key); }
									$tweet_date = date('d/m/Y H:i', $tweet_date);
									
									if ($tweet['status'] == ''){
										$status = '';
									}elseif ($tweet['status'] == 200){
										$status = ' '.$tweet['status'].' ';
									}else{
										$status = ' <span style="color: red; font-weight:900;">'.$tweet['status'].'</span> ';
									}
									
									if (isset($tweet['author']) AND strlen($tweet['author']) > 0){
										$tweet_link = '<a href="https://twitter.com/'.$tweet['author'].'/status/'.$tweet['tweet_id'].'" style="text-decoration: none; "><span class="dashicons dashicons-twitter"></span></a>&nbsp';
									}else{
										$tweet_link = '';
									}
									
									echo '•&nbsp;'.$tweet_date.' - '.$status.' - <em>'.$tweet_link.$tweet_detail.'</em><br />';
								}	
							}
							?>
							</p>
						<td>
					</tr>
				</table>
			</div>
		</fieldset>

	<?php
}

/**
 * Add post metabox to sidebar.
 *
 * @since 1.0.0
 *
 */
function azrcrv_wa_add_sidebar_metabox(){
	add_meta_box('azrcrv-wa-box', esc_html__('Repeat announcement', 'widget-announcement'), 'azrcrv_wa_generate_sidebar_metabox', array('widget-announcement'), 'side', 'default');	
}

/**
 * Generate post sidebar metabox.
 *
 * @since 1.0.0
 *
 */
function azrcrv_wa_generate_sidebar_metabox(){
	
	global $post;
	
	wp_nonce_field(basename(__FILE__), 'azrcrv-wa-sidebar-nonce');
	
	$repeat = get_post_meta($post->ID, '_azrcrv_wa_repeat', true);
	
	?>
	
	<fieldset>
	
		<p>
			<input type="radio" id="none" name="repeat-type" value="none" <?php if (!isset($repeat['type']) OR isset($repeat['type']) AND $repeat['type'] == 'none'){ echo 'checked'; } ?>><label for="none"><?php echo esc_html__('No repeat', 'widget-announcement'); ?></label>
		</p>
	
		<p>
			<input type="radio" id="monthly" name="repeat-type" value="monthly" <?php if (isset($repeat['type']) AND $repeat['type'] == 'monthly'){ echo 'checked'; } ?>><label for="monthly"><?php echo esc_html__('Repeat monthly', 'widget-announcement'); ?></label>
		</p>
	
		<p>
			<input type="radio" id="annual" name="repeat-type" value="annual" <?php if (isset($repeat['type']) AND $repeat['type'] == 'annual'){ echo 'checked'; } ?>><label for="annual"><?php echo esc_html__('Repeat annually', 'widget-announcement'); ?></label>
		</p>
		
		<p>
			<input type="radio" id="goodfriday" name="repeat-type" value="goodfriday" <?php if (isset($repeat['type']) AND $repeat['type'] == 'goodfriday'){ echo 'checked'; } ?>><label for="goodfriday"><?php echo esc_html__('Repeat on Good Friday', 'widget-announcement'); ?></label>
		</p>
		
		<p>
			<input type="radio" id="eastersunday" name="repeat-type" value="eastersunday" <?php if (isset($repeat['type']) AND $repeat['type'] == 'eastersunday'){ echo 'checked'; } ?>><label for="eastersunday"><?php echo esc_html__('Repeat on Easter Sunday', 'widget-announcement'); ?></label>
		</p>
		
		<p>
			<input type="radio" id="eastermonday" name="repeat-type" value="eastermonday" <?php if (isset($repeat['type']) AND $repeat['type'] == 'eastermonday'){ echo 'checked'; } ?>><label for="eastermonday"><?php echo esc_html__('Repeat on Easter Monday', 'widget-announcement'); ?></label>
		</p>
		
		<?php
			$instance = array(
							'first' => esc_html__('1st', 'widget-announcement'),
							'second' => esc_html__('2nd', 'widget-announcement'),
							'third' => esc_html__('3rd', 'widget-announcement'),
							'fourth' => esc_html__('4th', 'widget-announcement'),
						);
			
			$days = array(
							'Sunday' => esc_html__('Sunday', 'widget-announcement'),
							'Monday' => esc_html__('Monday', 'widget-announcement'),
							'Tuesday' => esc_html__('Tuesday', 'widget-announcement'),
							'Wednesday' => esc_html__('Wednesday', 'widget-announcement'),
							'Thursday' => esc_html__('Thursday', 'widget-announcement'),
							'Friday' => esc_html__('Friday', 'widget-announcement'),
							'Saturday' => esc_html__('Saturday', 'widget-announcement'),
						);
			
			$months = array(
							1 => esc_html__('Jan', 'widget-announcement'),
							2 => esc_html__('Feb', 'widget-announcement'),
							3 => esc_html__('Mar', 'widget-announcement'),
							4 => esc_html__('Apr', 'widget-announcement'),
							5 => esc_html__('May', 'widget-announcement'),
							6 => esc_html__('Jun', 'widget-announcement'),
							7 => esc_html__('Jul', 'widget-announcement'),
							8 => esc_html__('Aug', 'widget-announcement'),
							9 => esc_html__('Sept', 'widget-announcement'),
							10 => esc_html__('Oct', 'widget-announcement'),
							11 => esc_html__('Nov', 'widget-announcement'),
							12 => esc_html__('Dec', 'widget-announcement'),
						);
		?>
		
		<p>
			<input type="radio" id="monthnday" name="repeat-type" value="monthnday" <?php if (isset($repeat['type']) AND $repeat['type'] == 'monthnday'){ echo 'checked'; } ?>><label for="monthnday"><?php echo esc_html__('Repeat monthly on <em>n day</em> of month', 'widget-announcement'); ?></label>
			
			<?php
				echo '<span style="margin-left: 20px; "><select name="month-repeat-instance">';
				foreach ($instance as $instance_number => $instance_name){
						// instance
						if ($repeat['month-repeat']['instance'] == $instance_number){
							$selected = 'selected';
						}else{
							$selected = '';
						}
						echo '<option value="'.$instance_number.'" '.$selected.' >'.$instance_name.'</option>';
				}
				echo '</select>';
				
				echo "&nbsp;";
				
				echo '<select name="month-repeat-day">';
				foreach ($days as $day_number => $day_name){
						// day
						if ($repeat['month-repeat']['day'] == $day_number){
							$selected = 'selected';
						}else{
							$selected = '';
						}
						echo '<option value="'.$day_number.'" '.$selected.' >'.$day_name.'</option>';
				}
				echo '</select></span>';
			?>
		</p>
		
		<p>
			<input type="radio" id="annualnday" name="repeat-type" value="annualnday" <?php if (isset($repeat['type']) AND $repeat['type'] == 'annualnday'){ echo 'checked'; } ?>><label for="annualnday"><?php echo esc_html__('Repeat annually on <em>n day</em> of month', 'widget-announcement'); ?></label>
			
			<?php
				echo '<span style="margin-left: 20px; "><select name="annual-repeat-instance">';
				foreach ($instance as $instance_number => $instance_name){
						// instance
						if ($repeat['annual-repeat']['instance'] == $instance_number){
							$selected = 'selected';
						}else{
							$selected = '';
						}
						echo '<option value="'.$instance_number.'" '.$selected.' >'.$instance_name.'</option>';
				}
				echo '</select>';
				
				echo "&nbsp;";
				
				echo '<select name="annual-repeat-day">';
				foreach ($days as $day_number => $day_name){
						// day
						if ($repeat['annual-repeat']['day'] == $day_number){
							$selected = 'selected';
						}else{
							$selected = '';
						}
						echo '<option value="'.$day_number.'" '.$selected.' >'.$day_name.'</option>';
				}
				echo '</select></span>';
				
				echo "&nbsp;";
				
				echo '<select name="annual-repeat-month">';
				foreach ($months as $month_number => $month_name){
						// month
						if ($repeat['annual-repeat']['month'] == $month_number){
							$selected = 'selected';
						}else{
							$selected = '';
						}
						echo '<option value="'.$month_number.'" '.$selected.' >'.$month_name.'</option>';
				}
				echo '</select></span>';
			?>
		</p>
		
	</fieldset>
	
	<?php
}

/**
 * Save sidebar metabox.
 *
 * @since 1.0.0
 *
 */
function azrcrv_wa_save_sidebar_metabox($post_id){
	
	if(! isset($_POST[ 'azrcrv-wa-sidebar-nonce' ]) || ! wp_verify_nonce($_POST[ 'azrcrv-wa-sidebar-nonce' ], basename(__FILE__))){
		return $post_id;
	}
	
	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return $post_id;
	
	if(! current_user_can('edit_post', $post_id)){
		return $post_id;
	}
	
	$post_type = get_post_type($post_ID);
	
    if ($post_type == 'widget-announcement'){
		update_post_meta($post_id, '_azrcrv_wa_repeat', array(
																'type' => sanitize_text_field($_POST['repeat-type']),
																'month-repeat' => array(
																									'instance' => sanitize_text_field($_POST['month-repeat-instance']),
																									'day' => sanitize_text_field($_POST['month-repeat-day']),
																								),
																'annual-repeat' => array(
																									'instance' => sanitize_text_field($_POST['annual-repeat-instance']),
																									'day' => sanitize_text_field($_POST['annual-repeat-day']),
																									'month' => sanitize_text_field($_POST['annual-repeat-month']),
																								),
															)
						);
	}
	
	return esc_attr($_POST[ 'autopost' ]);
}

/**
 * Add action link on plugins page.
 *
 * @since 1.0.0
 *
 */
function azrcrv_wa_add_plugin_action_link($links, $file){
	static $this_plugin;

	if (!$this_plugin){
		$this_plugin = plugin_basename(__FILE__);
	}

	if ($file == $this_plugin){
		$settings_link = '<a href="'.admin_url('admin.php?page=azrcrv-wa').'"><img src="'.plugins_url('/pluginmenu/images/logo.svg', __FILE__).'" style="padding-top: 2px; margin-right: -5px; height: 16px; width: 16px;" alt="azurecurve" />'.esc_html__('Settings' ,'widget-announcements').'</a>';
		array_unshift($links, $settings_link);
	}

	return $links;
}

/**
 * Add to menu.
 *
 * @since 1.0.0
 *
 */
function azrcrv_wa_create_admin_menu(){
	
	// add settings to announcements submenu
	add_submenu_page(
						'edit.php?post_type=widget-announcement'
						,esc_html__('Widget Announcement Settings', 'widget-announcements')
						,esc_html__('Settings', 'widget-announcements')
						,'manage_options'
						,'azrcrv-wa'
						,'azrcrv_wa_display_options'
					);
	
	// add settings to azurecurve menu
	add_submenu_page(
						"azrcrv-plugin-menu"
						,esc_html__("Widget Announcements Settings", "widget-announcements")
						,esc_html__("Widget Announcements", "widget-announcements")
						,'manage_options'
						,'azrcrv-wa'
						,'azrcrv_wa_display_options'
					);
}

/**
 * Display Settings page.
 *
 * @since 1.0.0
 *
 */
function azrcrv_wa_display_options(){
	if (!current_user_can('manage_options')){
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'widget-announcements'));
    }
	
	// Retrieve plugin configuration options from database
	$options = azrcrv_wa_get_option('azrcrv-wa');
	
	$to_twitter_enabled = azrcrv_wa_is_plugin_active('azrcrv-to-twitter/azrcrv-to-twitter.php');
	$toggle_showhide_enabled = azrcrv_wa_is_plugin_active('azrcrv-toggle-showhide/azrcrv-toggle-showhide.php');
	
	?>
	<div id="azrcrv-wa-general" class="wrap azrcrv-wa">
		<fieldset>
			<h1>
				<?php
					echo '<a href="https://development.azurecurve.co.uk/classicpress-plugins/"><img src="'.plugins_url('/pluginmenu/images/logo.svg', __FILE__).'" style="padding-right: 6px; height: 20px; width: 20px;" alt="azurecurve" /></a>';
					_e(get_admin_page_title());
				?>
			</h1>
			<?php if(isset($_GET['settings-updated'])){ ?>
				<div class="notice notice-success is-dismissible">
					<p><strong><?php esc_html_e('Settings have been saved.', 'widget-announcements'); ?></strong></p>
				</div>
			<?php } ?>
			<form method="post" action="admin-post.php">
				<input type="hidden" name="action" value="azrcrv_wa_save_options" />
				<input name="page_options" type="hidden" value="width,height" />
				
				<!-- Adding security through hidden referrer field -->
				<?php wp_nonce_field('azrcrv-wa', 'azrcrv-wa-nonce'); ?>
				
				<p>
					<?php printf(esc_html__('%s allows you to add a widget which can be used to announce holidays, events, achievements and notable historical figures in a widget.', 'widget-announcements'), 'Widget Announcements'); ?>
				</p>
				
				<p>
					<?php
						_e('Announcements can be made:
					<ul>
					<li>One off</li>
					<li>Monthly</li>
					<li>Annually</li>
					<li>Good Friday</li>
					<li>Easter Sunday</li>
					<li>Easter Monday</li>
					<li>Monthly on the nth day (e.g. 2nd Wednesday)</li>
					<li>Annually on the nth day of the month (e.g. 4th Thursday November)</li>
					</ul>', 'widget-announcements');
					?>
				</p>
				<p> 
					<?php
						_e('Announcements are created as a custom post type and can have details, an image and additional text after the image; images should be narrower than your widget area.', 'widget-announcements');
					?>
				</p>
				<p>
					<?php
						_e('When creating widgets they can be added to one or more categories; when adding a widget, select the category to include.', 'widget-announcements');
					?>
				</p>
				<p>
					<?php printf(esc_html__('Integration with the %s plugin from %s can be enabled to send announcements to Twitter.'), '<a href="https://development.azurecurve.co.uk/classicpress-plugins/to-twitter/">To Twitter</a>', '<a href="https://development.azurecurve.co.uk/classicpress-plugins/">azurecurve</a>'); ?>
				</p>
				<p>
					<?php printf(esc_html__('Integration with the %s plugin from %s can be enabled to use the &lt;!--readmore--&gt; tag in the <em>content</em> and <em>text after announcement</em> fields.'), '<a href="https://development.azurecurve.co.uk/classicpress-plugins/toggle-showhide/">Toggle Show/Hide</a>', '<a href="https://development.azurecurve.co.uk/classicpress-plugins/">azurecurve</a>'); ?>
				</p>
				
				<?php
				if ($to_twitter_enabled AND $options['to-twitter']['integrate'] == 1){
				
					if(isset($_GET['i'])){
							$tab1active = '';
							$tab2active = 'azrcrv-wa-nav-tab-active';
							$tab1visibility = 'azrcrv-wa-tab-invisible';
							$tab2visibility = '';
						}else{
							$tab1active = 'azrcrv-wa-nav-tab-active';
							$tab2active = '';
							$tab1visibility = '';
							$tab2visibility = 'azrcrv-wa-tab-invisible';
						}
					?>
					<h2 class="azrcrv-wa-nav-wrapper">
						<a class="azrcrv-wa-nav-tab <?php echo $tab1active; ?>" data-item=".tabs-1" href="#tabs-1"><?php esc_html_e('Settings', 'widget-announcements') ?></a>
						<a class="azrcrv-wa-nav-tab <?php echo $tab2active; ?>" data-item=".tabs-2" href="#tabs-2"><?php esc_html_e('To Twitter Integration', 'widget-announcements') ?></a>
					</h2>
					
					<div class='azrcrv-wa-tab-wrapper'>
						<div class="azrcrv-wa-tab tabs-1 <?php echo $tab1visibility; ?>">
				<?php } ?>
				
							<table class="form-table">
								
								<tr>
									<th>
										<h3><?php esc_html_e('Widget Defaults', 'widget-announcements'); ?></h3>
									</th>
								</tr>
								
								<tr>
									<th scope="row"><label for="widget-width">
										<?php esc_html_e('Width', 'widget-announcements'); ?></label>
									</th>
									<td>
										<input name="widget-width" type="number" min="1" id="widget-width" value="<?php if (strlen($options['widget']['width']) > 0){ echo sanitize_text_field($options['widget']['width']); } ?>" class="small-text" /> px
									</td>
								</tr>
								
								<tr>
									<th scope="row"><label for="widget-height">
										<?php esc_html_e('Height', 'widget-announcements'); ?></label>
									</th>
									<td>
										<input name="widget-height" type="number" min="1" id="widget-height" value="<?php if (strlen($options['widget']['height']) > 0){ echo sanitize_text_field($options['widget']['height']); } ?>" class="small-text" /> px
									</td>
								</tr>
								
								<tr>
									<th colspan=2>
										<h3><?php esc_html_e('Integration', 'widget-announcements'); ?></h3>
									</th>
								</tr>
								
								<tr>
									<th scope="row">
										<label for="to-twitter-integration">
											<?php esc_html_e('Enable To Twitter', 'widget-announcements'); ?>
										</label>
									</th>
									<td>
										<?php
											if ($to_twitter_enabled){ ?>
												<label for="to-twitter-integration"><input name="to-twitter-integration" type="checkbox" id="to-twitter-integration" value="1" <?php checked('1', $options['to-twitter']['integrate']); ?> /><?php printf(esc_html__('Enable integration with %s from %s?', 'widget-announcements'), '<a href="admin.php?page=azrcrv-tt">To Twitter</a>', '<a href="https://development.azurecurve.co.uk/classicpress-plugins/">azurecurve</a>'); ?></label>
											<?php }else{
												printf(esc_html__('%s from %s not installed/activated.', 'widget-announcements'), '<a href="https://development.azurecurve.co.uk/classicpress-plugins/to-twitter/">To Twitter</a>', '<a href="https://development.azurecurve.co.uk/classicpress-plugins/">azurecurve</a>');
											}
										?>
									</td>
								</tr>
								
								<tr>
									<th scope="row">
										<label for="toggle-showhide-integration">
											<?php esc_html_e('Enable Toggle Show/Hide', 'widget-announcements'); ?>
										</label>
									</th>
									<td>
										<?php
											if ($toggle_showhide_enabled){ ?>
												<label for="toggle-showhide-integration"><input name="toggle-showhide-integration" type="checkbox" id="toggle-showhide-integration" value="1" <?php checked('1', $options['toggle-showhide']['integrate']); ?> /><?php printf(esc_html__('Enable integration with %s from %s?', 'widget-announcements'), '<a href="admin.php?page=azrcrv-tsh">Toggle Show/Hide</a>', '<a href="https://development.azurecurve.co.uk/classicpress-plugins/">azurecurve</a>'); ?></label>
											<?php }else{
												printf(esc_html__('%s from %s not installed/activated.', 'widget-announcements'), '<a href="https://development.azurecurve.co.uk/classicpress-plugins/toggle-showhide/">Toggle Show/Hide</a>', '<a href="https://development.azurecurve.co.uk/classicpress-plugins/">azurecurve</a>');
											}
										?>
									</td>
								</tr>
							
							</table>
							
				<?php if ($to_twitter_enabled AND $options['to-twitter']['integrate'] == 1){ ?>
						</div>
				
						<div class="azrcrv-wa-tab <?php echo $tab2visibility; ?> tabs-2">
				
							<table class="form-table">
						
								<tr>
									<th scope="row">
										<label for="to-twitter-tweet">
											<?php esc_html_e('Tweet', 'widget-announcements'); ?>
										</label>
									</th>
									<td>
										<label for="to-twitter-tweet"><input name="to-twitter-tweet" type="checkbox" id="to-twitter-tweet" value="1" <?php checked('1', $options['to-twitter']['tweet']); ?> /><?php esc_html_e('Send tweet at below time?', 'widget-announcements'); ?></label>
									</td>
								</tr>
								
								<tr>
									<th scope="row">
										<label for="to-twitter-tweet-time">
											<?php esc_html_e('Tweet Time', 'widget-announcements'); ?>
										</label>
									</th>
									<td>										
										<input type="time" id="to-twitter-tweet-time" name="to-twitter-tweet-time" value="<?php esc_html_e($options['to-twitter']['tweet-time']); ?>" required />
									</td>
								</tr>
								
								<tr>
									<th scope="row">
										<label for="to-twitter-retweet">
											<?php esc_html_e('Reweet', 'widget-announcements'); ?>
										</label>
									</th>
									<td>
										<label for="to-twitter-retweet"><input name="to-twitter-retweet" type="checkbox" id="to-twitter-retweet" value="1" <?php checked('1', $options['to-twitter']['retweet']); ?> /><?php esc_html_e('Send retweet at below time?', 'widget-announcements'); ?></label>
									</td>
								</tr>
								
								<tr>
									<th scope="row">
										<label for="to-twitter-retweet-time">
											<?php esc_html_e('Tweet Time', 'widget-announcements'); ?>
										</label>
									</th>
									<td>										
										<input type="time" id="to-twitter-retweet-time" name="to-twitter-retweet-time" value="<?php esc_html_e($options['to-twitter']['retweet-time']); ?>" required />
									</td>
								</tr>
								
								<tr>
									<th scope="row"><label for="to-twitter-retweet-prefix">
										<?php esc_html_e('Retweet Prefix', 'widget-announcements'); ?></label>
									</th>
									<td>
										<input name="to-twitter-retweet-prefix" type="text" id="to-twitter-retweet-prefix" value="<?php if (strlen($options['to-twitter']['retweet-prefix']) > 0){ echo sanitize_text_field($options['to-twitter']['retweet-prefix']); } ?>" class="regular-text" />
									</td>
								</tr>
								
								<tr>
									<th scope="row"><label for="to-twitter-tweet-format">
										<?php esc_html_e('Tweet Format', 'widget-announcements'); ?></label>
									</th>
									<td>
										<input name="to-twitter-tweet-format" type="text" id="to-twitter-tweet-format" value="<?php if (strlen($options['to-twitter']['tweet-format']) > 0){ echo sanitize_text_field($options['to-twitter']['tweet-format']); } ?>" class="regular-text" />
									</td>
								</tr>
								
								<tr>
									<th scope="row">
										<label for="to-twitter-use-featured-image">
											<?php esc_html_e('Use Featured Imge', 'widget-announcements'); ?>
										</label>
									</th>
									<td>
										<label for="to-twitter-use-featured-image"><input name="to-twitter-use-featured-image" type="checkbox" id="to-twitter-use-featured-image" value="1" <?php checked('1', $options['to-twitter']['use-featured-image']); ?> /><?php esc_html_e('Use featured image? Only three other media images can be included in the tweet.', 'widget-announcements'); ?></label>
									</td>
								</tr>
								
							</table>
						</div>
				</div>
				<?php } ?>
				
				<input type="submit" value="<? esc_html_e('Save Changes', 'widget-announcements'); ?>" class="button-primary"/>
				
			</form>
		</fieldset>
	</div>
	<?php
}

/**
 * Check if other plugin active.
 *
 * @since 1.2.0
 *
 */
function azrcrv_wa_is_plugin_active($plugin){
    return in_array($plugin, (array) get_option('active_plugins', array()));
}

/**
 * Save settings.
 *
 * @since 1.0.0
 *
 */
function azrcrv_wa_save_options(){
	// Check that user has proper security level
	if (!current_user_can('manage_options')){
		wp_die(esc_html__('You do not have permissions to perform this action', 'widget-announcements'));
	}
	// Check that nonce field created in configuration form is present
	if (! empty($_POST) && check_admin_referer('azrcrv-wa', 'azrcrv-wa-nonce')){
	
		// Retrieve original plugin options array
		$options = get_option('azrcrv-wa');
		$original_options = $options;
		
		$option_name = 'widget-width';
		if (isset($_POST[$option_name])){
			$options['widget']['width'] = sanitize_text_field(intval($_POST[$option_name]));
		}
		
		$option_name = 'widget-height';
		if (isset($_POST[$option_name])){
			$options['widget']['height'] = sanitize_text_field(intval($_POST[$option_name]));
		}
		
		$option_name = 'to-twitter-integration';
		if (isset($_POST[$option_name])){
			$options['to-twitter']['integrate'] = 1;
		}else{
			$options['to-twitter']['integrate'] = 0;
		}
		
		$option_name = 'to-twitter-tweet';
		if (isset($_POST[$option_name])){
			$options['to-twitter']['tweet'] = 1;
		}else{
			$options['to-twitter']['tweet'] = 0;
		}
		
		$option_name = 'to-twitter-tweet-time';
		if (isset($_POST[$option_name])){
			$tweet_time = preg_replace("([^0-9-:-])", "", $_POST[$option_name]);
			$options['to-twitter']['tweet-time'] = sanitize_text_field($tweet_time);
		}
		
		$option_name = 'to-twitter-retweet';
		if (isset($_POST[$option_name])){
			$options['to-twitter']['retweet'] = 1;
		}else{
			$options['to-twitter']['retweet'] = 0;
		}
		
		$option_name = 'to-twitter-retweet-time';
		if (isset($_POST[$option_name])){
			$retweet_time = preg_replace("([^0-9-:-])", "", $_POST[$option_name]);
			$options['to-twitter']['retweet-time'] = sanitize_text_field($retweet_time);
		}
		
		$option_name = 'to-twitter-retweet-prefix';
		if (isset($_POST[$option_name])){
			$options['to-twitter']['retweet-prefix'] = sanitize_text_field($_POST[$option_name]);
		}
		
		$option_name = 'to-twitter-tweet-format';
		if (isset($_POST[$option_name])){
			$options['to-twitter']['tweet-format'] = sanitize_text_field($_POST[$option_name]);
		}
		
		$option_name = 'to-twitter-use-featured-image';
		if (isset($_POST[$option_name])){
			$options['to-twitter']['use-featured-image'] = 1;
		}else{
			$options['to-twitter']['use-featured-image'] = 0;
		}
		
		$option_name = 'toggle-showhide-integration';
		if (isset($_POST[$option_name])){
			$options['toggle-showhide']['integrate'] = 1;
		}else{
			$options['toggle-showhide']['integrate'] = 0;
		}
		
		// Store updated options array to database
		update_option('azrcrv-wa', $options);
		
		if ($options['to-twitter']['integrate'] == 1){
			wp_schedule_event(strtotime('00:01:00'), 'hourly', 'azrcrv_wa_cron_hourly_check');
		}else{
			wp_clear_scheduled_hook("azrcrv_wa_cron_hourly_check");
		}
		
		$response = '';
		if ($original_options['to-twitter']['integrate'] == 0 AND $options['to-twitter']['integrate'] == 1){
			$response = '&i';
		}
		// Redirect the page to the configuration form that was processed
		wp_redirect(add_query_arg('page', 'azrcrv-wa&settings-updated'.$response, admin_url('admin.php')));
		exit;
	}
}

/**
 * Post status changes to "publish".
 *
 * @since 1.2.0
 *
 */
function azrcrv_wa_post_status_transition($new_status, $old_status, $post){
	
	$options = azrcrv_wa_get_option('azrcrv-wa');
	$to_twitter_enabled = azrcrv_wa_is_plugin_active('azrcrv-to-twitter/azrcrv-to-twitter.php');
	
	if ($post->post_type == 'widget-announcement' AND $to_twitter_enabled AND $options['to-twitter']['integrate'] == 1 AND $new_status == 'publish' AND $old_status != 'publish') {
		azrcrv_wa_check_tweet($post->ID, $post);
    }
	
}

/**
 * Autopost tweet for post when status changes to "publish".
 *
 * @since 1.0.0
 *
 */
function azrcrv_wa_check_tweet($post_id, $post){
    remove_action('wp_insert_post', 'updated_to_publish', 10, 2);
	
	$options = azrcrv_wa_get_option('azrcrv-wa');
	$to_twitter_enabled = azrcrv_wa_is_plugin_active('azrcrv-to-twitter/azrcrv-to-twitter.php');
	
	if ($post->post_type == 'widget-announcement' AND $to_twitter_enabled AND $options['to-twitter']['integrate'] == 1 AND $post->post_status == 'publish'){
		azrcrv_wa_check_tweet_today($post_id , $post->post_date);
	}
	
}

/**
 * Register widget.
 *
 * @since 1.0.0
 *
 */
function azrcrv_wa_create_widget(){
	register_widget('azrcrv_wa_register_widget');
}

/**
 * Widget class.
 *
 * @since 1.0.0
 *
 */
class azrcrv_wa_register_widget extends WP_Widget {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 */
	function __construct(){
		add_action('wp_enqueue_scripts', array($this, 'enqueue'));
		
		// Widget creation function
		parent::__construct('azrcrv-wa',
							 'Widget Announcements by azurecurve',
							 array('description' =>
									esc_html__('Announcements in a widget', 'widget-announcements')));
	}
	
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function enqueue(){
		// Enqueue Styles
		wp_enqueue_style('azrcrv-wa', plugins_url('assets/css/style.css', __FILE__), '', '1.0.0');
	}

	/**
	 * Display widget form in admin.
	 *
	 * @since 1.0.0
	 *
	 */
	function form($instance){
		
		$options = azrcrv_wa_get_option('azrcrv-wa');
		
		$widget_category = (!empty($instance['category']) ? esc_attr($instance['category']) : 'Announcements');
		
		$width = (!empty($instance['width']) ? esc_attr($instance['width']) : $options['widget']['width']);
		
		$height = (!empty($instance['height']) ? esc_attr($instance['height']) : $options['widget']['height']);
		?>
		
		<p>
			<label for="<?php echo 
						$this->get_field_id('category'); ?>">
			<?php
			echo 'Category:';
				
			echo '<select id="'.$this->get_field_id('category').'" name="'.$this->get_field_name('category').'">';
				$categories = get_categories(
												array(
													'orderby' => 'name',
													'hide_empty' => false,
													'taxonomy' => 'announcement-category',
												)
											);
				
				foreach ($categories as $category){
					if ($widget_category == $category->term_id){
						$selected = 'selected';
					}else{
						$selected = '';
					}
					echo '<option value="'.$category->term_id.'" '.$selected.' >'.$category->name.'</option>';
				}
			echo	'</select>';
			?>	
			</label>
		</p> 
		
		<p>
			<label for="<?php echo 
						$this->get_field_name('width'); ?>">
			<?php esc_html_e('Width:', 'events'); ?>&nbsp;			
			<input type="number" id="<?php echo $this->get_field_name('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" class="small-text" value="<?php echo $width; ?>" />
			</label>
		</p>
		
		<p>
			<label for="<?php echo 
						$this->get_field_name('height'); ?>">
			<?php esc_html_e('Height:', 'events'); ?>&nbsp;			
			<input type="number" id="<?php echo $this->get_field_name('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" class="small-text" value="<?php echo $height; ?>" />
			</label>
		</p>

		<?php
	}

	/**
	 * Validate user input.
	 *
	 * @since 1.0.0
	 *
	 */
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		
		$instance['category'] = strip_tags($new_instance['category']);
		$instance['width'] = sanitize_text_field(intval($new_instance['width']));
		$instance['height'] = sanitize_text_field(intval($new_instance['height']));

		return $instance;
	}
	
	/**
	 * Display widget on front end.
	 *
	 * @since 1.0.0
	 *
	 */
	function widget ($args, $instance){
		
		$options = azrcrv_wa_get_option('azrcrv-wa');
		
		$width = (!empty($instance['width']) ? esc_attr($instance['width']) : $options['widget']['width']);
		
		$height = (!empty($instance['height']) ? esc_attr($instance['height']) : $options['widget']['height']);
		
		// Extract members of args array as individual variables
		extract($args);
		
		$today = getdate();
		$announcements = get_posts(array(
											'post_type' => 'widget-announcement',
											'numberposts' => -1,
											'orderby' => 'date',
											'order' => 'ASC',
											'tax_query' => array(
																	array(
																		'taxonomy' => 'announcement-category',
																		'field' => 'term_id', 
																		'terms' => $instance['category'],
																		'include_children' => false,
																	)
																),
											'post_status'   => 'publish',
										),
							);
		
		foreach ($announcements as $announcement){
			
			$year = date('Y');
			
			$repeat_details = get_post_meta($announcement->ID, '_azrcrv_wa_repeat', true);
			
			if (
					// today
					(date_format(date_create($announcement->post_date), "Y-m-d") == date("Y-m-d") AND $repeat_details['type'] == 'none')
				OR
					// annual repeat
					(date_format(date_create($announcement->post_date), "m-d") == date('m-d') AND $repeat_details['type'] == 'annual')
				OR
					// month repeat
					(date_format(date_create($announcement->post_date), "d") == date('d') AND $repeat_details['type'] == 'monthly')
				OR
					// n day of month repeat
					(date( "Y-m-d") == date("Y-m-d", strtotime($repeat_details['month-repeat']['instance'].' '.$repeat_details['month-repeat']['day'].' of '.date('Y-m'))) AND $repeat_details['type'] == 'monthnday')
				OR
					// n day of month annaul repeat
					(date( "Y-m-d") == date("Y-m-d", strtotime($repeat_details['annual-repeat']['instance']." ".$repeat_details['annual-repeat']['day']." of $year-".$repeat_details['annual-repeat']['month'])) AND $repeat_details['type'] == 'annualnday')
				OR
					// good friday
					(date("Y-m-d", strtotime("+".(easter_days($year) - 2)." days", strtotime("$year-03-21 12:00:00"))) == date( "Y-m-d") AND $repeat_details['type'] == 'goodfriday')
				OR
					// easter sunday
					(date("Y-m-d", easter_date($year)) == date( "Y-m-d") AND $repeat_details['type'] == 'eastersunday')
				OR
					// easter monday
					(date("Y-m-d", strtotime("+".(easter_days($year) + 1)." days", strtotime("$year-03-21 12:00:00"))) == date( "Y-m-d") AND $repeat_details['type'] == 'eastermonday')
				){
			
				// display widget title
				echo $before_widget;
				echo $before_title;
				$widget_title = $announcement->post_title;
				echo apply_filters('widget_title', $widget_title);
				echo $after_title; 
				
				// display widget body
				if ($options['toggle-showhide']['integrate'] == 1 AND azrcrv_wa_is_plugin_active('azrcrv-toggle-showhide/azrcrv-toggle-showhide.php')){
					$toggle_showhide_enabled = 1;
				}else{
					$toggle_showhide_enabled = 0;
				}
				
				$content = $announcement->post_content;
				$excerpt = $announcement->post_excerpt;
				if ($toggle_showhide_enabled){
					$atts = array('style' => 2, );
					$content = azrcrv_tsh_display_toggle($atts, $content);
					$excerpt = azrcrv_tsh_display_toggle($atts, $excerpt);
				}
				echo '<p>'.$content.'</p>';
				if (has_post_thumbnail($announcement->ID)){
					$image = wp_get_attachment_image(get_post_thumbnail_id($announcement->ID), array($width,$height),'', array('class' => "img-responsive aligncenter", 'alt' => get_the_title()));
					echo '<div class="azrcrv-wa">'.$image.'</div>';
				}
				echo '<p>'.$excerpt.'</p>';
				
				// display widget footer
				echo $after_widget;
			}
		}
	}
}

/**
 * Add post metabox to sidebar.
 *
 * @since 1.2.0
 *
 */
function azrcrv_wa_add_to_twitter_sidebar_metabox(){
		
	$options = azrcrv_wa_get_option('azrcrv-wa');
	
	if ($options['to-twitter']['integrate'] == 1 AND azrcrv_wa_is_plugin_active('azrcrv-to-twitter/azrcrv-to-twitter.php')){
		$to_twitter_enabled = 1;
	}else{
		$to_twitter_enabled = 0;
	}
	
	if ($to_twitter_enabled){
		
		$options = azrcrv_wa_get_option('azrcrv-wa');
		
		if ($options['to-twitter']['integrate'] == 1){
			add_meta_box('azrcrv-wa-to-twitter-box', esc_html__('Autopost Tweet', 'widget-announcements'), 'azrcrv_wa_generate_to_twitter_sidebar_metabox', 'widget-announcement', 'side', 'default');
		}
		
	}
}

/**
 * Generate post sidebar metabox.
 *
 * @since 1.2.0
 *
 */
function azrcrv_wa_generate_to_twitter_sidebar_metabox(){
	
	global $post;
	
	$options = azrcrv_wa_get_option('azrcrv-wa');
	
	$autopost_tweet = get_post_meta($post->ID, '_azrcrv_wa_tweet', true);
	
	if (is_array($autopost_tweet)){
		$use_featured_image = $autopost_tweet['use-featured-image'];
		$tweet = $autopost_tweet['tweet'];
		$tweet_time = $autopost_tweet['tweet-time'];
		$retweet = $autopost_tweet['retweet'];
		$retweet_time = $autopost_tweet['retweet-time'];
		$hashtags = $autopost_tweet['hashtags'];
	}else{
		$use_featured_image = $options['to-twitter']['use-featured-image'];
		$tweet = $options['to-twitter']['tweet'];
		$retweet = $options['to-twitter']['retweet'];
		$tweet_time = $options['to-twitter']['tweet-time'];
		$retweet_time = $options['to-twitter']['retweet-time'];
		$hashtags = '';
	}
	
	echo '<p class="azrcrv-wa-tweet">';
		wp_nonce_field(basename(__FILE__), 'azrcrv-wa-to-twitter-sidebar-nonce');
		
		if ($use_featured_image == 1){
			$checked = 'checked="checked"';
		}else{
			$checked = '';
		}
		echo '<p>
				<label>
					<input type="checkbox" name="use-featured-image" '.$checked.' />  '.esc_html__('Use featured image as tweet media image 1?', 'widget-announcements').'
				</label>';
		echo '</p>';
		
		if ($tweet == 1){
			$checked = 'checked="checked"';
		}else{
			$checked = '';
		}
		echo '<p><label><input type="checkbox" name="tweet" '.$checked.' />  '.esc_html__('Tweet announcement?', 'widget-announcements').'</label></p>
		
		<p>
			'.esc_html__('Tweet Time: ', 'widget-announcements').'
			
			<input type="time" id="tweet-time" name="tweet-time" value="'.esc_html($tweet_time).'" required />
		</p>';
		
		if ($retweet == 1){
			$checked = 'checked="checked"';
		}else{
			$checked = '';
		}
		echo '<p><label><input type="checkbox" name="retweet" '.$checked.' />  '.esc_html__('Retweet announcement?', 'widget-announcements').'</label></p>
		<p>
			'.esc_html__('Retweet Time: ', 'widget-announcements').'
			
			<input type="time" id="retweet-time" name="retweet-time" value="'.esc_html($retweet_time).'" required />
		</p>';
		
		echo '<p>
			<label for="hashtags">Hashtags</label><br/>
			<input name="hashtags" type="text" style="width: 100%;" value="'.esc_html($hashtags).'" />
		</p>
	</p>';
	
}

/**
 * Save To Twitter Sidebar Metabox.
 *
 * @since 1.2.0
 *
 */
function azrcrv_wa_save_to_twitter_sidebar_metabox($post_id){
	
	if (! isset($_POST['azrcrv-wa-to-twitter-sidebar-nonce']) || ! wp_verify_nonce($_POST['azrcrv-wa-to-twitter-sidebar-nonce'], basename(__FILE__))){
		return $post_id;
	}
	
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
		return $post_id;
	}
	
	if (! current_user_can('edit_post', $post_id)){
		return $post_id;
	}
	
	$post_type = get_post_type($post_id);
	
    if ($post_type == 'widget-announcement'){
		if (isset($_POST['use-featured-image'])){
			$use_featured_image = 1;
		}else{
			$use_featured_image = 0;
		}
		if (isset($_POST['tweet'])){
			$tweet = 1;
		}else{
			$tweet = 0;
		}
		$tweet_time = preg_replace("([^0-9-:-])", "", $_POST['tweet-time']);
		
		if (isset($_POST['retweet'])){
			$retweet = 1;
		}else{
			$retweet = 0;
		}
		$retweet_time = preg_replace("([^0-9-:-])", "", $_POST['retweet-time']);
		
		$hashtags = sanitize_text_field($_POST['hashtags']);
		
		$autopost_tweet = get_post_meta($post_id, '_azrcrv_wa_tweet', true);
		
		if (!is_array($autopost_tweet)){
			$autopost_tweet = array(
										'tweeted-date' => '1900-01-01',
										'retweeted-date' => '1900-01-01',
									);
		}
		
		$autopost_tweet['use-featured-image'] = $use_featured_image;
		$autopost_tweet['tweet'] = $tweet;
		$autopost_tweet['tweet-time'] = $tweet_time;
		$autopost_tweet['retweet'] = $retweet;
		$autopost_tweet['retweet-time'] = $retweet_time;
		$autopost_tweet['hashtags'] = $hashtags;
		
		update_post_meta($post_id, '_azrcrv_wa_tweet', $autopost_tweet);
	}
	
	return;
}

/**
 * Create Cron hourly check for widget announcements.
 *
 * @since 1.2.0
 *
 */
function azrcrv_wa_create_cron_hourly(){
	
	$options = azrcrv_wa_get_option('azrcrv-wa');
	$to_twitter_enabled = azrcrv_wa_is_plugin_active('azrcrv-to-twitter/azrcrv-to-twitter.php');
	
	if ($to_twitter_enabled AND $options['to-twitter']['integrate'] == 1){
		wp_schedule_event(strtotime('00:01:00'), 'hourly', 'azrcrv_wa_cron_hourly_check');
	}
	
}

/**
 * Clear Cron hourly check for widget announcements.
 *
 * @since 1.2.0
 *
 */
function azrcrv_wa_clear_cron_hourly(){
	
	wp_clear_scheduled_hook("azrcrv_wa_cron_hourly_check");
	
}

/**
 * Clear Cron for widget announcement.
 *
 * @since 1.2.0
 *
 */
function azrcrv_wa_clear_cron_single($cron_name, $post_id, $type){
	
	wp_clear_scheduled_hook($cron_name, array($post_id, $type));
	
}

/**
 * Perform Cron hourly check for widget announcements.
 *
 * @since 1.2.0
 *
 */
function azrcrv_wa_perform_cron_check(){
	
	$today = getdate();
	$announcements = get_posts(array(
										'post_type' => 'widget-announcement',
										'numberposts' => -1,
										'orderby' => 'date',
										'order' => 'ASC',
										'post_status'   => 'publish',
									),
						);
	
	foreach ($announcements as $announcement){
		
		azrcrv_wa_check_tweet_today($announcement->ID, $announcement->post_date);
		
	}
	
}

function azrcrv_wa_check_tweet_today($post_id, $post_date){
	
	$year = date('Y');
	
	$repeat_details = get_post_meta($post_id, '_azrcrv_wa_repeat', true);
	
	if (is_array($repeat_details)){
		if (
			// today
			(date_format(date_create($post_date), "Y-m-d") == date("Y-m-d") AND $repeat_details['type'] == 'none')
		OR
			// annual repeat
			(date_format(date_create($post_date), "m-d") == date('m-d') AND $repeat_details['type'] == 'annual')
		OR
			// month repeat
			(date_format(date_create($post_date), "d") == date('d') AND $repeat_details['type'] == 'monthly')
		OR
			// n day of month repeat
			(date( "Y-m-d") == date("Y-m-d", strtotime($repeat_details['month-repeat']['instance'].' '.$repeat_details['month-repeat']['day'].' of '.date('Y-m'))) AND $repeat_details['type'] == 'monthnday')
		OR
			// n day of month annaul repeat
			(date( "Y-m-d") == date("Y-m-d", strtotime($repeat_details['annual-repeat']['instance']." ".$repeat_details['annual-repeat']['day']." of $year-".$repeat_details['annual-repeat']['month'])) AND $repeat_details['type'] == 'annualnday')
		OR
			// good friday
			(date("Y-m-d", strtotime("+".(easter_days($year) - 2)." days", strtotime("$year-03-21 12:00:00"))) == date( "Y-m-d") AND $repeat_details['type'] == 'goodfriday')
		OR
			// easter sunday
			(date("Y-m-d", easter_date($year)) == date( "Y-m-d") AND $repeat_details['type'] == 'eastersunday')
		OR
			// easter monday
			(date("Y-m-d", strtotime("+".(easter_days($year) + 1)." days", strtotime("$year-03-21 12:00:00"))) == date( "Y-m-d") AND $repeat_details['type'] == 'eastermonday')
		){
			$autopost_tweet = get_post_meta($post_id, '_azrcrv_wa_tweet', true);
			
			if ($autopost_tweet['tweet'] == 1 AND $autopost_tweet['tweeted-date'] < date("Y-m-d")){
				$cron_name = 'azrcrv_wa_cron_tweet_announcement';
				$cron_type = 'tweet';
				azrcrv_wa_clear_cron_single($cron_name, $post_id, $cron_type);
				wp_schedule_single_event(strtotime($autopost_tweet['tweet-time']), $cron_name, array($post_id, $cron_type));
			}
			
			if ($autopost_tweet['retweet'] == 1 AND $autopost_tweet['retweeted-date'] < date("Y-m-d")){
				$cron_name = 'azrcrv_wa_cron_tweet_announcement';
				$cron_type = 'retweet';
				azrcrv_wa_clear_cron_single($cron_name, $post_id, $cron_type);
				wp_schedule_single_event(strtotime($autopost_tweet['retweet-time']), $cron_name, array($post_id, $cron_type));
			}
		}
	}
}

function azrcrv_wa_perform_tweet_announcement($post_id, $type){
	
	$post = get_post($post_id);
	if ($post->post_status != 'publish'){ return; }
	
	$autopost_tweet = get_post_meta($post_id, '_azrcrv_wa_tweet', true);
	if ($type == 'tweet'){
		$autopost_tweet['tweeted-date'] = date('Y-m-d');
	}
	if ($type == 'retweet'){
		$autopost_tweet['retweeted-date'] = date('Y-m-d');
	}
	update_post_meta($post_id, '_azrcrv_wa_tweet', $autopost_tweet);

	$post_tweet = get_post_meta($post_id, '_azrcrv_wa_post_tweet', true);
	$media_to_use = array();
	if ($autopost_tweet['use-featured-image'] == 1 AND has_post_thumbnail($post_id)){
		$post_image = get_the_post_thumbnail_url($post_id, 'full'); ;
		$media_to_use[] = $post_image;
	}
	$post_media = get_post_meta( $post_id, '_azrcrv_wa_post_tweet_media', true ); // get tweet content
	
	$options = azrcrv_wa_get_option('azrcrv-wa');
	
	if ($type == 'retweet'){
		$prefix = $options['to-twitter']['retweet-prefix'];
		if (strlen($prefix) > 0){
			$prefix .= ' ';
		}
	}else{
		$prefix = '';
	}
	
	$post_tweet = $prefix.$post_tweet; //text for your tweet.
	
	$parameters = array("status" => $post_tweet);
	if (isset($post_media) AND is_array($post_media)){
		$media_pos = 0;
		foreach ($post_media as $media){
			$media_pos++;
			if ($media_pos == 4 AND isset($post_image)){
				break;
			}else{
				$media_to_use[] = $media;
			}
		}
		$parameters['media-urls'] = $media_to_use;
	}else{
		if (isset($post_image)){
			$parameters['media-urls'] = $media_to_use;
		}
	}
	
	$tweet_result = azrcrv_tt_post_tweet($parameters);
	
	$tt_options = azrcrv_tt_get_option('azrcrv-tt');
	
	if ($tt_options['record_tweet_history'] == 1){

		$tweet_history = get_post_meta($post_id, '_azrcrv_tt_tweet_history', true);
		if (!is_array($tweet_history)){ $tweet_history = array(); }
		$tweet_history[] = array(
									'key' => time(),
									'date' => date("Y-m-d"),
									'time' => date("H:i"),
									'tweet_id' => $tweet_result['id'],
									'author' => $tweet_result['screen_name'],
									'tweet' => $post_tweet,
									'status' => $tweet_result['status'],
								);
		update_post_meta($post_id, '_azrcrv_tt_tweet_history', $tweet_history);
	}
	
}
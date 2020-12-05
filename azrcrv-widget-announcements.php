<?php
/**
 * ------------------------------------------------------------------------------
 * Plugin Name: Widget Announcements
 * Description: Announce holidays, events, achievements and notable historical figures in a widget.
 * Version: 1.1.0
 * Author: azurecurve
 * Author URI: https://development.azurecurve.co.uk/classicpress-plugins/
 * Plugin URI: https://development.azurecurve.co.uk/classicpress-plugins/azrcrv-widget-announcements/
 * Text Domain: widget-announcements
 * Domain Path: /languages
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
// add actions
add_action('admin_menu', 'azrcrv_wa_create_admin_menu');
add_action('init', 'azrcrv_wa_create_cust_taxonomy_for_custom_post');
add_action('init', 'azrcrv_wa_create_custom_post_type');
add_action('admin_menu', 'azrcrv_wa_add_sidebar_metabox');
add_action('save_post', 'azrcrv_wa_save_sidebar_metabox', 10, 1);
add_action('plugins_loaded', 'azrcrv_wa_load_languages');
add_action('wp_enqueue_scripts', 'azrcrv_wa_load_css');
add_action('widgets_init', 'azrcrv_wa_create_widget');
add_action('admin_post_azrcrv_wa_save_options', 'azrcrv_wa_save_options');

// add filters
add_filter('plugin_action_links', 'azrcrv_wa_add_plugin_action_link', 10, 2);
add_filter('codepotent_update_manager_image_path', 'azrcrv_wa_custom_image_path');
add_filter('codepotent_update_manager_image_url', 'azrcrv_wa_custom_image_url');
add_action('current_screen', 'azrcrv_wa_current_screen_callback');

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
function azrcrv_wa_load_languages() {
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
			$new_args[ $key ] = azrcrv_e_recursive_parse_args( $value, $new_args[ $key ] );
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
						'label' => __( 'Category' ),
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
		return __('Text after announcement', 'widget-announcements');
	}else{
		$pos = strpos($original, 'Excerpts are optional hand-crafted summaries of your');

		if ($pos !== false){
			return  '';
		}
	}
	
	return $translation;
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
	
	wp_nonce_field(basename(__FILE__), 'azrcrv-wa-nonce');
	
	$repeat = get_post_meta($post->ID, '_azrcrv_wa_repeat', true);
		
	?>
	
	<fieldset>
	
		<p>
			<input type="radio" id="none" name="repeat-type" value="none" <?php if (!isset($repeat['type']) OR isset($repeat['type']) AND $repeat['type'] == 'none'){ echo 'checked'; } ?>><label for="none"><?php echo __('No repeat', 'widget-announcement'); ?></label>
		</p>
	
		<p>
			<input type="radio" id="monthly" name="repeat-type" value="monthly" <?php if (isset($repeat['type']) AND $repeat['type'] == 'monthly'){ echo 'checked'; } ?>><label for="monthly"><?php echo __('Repeat monthly', 'widget-announcement'); ?></label>
		</p>
	
		<p>
			<input type="radio" id="annual" name="repeat-type" value="annual" <?php if (isset($repeat['type']) AND $repeat['type'] == 'annual'){ echo 'checked'; } ?>><label for="annual"><?php echo __('Repeat annually', 'widget-announcement'); ?></label>
		</p>
		
		<p>
			<input type="radio" id="goodfriday" name="repeat-type" value="goodfriday" <?php if (isset($repeat['type']) AND $repeat['type'] == 'goodfriday'){ echo 'checked'; } ?>><label for="goodfriday"><?php echo __('Repeat on Good Friday', 'widget-announcement'); ?></label>
		</p>
		
		<p>
			<input type="radio" id="eastersunday" name="repeat-type" value="eastersunday" <?php if (isset($repeat['type']) AND $repeat['type'] == 'eastersunday'){ echo 'checked'; } ?>><label for="eastersunday"><?php echo __('Repeat on Easter Sunday', 'widget-announcement'); ?></label>
		</p>
		
		<p>
			<input type="radio" id="eastermonday" name="repeat-type" value="eastermonday" <?php if (isset($repeat['type']) AND $repeat['type'] == 'eastermonday'){ echo 'checked'; } ?>><label for="eastermonday"><?php echo __('Repeat on Easter Monday', 'widget-announcement'); ?></label>
		</p>
		
		<?php
			$instance = array(
							'first' => __('1st', 'widget-announcement'),
							'second' => __('2nd', 'widget-announcement'),
							'third' => __('3rd', 'widget-announcement'),
							'fourth' => __('4th', 'widget-announcement'),
						);
			
			$days = array(
							'Sunday' => __('Sunday', 'widget-announcement'),
							'Monday' => __('Monday', 'widget-announcement'),
							'Tuesday' => __('Tuesday', 'widget-announcement'),
							'Wednesday' => __('Wednesday', 'widget-announcement'),
							'Thursday' => __('Thursday', 'widget-announcement'),
							'Friday' => __('Friday', 'widget-announcement'),
							'Saturday' => __('Saturday', 'widget-announcement'),
						);
			
			$months = array(
							1 => __('Jan', 'widget-announcement'),
							2 => __('Feb', 'widget-announcement'),
							3 => __('Mar', 'widget-announcement'),
							4 => __('Apr', 'widget-announcement'),
							5 => __('May', 'widget-announcement'),
							6 => __('Jun', 'widget-announcement'),
							7 => __('Jul', 'widget-announcement'),
							8 => __('Aug', 'widget-announcement'),
							9 => __('Sept', 'widget-announcement'),
							10 => __('Oct', 'widget-announcement'),
							11 => __('Nov', 'widget-announcement'),
							12 => __('Dec', 'widget-announcement'),
						);
		?>
		
		<p>
			<input type="radio" id="monthnday" name="repeat-type" value="monthnday" <?php if (isset($repeat['type']) AND $repeat['type'] == 'monthnday'){ echo 'checked'; } ?>><label for="monthnday"><?php echo __('Repeat monthly on <em>n day</em> of month', 'widget-announcement'); ?></label>
			
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
			<input type="radio" id="annualnday" name="repeat-type" value="annualnday" <?php if (isset($repeat['type']) AND $repeat['type'] == 'annualnday'){ echo 'checked'; } ?>><label for="annualnday"><?php echo __('Repeat annually on <em>n day</em> of month', 'widget-announcement'); ?></label>
			
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
	
	if(! isset($_POST[ 'azrcrv-wa-nonce' ]) || ! wp_verify_nonce($_POST[ 'azrcrv-wa-nonce' ], basename(__FILE__))){
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
		$settings_link = '<a href="'.admin_url('admin.php?page=azrcrv-wa').'"><img src="'.plugins_url('/pluginmenu/images/Favicon-16x16.png', __FILE__).'" style="padding-top: 2px; margin-right: -5px; height: 16px; width: 16px;" alt="azurecurve" />'.esc_html__('Settings' ,'widget-announcements').'</a>';
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
	
	?>
	<div id="azrcrv-wa-general" class="wrap azrcrv-wa">
		<fieldset>
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
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
					<?php printf(__('%s allows you to add a widget which can be used to announce holidays, events, achievements and notable historical figures in a widget.', 'widget-announcements'), 'Widget Announcements'); ?>
				</p>
				
				<p>
					Announcements can be made:
					<li>One off</li>
					<li>Monthly</li>
					<li>Annually</li>
					<li>Good Friday</li>
					<li>Easter Sunday</li>
					<li>Easter Monday</li>
					<li>Monthly on the nth day (e.g. 2nd Wednesday)</li>
					<li>Annually on the nth day of the month (e.g. 4th Thursday November)</li>
				</p>
				<p> 
					Announcements are created as a custom post type and can have details, an image and additional text after the image; images should be narrower than your widget area.
				</p>
				<p>
					When creating widgets they can be added to one or more categories; when adding a widget, select the category to include.
				</p>
				
				
				<table class="form-table">
					
					<tr>
						<th>
							<h3><?php _e('Widget Defaults', 'widget-announcements'); ?></h3>
						</th>
					</tr>
					
					<tr>
						<th scope="row"><label for="widget-width">
							<?php esc_html_e('Width', 'widget-announcements'); ?></label>
						</th>
						<td>
							<input name="widget-width" type="number" min="1" id="widget-width" value="<?php if (strlen($options['widget']['width']) > 0){ echo sanitize_text_field($options['widget']['width']); } ?>" class="small-text" />
						</td>
					</tr>
					
					<tr>
						<th scope="row"><label for="widget-height">
							<?php esc_html_e('Height', 'widget-announcements'); ?></label>
						</th>
						<td>
							<input name="widget-height" type="number" min="1" id="widget-height" value="<?php if (strlen($options['widget']['height']) > 0){ echo sanitize_text_field($options['widget']['height']); } ?>" class="small-text" />
						</td>
					</tr>
				
				</table>
				
				<input type="submit" value="<? _e('Save Changes', 'widget-announcements'); ?>" class="button-primary"/>
				
			</form>
		</fieldset>
	</div>
	<?php
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
		
		$option_name = 'widget-width';
		if (isset($_POST[$option_name])){
			$options['widget']['width'] = sanitize_text_field(intval($_POST[$option_name]));
		}
		
		$option_name = 'widget-height';
		if (isset($_POST[$option_name])){
			$options['widget']['height'] = sanitize_text_field(intval($_POST[$option_name]));
		}
		
		// Store updated options array to database
		update_option('azrcrv-wa', $options);
		
		// Redirect the page to the configuration form that was processed
		wp_redirect(add_query_arg('page', 'azrcrv-wa&settings-updated', admin_url('admin.php')));
		exit;
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
			<?php _e('Width:', 'events'); ?>&nbsp;			
			<input type="number" id="<?php echo $this->get_field_name('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" class="small-text" value="<?php echo $width; ?>" />
			</label>
		</p>
		
		<p>
			<label for="<?php echo 
						$this->get_field_name('height'); ?>">
			<?php _e('Height:', 'events'); ?>&nbsp;			
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
				echo '<p>'.$announcement->post_content.'</p>';
				if (has_post_thumbnail($announcement->ID)){
					$image = wp_get_attachment_image(get_post_thumbnail_id($announcement->ID), array($width,$height),'', array('class' => "img-responsive aligncenter", 'alt' => get_the_title()));
					echo '<div class="azrcrv-wa">'.$image.'</div>';
				}
				echo '<p>'.$announcement->post_excerpt.'</p>';
				
				// display widget footer
				echo $after_widget;
			}
		}
		
	}
}
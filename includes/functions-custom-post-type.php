<?php
/**
 * Custom post type and taxonomy functions.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\WidgetAnnouncements;

/**
 * Register custom announcement taxonomy.
 *
 * @since 1.0.0
 */
function create_cust_taxonomy_for_custom_post() {

	register_taxonomy(
		'announcement-category',
		'widget-announcement',
		array(
			'label'        => esc_html__( 'Category', 'azrcrv-wa' ),
			'rewrite'      => array( 'slug' => 'announcement-category' ),
			'hierarchical' => true,
		)
	);
}

/**
 * Register custom announcement post type.
 *
 * @since 1.0.0
 */
function create_custom_post_type() {

	register_post_type(
		'widget-announcement',
		array(
			'labels'              => array(
				'name'               => esc_html__( 'Announcements', 'azrcrv-wa' ),
				'singular_name'      => esc_html__( 'Announcement', 'azrcrv-wa' ),
				'add_new'            => esc_html__( 'Add New', 'azrcrv-wa' ),
				'add_new_item'       => esc_html__( 'Add New Announcement', 'azrcrv-wa' ),
				'edit'               => esc_html__( 'Edit', 'azrcrv-wa' ),
				'edit_item'          => esc_html__( 'Edit Announcement', 'azrcrv-wa' ),
				'new_item'           => esc_html__( 'New Announcement', 'azrcrv-wa' ),
				'view'               => esc_html__( 'View', 'azrcrv-wa' ),
				'view_item'          => esc_html__( 'View Announcement', 'azrcrv-wa' ),
				'search_items'       => esc_html__( 'Search Announcement', 'azrcrv-wa' ),
				'not_found'          => esc_html__( 'No Announcement found', 'azrcrv-wa' ),
				'not_found_in_trash' => esc_html__( 'No Announcement found in Trash', 'azrcrv-wa' ),
				'parent'             => esc_html__( 'Parent Announcement', 'azrcrv-wa' ),
			),
			'public'              => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'menu_position'       => 50,
			'supports'            => array( 'title', 'revisions', 'editor', 'excerpt', 'thumbnail' ),
			'taxonomies'          => array( 'announcement-category' ),
			'menu_icon'           => 'dashicons-megaphone',
			'has_archive'         => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => false,
		)
	);
}

/**
 * Make sure label changes only apply for the widget-announcement post type.
 *
 * @since 1.0.1
 */
function current_screen_callback( $screen ) {
	if ( is_object( $screen ) && $screen->post_type === 'widget-announcement' ) {
		add_filter( 'gettext', __NAMESPACE__ . '\\admin_post_excerpt_change_labels', 99, 3 );
	}
}

/**
 * Change labels in the excerpt box.
 *
 * @since 1.0.0
 */
function admin_post_excerpt_change_labels( $translation, $original ) {
	if ( 'Excerpt' === $original ) {
		return esc_html__( 'Text after announcement', 'azrcrv-wa' );
	} else {
		$pos = strpos( $original, 'Excerpts are optional hand-crafted summaries of your' );
		if ( false !== $pos ) {
			return '';
		}
	}

	return $translation;
}

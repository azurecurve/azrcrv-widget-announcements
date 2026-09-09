<?php
/*
	shortcode-in-widgets filters - registers do_shortcode() (and, for the
	legacy Text widget, shortcode_unautop() first) against whichever widget
	areas are enabled on the Settings tab (see functions-settings.php).

	Filters are only ever added for a toggle that is actually enabled -
	nothing is registered-then-no-op for a disabled toggle, so there is no
	extra work done on the front end for widget areas the admin hasn't opted
	into.

	Note on double-processing: ClassicPress/WordPress core itself adds
	do_shortcode() to the 'widget_text_content' filter for the built-in Text
	widget when it is in "visual" mode, and core automatically suspends any
	plugin-supplied do_shortcode() on 'widget_text' for that same visual-mode
	case specifically to prevent shortcodes being processed twice (see
	WP_Widget_Text::widget()). Because of that existing core safeguard,
	enabling both 'enable_widget_text' and 'enable_widget_text_content' at
	once is safe - it will not double-process a visual-mode Text widget.
	'enable_widget_text' still matters on its own for the legacy/classic Text
	widget and for any third-party widget that manually applies the
	'widget_text' filter (many do, for back-compat).

	'enable_widget_custom_html' has no such core safeguard - core does not
	run do_shortcode() on the Custom HTML widget by default, so this is a
	genuinely new capability rather than a duplicate of existing behaviour.
*/

/**
 * Declare the Namespace.
 */
namespace azurecurve\ShortcodesInWidgets;

/**
 * Prevent direct access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Register the enabled widget-area shortcode filters.
 */
function register_shortcode_filters() {

	$settings = get_settings();

	if ( ! empty( $settings['enable_widget_text'] ) ) {
		add_filter( 'widget_text', 'shortcode_unautop' );
		add_filter( 'widget_text', 'do_shortcode' );
	}

	if ( ! empty( $settings['enable_widget_text_content'] ) ) {
		add_filter( 'widget_text_content', 'do_shortcode' );
	}

	if ( ! empty( $settings['enable_widget_custom_html'] ) ) {
		add_filter( 'widget_custom_html_content', 'do_shortcode' );
	}

	if ( ! empty( $settings['enable_widget_title'] ) ) {
		add_filter( 'widget_title', 'do_shortcode' );
	}
}
add_action( 'widgets_init', __NAMESPACE__ . '\\register_shortcode_filters' );

<?php
/*
	instructions tab
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
?>

<h2><?php esc_html_e( 'Instructions', 'azrcrv-siw' ); ?></h2>

<p><?php esc_html_e( 'This plugin lets shortcodes (such as [gallery] or those added by other plugins) be used inside widget content, rather than only inside post and page content.', 'azrcrv-siw' ); ?></p>

<ol>
	<li><?php esc_html_e( 'Open the Settings tab and enable shortcode support for whichever widget areas you use - Text widget, Custom HTML widget, and/or widget titles.', 'azrcrv-siw' ); ?></li>
	<li><?php esc_html_e( 'Add your shortcode directly into the widget content (or title) as you normally would in a post.', 'azrcrv-siw' ); ?></li>
	<li><?php esc_html_e( 'Save the widget and view the front end of your site to confirm the shortcode has expanded correctly.', 'azrcrv-siw' ); ?></li>
</ol>

<h3><?php esc_html_e( 'About the Text Widget options', 'azrcrv-siw' ); ?></h3>
<p>
	<?php esc_html_e( 'ClassicPress/WordPress already expands shortcodes automatically in the built-in Text widget when it is used in its default "visual" mode - the "Text Widget (visual/block mode)" option on the Settings tab mainly helps third-party widgets that render their own content through the same filter, and is safe to enable alongside the legacy option without shortcodes being expanded twice.', 'azrcrv-siw' ); ?>
</p>
<p>
	<?php esc_html_e( 'The "Text Widget (legacy/classic mode)" option covers the older, non-visual Text widget, and any third-party widget that has not been updated to use the newer widget content filter.', 'azrcrv-siw' ); ?>
</p>

<h3><?php esc_html_e( 'About the Custom HTML Widget option', 'azrcrv-siw' ); ?></h3>
<p>
	<?php esc_html_e( 'Unlike the Text widget, ClassicPress/WordPress does not expand shortcodes in the Custom HTML widget by default, so this option adds genuinely new functionality. Because shortcode processing runs across the entire widget content, avoid using square brackets in any inline JavaScript within a Custom HTML widget where this option is enabled.', 'azrcrv-siw' ); ?>
</p>

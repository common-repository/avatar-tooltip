<?php if (!defined ('ABSPATH')) die ('No direct access allowed');

/**
 * Admin-menu and admin-bar hooks
 *
 * Contains the functions/hooks about admin menu and admin bar
 *
 * @package Avatar Tooltip
 * @since 1.0
 */



/**
 * Add plugin option page
 */
function axe_at_add_admin_pages() {
	add_options_page('Avatar Tooltip', 'Avatar Tooltip', 'manage_options', AXE_AT_PLUGIN_DIR.'/php/view-admin-options.php');
}
add_action('admin_menu', 'axe_at_add_admin_pages');



/**
 * Help & option screen tabs (on top right)
 */
function axe_at_screen_tabs( $hook ) {
	global $wp_version;
	if ( version_compare ( $wp_version, '3.3', '>=' ) ) {
		$screen = get_current_screen();
		
		if ( AXE_AT_PLUGIN_DIR.'/php/view-admin-options' == $screen->id ) {
			
			$screen->add_help_tab( array(
				'id'      => 'sfc-base',
				'title'   => __('Help' ),
				'content' => '<ul>' .
							'<li><a href="http://wordpress.org/extend/plugins/avatar-tooltip/faq/" target="_blank">' . __('Plugin FAQ', AXE_AT_PLUGIN_DIR ) .'</a></li>' .
							'<li><a href="http://wordpress.org/support/plugin/avatar-tooltip" target="_blank">' . __('Support forum on WordPress.org', AXE_AT_PLUGIN_DIR ) .'</a></li>' .
							'</ul>'
				//'callback' => create_function('','echo "<p>This is my generated content.</p>";'
			));
			
			$screen->set_help_sidebar (
				'<p style="text-align: center;font-style: italic">'. __('If you use this plugin consider the idea of donating and supporting its development', AXE_AT_PLUGIN_DIR ) .': <br />' .
				'<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="display:block;width: 100%;margin: 0 auto;text-align: center">' .
				'<input name="cmd" value="_s-xclick" type="hidden"><input name="hosted_button_id" value="FSEGV7H8YNVLQ" type="hidden">' .
				'<input src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" name="submit" alt="Donate via PayPal" title="Donate via PayPal" border="0" type="image">' .
				'<img src="https://www.paypal.com/it_IT/i/scr/pixel.gif" border="0" height="1" width="1"></form></p>'
			);
	
		}
		
		
	} // end if version_compare
}
add_action('admin_enqueue_scripts', 'axe_at_screen_tabs');



/* EOF */

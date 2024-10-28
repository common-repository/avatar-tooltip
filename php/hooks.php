<?php if (!defined ('ABSPATH')) die ('No direct access allowed');

/**
 * Hooks
 *
 * Contains hooks and related functions
 *
 * @package Avatar Tooltip
 * @since 1.0
 */
 


/**
 * On plugin init
 */
function axe_at_init() {
	load_plugin_textdomain ( AXE_AT_PLUGIN_DIR, false, AXE_AT_PLUGIN_DIR. "/languages");
}
add_action( 'init', 'axe_at_init' );



/**
 * Load javascripts and css
 */
function axe_at_load_scripts() {

	$axe_at_options = axe_at_get_options();

	// Only for loggegin users?
	if ( $axe_at_options['only_logged'] == 'yes' && ! is_user_logged_in() ) return;
	    
	wp_enqueue_script( 'json2' );
    wp_enqueue_script( 'jquery', false, array( 'json2' ) );
    
	wp_enqueue_script( 'jquery-qtip', AXE_AT_PLUGIN_URL . '/inc/jquery.qtip.min.js' );
	
	wp_enqueue_script( 'axe-avatar-tooltip', AXE_AT_PLUGIN_URL . '/inc/axe-avatar-tooltip.js', array( 'jquery', 'json2', 'jquery-qtip' ) );	
	wp_localize_script('axe-avatar-tooltip', 'axeATloc', axe_at_localize_scripts() );
	wp_enqueue_style( 'jquery-qtip-css', AXE_AT_PLUGIN_URL.'/inc/jquery.qtip.min.css' );
	wp_enqueue_style( 'axe-avatar-services-css', AXE_AT_PLUGIN_URL.'/inc/services.css' );
	
	if ( @file_exists ( STYLESHEETPATH.'/axe-avatar-tooltip.css' ) ) {
		wp_enqueue_style ('axe-avatar-tooltip-css', get_bloginfo('stylesheet_directory') .'/axe-avatar-tooltip.css' );
	} else {
		wp_enqueue_style ('axe-avatar-tooltip-css', AXE_AT_PLUGIN_URL.'/axe-avatar-tooltip.css' );
	}
}    
add_action('wp_enqueue_scripts', 'axe_at_load_scripts');


/**
 * Load javascripts and css in admin too
 */
function axe_at_load_admin_scripts() {
	global $pagenow;
	if( 'options-general.php' == $pagenow && isset($_GET['page']) && $_GET['page'] == AXE_AT_PLUGIN_DIR.'/php/view-admin-options.php' )
	add_action('admin_enqueue_scripts', 'axe_at_load_scripts');
}    
add_action('admin_init', 'axe_at_load_admin_scripts');



function axe_at_localize_scripts () {
	global $pagenow, $post, $current_screen;
	$post_id = ( !empty( $post ) && is_object( $post ) ) ? $post->ID : false;

	$axe_at_options = axe_at_get_options();

	// No more useful
	unset( $axe_at_options['only_logged'] );

	// Add selector for avatar preview in backend
	$axe_at_options['jq_selector'] .= ', .wp-admin img.avatar[rel]';

    return array(
		'ajaxurl' 		=> admin_url( 'admin-ajax.php' ), 
		'scriptPath' 	=> AXE_AT_PLUGIN_URL."/",
		'nonce'			=> wp_create_nonce( 'axe_at_content' ),
		'loadingTxt' 	=> esc_js( __('loading...', AXE_AT_PLUGIN_DIR) ),
		'errorTxt' 		=> esc_js( __('error', AXE_AT_PLUGIN_DIR) ),
		'closeTxt'		=> esc_js( __('Close') ),
		'qtipOptions'	=> array_map( 'esc_js', $axe_at_options )
	);
}


/**
 * Save options in plugin option screen
 *
 * We save them on 'init' so the tooltip in admin preview loads updated options
 */
function axe_at_save_options () {
	global $pagenow;
	if( 'options-general.php' == $pagenow && isset($_GET['page']) && $_GET['page'] == AXE_AT_PLUGIN_DIR.'/php/view-admin-options.php' ) {

		if ( !current_user_can( 'manage_options' ) )  wp_die(__('Cheatin&#8217; uh?'));
		
		// Update Options
		if ( isset($_POST['submit_options']) ) {

			if ( !wp_verify_nonce( $_POST["axe_at_options"], AXE_AT_PLUGIN_DIR ) ) wp_die(__('Cheatin&#8217; uh?'));
			
			$_POST = array_map( 'stripslashes_deep', $_POST );
			$_POST = array_map( 'trim', $_POST );

			//echo "<pre>\n". print_r( $_POST, true) ."</pre>";
			
			// Sanitize input data
			if ( empty($_POST['only_logged']) ) $_POST['only_logged'] = 'no';
			if ( empty($_POST['jq_selector']) ) {
				unset( $_POST['jq_selector'] );
			} else {
				$_POST['jq_selector'] = strip_tags( $_POST['jq_selector'] );
			}
			if ( empty($_POST['show_event']) || !in_array( $_POST['show_event'], axe_at_get_option_values('show_event') ) ) {
				unset( $_POST['show_event'] );
			}
			if ( empty($_POST['position_my']) || !in_array( $_POST['position_my'], axe_at_get_option_values('position') ) ) {
				unset( $_POST['position_my'] );
			}
			if ( empty($_POST['position_at']) || !in_array( $_POST['position_at'], axe_at_get_option_values('position') ) ) {
				unset( $_POST['position_at'] );
			}
			if ( empty($_POST['style_class']) || !in_array( $_POST['style_class'], axe_at_get_option_values('style_class') ) ) {
				unset( $_POST['style_class'] );
			}

			// Get current option values
			$axe_at_options = axe_at_get_options();
			
			// Update the options
			$axe_at_updated_options = array_merge( $axe_at_options, $_POST );

			// Delete all POST keys that are not in default keys (eg.g 'submit')and return the updated '$axe_at_options'
			$axe_at_options = array_intersect_key( $axe_at_updated_options, axe_at_get_default_options() );
			
			update_option('axe_at_options', $axe_at_options);

			add_action('user_admin_notices', create_function( '', 'echo \'<div id="message" class="updated"><p>'. __('Updated', AXE_AT_PLUGIN_DIR ).'</p></div>\';' ));
		}
		
		// Reset Defaults
		if ( isset($_POST['submit_defaults']) ) {

			if ( !wp_verify_nonce( $_POST["axe_at_options"], AXE_AT_PLUGIN_DIR ) ) wp_die(__('Cheatin&#8217; uh?'));
			
			update_option('axe_at_options', axe_at_get_default_options() );

			add_action('user_admin_notices', create_function( '', 'echo \'<div id="message" class="updated"><p>'. __('Defaults restored', AXE_AT_PLUGIN_DIR ).'</p></div>\';' ));
		}		
	}
}
add_action('admin_init', 'axe_at_save_options', 1);


/**
 * Add Rel attribute with user ID to avatar
 */
function axe_at_get_avatar ( $avatar, $id_or_email, $size, $default, $alt ) {
	global $user_ID, $pagenow;
	
	// In admin: filter only in plugin setting
	if ( is_admin() ) {
		$page = isset($_GET['page']) ? $_GET['page'] : '';
		if ( 'options-general.php' != $pagenow || $page != AXE_AT_PLUGIN_DIR.'/php/view-admin-options.php' ) return $avatar;
	}
		
	$id = 0;
	$email = false;
	
	if ( is_numeric($id_or_email) ) {

		$id = (int) $id_or_email;
		$user = get_userdata($id);
		$email = ( $user ) ? $user->user_email : false;
			
	} elseif ( is_object($id_or_email) ) {
		
		if ( !empty($id_or_email->user_id) ) {
			
			$id = (int) $id_or_email->user_id;
			$user = get_userdata($id);
			$email = ( $user ) ? $user->user_email : false;
			
		} elseif ( !empty($id_or_email->comment_author_email) ) {
			
			$email = $id_or_email->comment_author_email;
			$user = get_user_by('email', $email);
			if ( $user ) $id = $user->ID;
			
		}
	} else {
		$email = $id_or_email;
		$user = get_user_by('email', $email);
		if ( $user ) $id = $user->ID;
	}
		
	// Add REL attr to <img> tag
	if ( $email ) {
		// To check vs gravatar url
		$md5_email = md5( strtolower( trim( $email ) ) );
		
		// Delete existing Rel attr
		$avatar = preg_replace('/rel=[\'|"].*?[\'|"]/i', '', $avatar);
		
		// Add new Rel
		$avatar = str_replace ( '<img', '<img rel="'. urlencode( base64_encode( $md5_email.'|'.$id ) ).'"', $avatar );
		
		return $avatar;
	} else {
		return $avatar;
	}
}   
add_filter('get_avatar', 'axe_at_get_avatar', 100, 5 ); // Priority 100: be sure that the [rel] attribute is added after other plugin filters



/* EOF */

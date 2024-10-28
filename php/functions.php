<?php if (!defined ('ABSPATH')) die ('No direct access allowed');

/**
 * Core Functions
 *
 * Contains the functions used in plugin files
 *
 * @package Avatar Tooltip
 * @since 1.0
 */



/**
 * Return array of plugin options
 *
 * Merge saved options with defaults
 */
function axe_at_get_options () {

	// So default array (keys + values)
	$axe_at_default_options = axe_at_get_default_options(); 

	// Get saved options
	$axe_at_saved_options = get_option('axe_at_options', array());

	// So merge existing options with defaults
	return array_merge( $axe_at_default_options, $axe_at_saved_options );
}


/**
 * Return array of plugin option defaults
 */
function axe_at_get_default_options () {

	return array(
		'jq_selector'	=> '#main img.avatar[rel]',
		'only_logged' 	=> 'yes',
		'show_event' 	=> 'mouseover',
		'position_my'	=>'left center',
		'position_at'	=> 'right center',
		'style_class'	=> 'tipsy'
	);
}



/**
 * Return array of option values
 */
 
function axe_at_get_option_values ( $name='' ) {
	switch( $name ) {
		
		case 'bol':
			$values = array( 'yes', 'no' );
			break;
			
		case 'position':
			$values = array( 'center', 'left top', 'left center', 'left bottom', 'top left', 'top center', 'top right', 'right top', 'right center', 'right bottom', 'bottom left', 'bottom center', 'bottom right' );
			break;
			
		case 'show_event':
			$values = array( 'click', 'dblclick', 'mousemove', 'mouseover' );
			break;

		case 'style_class':
			$values = array( 'cream', 'plain', 'light', 'dark', 'red', 'green', 'blue', 'bootstrap', 'tipsy', 'youtube', 'jtools', 'cluetip', 'tipped' );
			break;
						
		default:			
			$values = array();
	}
	return apply_filters( 'axe_avatar_tooltip_option_values', $values, $name );	// Hook
}



/* EOF */

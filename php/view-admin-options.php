<?php if (!defined ('ABSPATH')) die ('No direct access allowed');
/** 
 * Options screen
 *
 * The plugin option screen
 *
 * @package Avatar Tooltip
 * @since 1.0
 */
 
if ( !current_user_can( 'manage_options' ) ) 	wp_die(__('Cheatin&#8217; uh?'));


global $user_email;



// Get the saved options, including defaults for empty values
$axe_at_options = axe_at_get_options();


do_action('user_admin_notices');

// echo "<pre>\n". print_r( $axe_at_options, true) ."</pre>";

?>
	

<div class="wrap" >

<div class="icon32" id="icon-options-general"><br></div>
<h2>Gravatar Tooltip</h2>


<!--
Open form
-->
<form action="" method="post">


<?php
/**********************************************************************
 * PREVIEW
 **********************************************************************/
?>

<div style="width: 100%;padding: 30px 0 10px;text-align: center;border: 1px dotted #ccc">
<div style="margin: 0 auto">
	<?php echo get_avatar( $user_email, 48 ) ?>
	<div style="margin-top: 10px"><span class="description">
		<?php _e( 'After you save option changes, you will be able to view the updated tooltip on your avatar here', AXE_AT_PLUGIN_DIR) ?>.
	</span></div>
</div>
</div>

<p>&nbsp;</p>


<?php
/**********************************************************************
 * OPTIONS
 **********************************************************************/
?>


<h1><?php _e( 'Options', AXE_AT_PLUGIN_DIR) ?></h1>

<table class="form-table"><tbody>


<?php
if ( $axe_at_options['only_logged'] != "no" ) {
	$checked_only_logged = 'checked="checked"';
} else {
	$checked_only_logged = "";
}
?>
<tr valign="top">
<th scope="row"><?php _e('Only members can view tooltips', AXE_AT_PLUGIN_DIR ) ?>:</th>
<td>
		<input type="checkbox" name="only_logged" id="only_logged" value="yes" <?php echo $checked_only_logged ?> />
		<label class="description" for="only_logged">
			<?php _e( 'If flagged, only logged-in users can view tooltips', AXE_AT_PLUGIN_DIR ) ?>.
			<?php _e( 'If not, also not logged visitors can view tooltips', AXE_AT_PLUGIN_DIR ) ?>.
		</label></td>
</tr>


<?php $jq_selector = $axe_at_options['jq_selector']; ?>
<tr valign="top">
<th scope="row"><?php _e('Selector(s) for tooltip', AXE_AT_PLUGIN_DIR) ?>:</th>
<td>
<input type="text" name='jq_selector' id='jq_selector' value="<?php echo format_to_edit( $jq_selector ) ?>" />
<span class="description"><br />
	<?php _e( 'The selector(s) that are used by jQuery to open tooltips', AXE_AT_PLUGIN_DIR ) ?>.
	<small>
	<br /><?php _e( 'Please pay attention', AXE_AT_PLUGIN_DIR ) ?>:<br />
	- <?php printf( __( 'it is recommended to add the %s attribute to selectors because the plugin automatically adds this attibute to avatars and the toolips are shown only if it exists', AXE_AT_PLUGIN_DIR ), '<code>[rel]</code>') ?><br />
	- <?php printf( __( 'it is a good idea to add a parent class (e.g. %s) to avoid that the toolip is shown on unwanted avatars (e.g. top Toolbar)', AXE_AT_PLUGIN_DIR ), '<code>#main</code>, <code>#content</code>') ?>
	</small>
</span>
</td>
</tr>


<?php $selected_show_event = $axe_at_options['show_event']; ?>
<tr valign="top">
<th scope="row"><?php _e('Event for tooltip', AXE_AT_PLUGIN_DIR) ?>:</th>
<td>
	<select name='show_event' id='show_event'>
		<?php foreach( axe_at_get_option_values('show_event') as $key ) :
			echo "<option value='$key' ". ( ( $key == $axe_at_options['show_event'] )? " selected='selected'": "") .">". esc_html( $key ). "</option>";
		endforeach; ?>
	</select>


</td>
</tr>



</tbody> </table>

<p>&nbsp;</p>


<?php
/**********************************************************************
 * APPEARANCE
 **********************************************************************/
?>

<h1><?php _e( 'Appearance' ) ?></h1>

<table class="form-table"><tbody>


<?php
$selected_position_my = $axe_at_options['position_my'];
$selected_position_at = $axe_at_options['position_at'];
?>
<tr valign="top">
<th scope="row"><?php _e('Tooltip position', AXE_AT_PLUGIN_DIR) ?>:</th>
<td>
	<label class="description" for="position_my"><?php _e('Put this corner/side of the tooltip', AXE_AT_PLUGIN_DIR) ?>:</label>

	<select name='position_my' id='position_my'>
		<?php foreach( axe_at_get_option_values('position') as $key ) :
			echo "<option value='$key' ". ( ( $key == $selected_position_my )? " selected='selected'": "") .">". esc_html( $key ). "</option>";
		endforeach; ?>
	</select>

	<label class="description" for="position_at"><?php _e('at this corner/side of the avatar', AXE_AT_PLUGIN_DIR) ?>:</label>
	
	<select name='position_at' id='position_at'>
		<?php foreach( axe_at_get_option_values('position') as $key ) :
			echo "<option value='$key' ". ( ( $key == $selected_position_at )? " selected='selected'": "") .">". esc_html( $key ). "</option>";
		endforeach; ?>
	</select>

</td>
</tr>


<?php $selected_style_class = $axe_at_options['style_class']; ?>
<tr valign="top">
<th scope="row"><?php _e('CSS class', AXE_AT_PLUGIN_DIR) ?>:</th>
<td>
	<select name='style_class' id='style_class'>
		<?php foreach( axe_at_get_option_values('style_class') as $key ) :
			echo "<option value='$key' ". ( ( $key == $selected_style_class )? " selected='selected'": "") .">". esc_html( $key ). "</option>";
		endforeach; ?>
	</select>

	<span class="description"><br />
		<?php
		echo __( 'You can customise further the styles', AXE_AT_PLUGIN_DIR ).': ';
		echo __( 'in plugin folder there is a css file where you can add more styles', AXE_AT_PLUGIN_DIR ).'  (axe-gravatar-tooltip.css).<br />';
		echo __( 'A tip: copy this file to your theme directory and edit it there. Useful to prevent the loss of styles when you upgrade the plugin.', AXE_AT_PLUGIN_DIR); ?>.
	</span>
</td>
</tr>

</tbody> </table>



<!--
Close form
-->

<p class="submit">
<?php wp_nonce_field( AXE_AT_PLUGIN_DIR, "axe_at_options" ); ?>
<input type="submit" name="submit_options" value="<?php _e('Update', AXE_AT_PLUGIN_DIR) ?>" class="button-primary" />
<input type="submit" name="submit_defaults" value="<?php _e('Restore defaults', AXE_AT_PLUGIN_DIR) ?>" class="button" onclick="return confirm('<?php echo esc_js( __("Do you confirm?", AXE_AT_PLUGIN_DIR) ) ?>')" />
</p>
</form>



</div><!-- end wrap -->


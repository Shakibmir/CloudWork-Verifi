<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
/**
 * @package CloudWork Verifi
 * @subpackage cw-verifi-admin.php
 * @version 0.1.2
 * @author Chris Kelley <chris@organicbeemedia.com)
 * @copyright Copyright � 2013 CloudWork Themes
 * @link http://cloudworkthemes.com
 * @since 0.1
 *
 * Table Of Contents
 *
 * cw_verifi_option_init
 * cw_verifi_add_page 
 * cw_verifi_setting_username
 * cw_verifi_setting_api
 * cw_verifi_options_page
 * cw_verifi_options_validate
 *
*/
if ( !class_exists( 'cw_Verifi_admin' ) ) :

final class cw_Verifi_admin{
	
	function __construct(){
		
		add_action('admin_init', array( $this , 'init') );
		
		add_action('admin_menu', array( $this, 'add_page' ));

	}
	
	/**
	* Adds Admin menu
	* 
	* @since 0.1
	* @access public
	* @return void
	*/
	function add_page() {

		add_options_page('CloudWork Verifi', 'CW Verifi', 'manage_options', 'cw-verifi-options', array( $this ,'cw_verifi_options_page' ));

	}
	
	/**
	* Register Settings and fields
	* 
	* @since 0.1
	* @access public
	* @return void
	*/
	function init(){
		
		register_setting('cw_verifi_options', 'cw_verifi_options', array( $this, 'cw_verifi_options_validate') );
	
		add_settings_section('cw_verifi_general_settings', __('General Settings', 'cw-verifi') , array( $this, 'cw_setting_callback') , __FILE__);
	
		add_settings_field('cw_verifi_username', __('Envato Username', 'cw-verifi') , array( $this, 'cw_verifi_setting_username' ), __FILE__,'cw_verifi_general_settings');
	
		add_settings_field('cw_verifi_api_key', __('Envato API Key', 'cw-verifi') , array( $this, 'cw_verifi_setting_api') ,__FILE__, 'cw_verifi_general_settings');

	}
	
	
	/**
	 * Callback String for the General Settings section
	 * 
	 * @since 0.1.2
	 * @access public
	 * @return string
	 */
	function  cw_setting_callback() {

		echo '<p>This plugin adds an extra field to the wp-login.php registration form. <br /> You can also insert a form on the front-end with this shortcode <code>[cw-verifi-registration]</code></p>';
	
	}
	/**
	* Callback function for cw_verifi_username
	* 
	* @access public
	* @return string
	*/
	function cw_verifi_setting_username() {

		$options = get_option('cw_verifi_options');
		
		echo "<input id='plugin_text_string' name='cw_verifi_options[username]' size='40' type='text' value='{$options['username']}' />";
		
	}

	/**
	* Callback function for cw_verifi_api_key
	* 
	* @since 0.1
	* @access public
	* @return string
	*/
	function cw_verifi_setting_api() {

		$options = get_option('cw_verifi_options');
	
		echo "<input id='plugin_text_string' name='cw_verifi_options[api_key]' size='40' type='text' value='{$options['api_key']}' />";

	}

	/**
	* Creates Options Page
	* 
	* @since 0.1
	* @access public
	* @return string
	*/
	function cw_verifi_options_page() {
	?>
		<div class="wrap">
	
			<div class="icon32" id="icon-options-general"><br></div>
		
			<h2><?php _e('CloudWork Verifi', 'cw-verifi') ?></h2>
				
			<form action="options.php" method="post">
		
				<?php settings_fields('cw_verifi_options'); ?>
		
				<?php do_settings_sections(__FILE__); ?>
		
				<p class="submit">
		
					<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'cw-verifi'); ?>" />
		
				</p>
		
			</form>
	
		</div><?php

	}
	
	/**
	* Validates fields 
	* 
	* @since 0.1
	* @access public
	* @param mixed $input
	* @return string
	*/
	function cw_verifi_options_validate($input) {

		$input['username'] =  wp_filter_nohtml_kses($input['username']);	
	
		$input['api_key'] =  wp_filter_nohtml_kses($input['api_key']);	

		return $input; // return validated input
	
	}


}

endif; //end if class exists

//High Five
$cw_verifi_admin = new cw_Verifi_admin;
//Low Five
?>
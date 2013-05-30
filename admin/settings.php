<?php

add_filter('cwv_general_settings', 'cwv_general_options');
	
/**
 * cwtu_general_options function.
 * 
 * @since 0.4
 * @access public
 * @return void
 */
function cwv_general_options() {

	$options = array();
		
	//Maintance Settings Tab
	$options[] = array(
		'name' => __('General', 'cwtu'),
		'type' => 'heading');	
		
	$options[] = array(
		'name' => __('Envato Username', 'cwtu'),
		'desc' => __('Please enter your Envato Username', 'cwtu'),
		'id' => 'username',
		'std' => '',
		'type' => 'text');
		
	$options[] = array(
		'name' => __('Envato API Key', 'cwtu'),
		'desc' => __('Please enter your Envato API key', 'cwtu'),
		'id' => 'api_key',
		'std' => '',
		'type' => 'text');
				
	$options[] = array(
		'name' => __('Redirect URL', 'cwtu'),
		'desc' => __('This controls the redirect after signup', 'cwtu'),
		'id' => 'cw_redirect_url',
		'std' => get_home_url() ,
		'type' => 'text');	
			
	return $options;
	
}
?>
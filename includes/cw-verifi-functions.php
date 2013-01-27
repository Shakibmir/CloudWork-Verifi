<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
/**
 * @package CloudWork Verifi
 * @subpackage cw-verifi-functions.php
 * @version 0.2
 * @author Chris Kelley <chris@organicbeemedia.com)
 * @copyright Copyright © 2013 CloudWork Themes
 * @link http://cloudworkthemes.com
 * @since 0.1
 *
 * Table Of Contents
 *
 * cw_get_user_by_meta_data
 * cw_purchase_exists 
 * cw_validate_api
 * cw_get_purchase_data
 *
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
* Gets user by meta key and meta value
* This function may be depreciated in future version as its not
* directly used by the plugin as of 0.2 
*
* @thanks Tom Mcfarlin http://tommcfarlin.com
* @since 0.1
* @uses WP_User_Query
* @access public
* @param mixed $meta_key
* @param mixed $meta_value
* @return mixed
*/
function cw_get_user_by_meta_data( $meta_key, $meta_value ) {

	// Query for users based on the meta data
	$user_query = new WP_User_Query(
			
		array(
			'meta_key'	  =>	$meta_key,
			'meta_value'	=>	$meta_value
		)
		
	);
	
	// Get the results from the query, returning the first user
	$users = $user_query->get_results();

	return $users;

}
	
/**
* Checks to see purchase already exists
* 
* @since 0.1
* @uses WP_User_Query
* @access public
* @param mixed $cw_purcahse_code
* @return bool
*/
function cw_purchase_exists( $input ) {

	// Query for users based on the meta data
	$user_query = new WP_User_Query(
		array(
			'meta_query' => array(
				array(
				'key' => '_cw_purchase_code',
				'value' => strval($input),
				'compare' => 'like',
				)		
			)
		
		)
	);
		
	if ( 	$users = $user_query->get_results() ) {
	
		return true;
	
	} else {
	
		return false;
	
	}
	
}
	
/**
* Check if a API retruns buyer, Uses Envato Marketplace Class
* 
* @since 0.1
* @access public
* @param mixed $cw_purcahse_code
* @return bool
*/
function cw_validate_api( $cw_purcahse_code ){
	 	 	
	global $verifi;
				
	$market_class = $verifi->envato;
		
	$test_name = $verifi->username;
		
	$verify = $market_class->verify_purchase( $test_name , $cw_purcahse_code);
	
	if (isset($verify->buyer)) {
		
		return true;
			
	} else {
			
		return false;
		
	}
	
}

/**
 * Pulls all data from API and returns array.
 * 
 * @since 0.2
 * @access public
 * @param mixed $purchase_code
 * @return array
 */
function cw_get_purchase_data($purchase_code){
	
	global $verifi;
				
	$market_class = $verifi->envato;
		
	$test_name = $verifi->username;
		
	$api_check = $market_class->verify_purchase( $test_name , $purchase_code);
	
	$meta = array(
		"purchase_code" => $purchase_code,
		"item_name" =>$api_check->item_name,
		"item_id" => $api_check->item_id,
		"created_at" => $api_check->created_at,
		"buyer" => $api_check->buyer ,
		"licence" =>   $api_check->licence
	);
	
	return $meta;
	
}	
?>
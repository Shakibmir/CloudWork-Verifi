<?php
/**
 * @package CloudWork Verifi
 * @subpackage utility-functions.php
 * @version 0.4
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
		
	if ( $users = $user_query->get_results() ) {
	
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
				
	$verify = $market_class->verify_purchase( $cw_purcahse_code);
	
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
				
	$api_check = $market_class->verify_purchase( $purchase_code);
	
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

/**
 * Custom function to register users.
 * 
 * In prior versions this function was used in multiple classes, we're getting rid of that excess code!
 * 
 * @since 0.4
 * @access public
 * @return void
 */
function cw_verifi_register_user($user_login, $user_email, $user_pass, $confirm_pass, $purchase_code){
		
		$errors = new WP_Error();
		
		$sanitized_user_login = sanitize_user( $user_login );
		
		$user_email = apply_filters( 'user_registration_email', $user_email );
		
		$user_pass = $user_pass;
		
		$confirm_pass = $confirm_pass;
		
		$purchase_code = $purchase_code;

		// Check the username
		if ( $sanitized_user_login == '' ) {
		
			$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username.', 'cw-verifi' ) );
		
		} elseif ( ! validate_username( $user_login ) ) {
		
			$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.', 'cw-verifi' ) );
			
		} elseif ( username_exists( $sanitized_user_login ) ) {
		
			$errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered. Please choose another one.', 'cw-verifi' ) );
		
		}
		
		if ( $sanitized_user_login === $user_pass ) {
						
			$errors->add( 'bad_combo', __( '<strong>ERROR</strong>: Your Username and Password cannot match.', 'cw-verifi' ) );

			
		}
		// Check the e-mail address
		if ( $user_email == '' ) {
			
			$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.', 'cw-verifi' ) );
			
		} elseif ( ! is_email( $user_email ) ) {
			
			$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.', 'cw-verifi' ) );
			
			$user_email = '';
			
		} elseif ( email_exists( $user_email ) ) {
				
			$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.', 'cw-verifi' ) );
		}
			
		// Check the password fields
			
		if ( $user_pass == '' ) {
				
			$errors->add( 'empty_pass', __( '<strong>ERROR</strong>: Please enter a password.', 'cw-verifi' ) );
		
		} elseif ( $user_pass != $confirm_pass ) {
				
			$errors->add( 'pass_match', __( '<strong>ERROR</strong>: Your passwords dont match!', 'cw-verifi' ) );
			
		} elseif( strlen($user_pass) < 6 ){ 
		
			$errors->add( 'short_pass', __( '<strong>ERROR</strong>: Your Password is too short.', 'cw-verifi' ) );

		}
			
		if (  $confirm_pass == '' ){
			
			$errors->add( 'empty_confirm', __( '<strong>ERROR</strong>: Please confirm your password', 'cw-verifi' ) );
			
		}
				// Check the purchase code
		if ( $purchase_code == '' ) {

			$errors->add( 'empty_purchase_code', __( '<strong>ERROR</strong>: Please enter your purchase code', 'cw-verifi' ) );
		
		} elseif ( !cw_validate_api( $purchase_code, true ) ) {

			$errors->add( 'invalid_purchase_code', __( '<strong>ERROR</strong>: Please enter a valid purchase code', 'cw-verifi' ) );

		} elseif ( cw_purchase_exists( $purchase_code ) ) {

			$errors->add( 'used_purchase_code', __( '<strong>ERROR</strong>: Sorry this purchase code exsits', 'cw-verifi' ) );

		}
				
		do_action( 'register_post', $sanitized_user_login, $user_email, $errors );

		$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email, $user_pass, $confirm_pass );

		if ( $errors->get_error_code() )
		
			return $errors;
			
		$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
		
		if ( ! $user_id ) {
			
			$errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', 'cw-verifi' ), get_option( 'admin_email' ) ) );
		
		return $errors;
	
		}
	
		$meta = cw_get_purchase_data($purchase_code);			

		//Add all meta to db
		update_user_meta( $user_id, '_cw_purchase_code' , $meta );
				
		wp_new_user_notification( $user_id );	
		
		//Lets set a cookie for 60 minutes so we can display cool messages 
		if(!isset($_COOKIE['cw_verifi_new_user'])){
		
			ob_start();
			
			setcookie('cw_verifi_new_user', 1,  time() + (60 * 60), COOKIEPATH, COOKIE_DOMAIN, false );
			
			ob_end_flush();
			
		}
			
		$credentials = array();
		
		$credentials['user_login'] = $user_login;
		
		$credentials['user_password'] = $user_pass;
		
		$credentials['remember'] = true;
		
		wp_signon( $credentials );
		
		return $user_id;

}

if ( ! function_exists('cw_pretty_print') ) {
/**
 * Pretty Print is a utility function for testing stuff!!!
 * 
 * @since 0.4
 * @access public
 * @param mixed $array
 * @return void
 */
function cw_pretty_print($array){
	
	echo '<pre>';
    print_r( $array );
    echo '</pre>';
    
}

}
//I have no legs, I have no legs.
?>
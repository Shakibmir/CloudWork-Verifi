<?php

/**
 * @package CloudWork Verifi
 * @subpackage cw-verifi-login.php
 * @version 0.3
 * @author Chris Kelley <chris@organicbeemedia.com)
 * @copyright Copyright © 2013 CloudWork Themes
 * @link http://cloudworkthemes.com
 * @since 0.3
 *
 * This file does some Ninja Shit
 * Table Of Contents
 *
 * cw_Verfi_UserShort
 *	__construct
 *	register_user
 *	register_form
 *	login_scripts
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'cw_Verfi_Login' ) ) :

class cw_Verfi_Login {
	
	public function __construct() {
				
		add_action('login_form_register', array($this, 'register_form'), 0);
		
		add_action('login_footer', array( $this, 'login_scripts' ));
	
	}
	
	/**
	 * This is where we register new users
	 * 
	 * Make sure all default WordPress actions and filters are intact to avoid breakage
	 *
	 * @since 0.3
	 * @access public
	 * @param mixed $user_login
	 * @param mixed $user_email
	 * @param mixed $user_pass
	 * @param mixed $confirm_pass
	 * @return init
	 */
	function register_user($user_login, $user_email, $user_pass, $confirm_pass, $purchase_code){
		
		$errors = new WP_Error();

		$sanitized_user_login = sanitize_user( $user_login );
		
		$user_email = apply_filters( 'user_registration_email', $user_email );
		
		$user_pass = $user_pass;
		
		$confirm_pass = $confirm_pass;
		
		$purchase_code = $purchase_code;

		// Check the username
		if ( $sanitized_user_login == '' ) {
		
			$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username.' ) );
		
		} elseif ( ! validate_username( $user_login ) ) {
		
			$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.' ) );
			$sanitized_user_login = '';
			
		} elseif ( username_exists( $sanitized_user_login ) ) {
		
			$errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered. Please choose another one.' ) );
		
		}

		// Check the e-mail address
		if ( $user_email == '' ) {
			
			$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.' ) );
			
		} elseif ( ! is_email( $user_email ) ) {
			
			$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.' ) );
			
			$user_email = '';
			
		} elseif ( email_exists( $user_email ) ) {
				
			$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.' ) );
		}
			
		// Check the password fields
			
		if ( $user_pass == '' ) {
				
			$errors->add( 'empty_pass', __( '<strong>ERROR</strong>: Please enter a password.' ) );
		
		} elseif ( $user_pass != $confirm_pass ) {
				
			$errors->add( 'pass_match', __( '<strong>ERROR</strong>: Your passwords dont match!' ) );
			
		} elseif( strlen($user_pass) < 6 ){ 
		
			$errors->add( 'short_pass', __( '<strong>ERROR</strong>: Your Password is too short.' ) );

		}
			
		if (  $confirm_pass == '' ){
			
			$errors->add( 'empty_confirm', __( '<strong>ERROR</strong>: Please confirm your password' ) );
			
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
			
			$errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !' ), get_option( 'admin_email' ) ) );
		
		return $errors;
	
		}
	
		$meta = cw_get_purchase_data($purchase_code);			

		//Add all meta to db
		update_user_meta( $user_id, '_cw_purchase_code' , $meta );
				
		wp_new_user_notification( $user_id );	
		
		//Lets set a cookie for 10 minutes so we can display cool messages 
		if(!isset($_COOKIE['cw_verifi_new_user'])){
			
			setcookie('cw_verifi_new_user', 1,  time() + (60 * 10), COOKIEPATH, COOKIE_DOMAIN, false );
			
		}
			
		$credentials = array();
		$credentials['user_login'] = $user_login;
		$credentials['user_password'] = $user_pass;
		$credentials['remember'] = true;
		
		wp_signon( $credentials );
		
		return $user_id;
	
	}

	/**
	 * This will override the default registration form
	 * 
	 * Make sure all default WordPress actions and filters are intact to avoid breakage
	 *
	 * @since 0.3
	 * @access public
	 * @return mixed
	 */
	function register_form(){
	
		if($_REQUEST['action'] = 'register'){
			
			$http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
			
			$errors = new WP_Error();

			if ( is_multisite() ) {
				
				// Multisite uses wp-signup.php
				wp_redirect( apply_filters( 'wp_signup_location', network_site_url('wp-signup.php') ) );
			
				exit;
			}

			if ( !get_option('users_can_register') ) {
				
				wp_redirect( site_url('wp-login.php?registration=disabled') );
			
				exit();
			
			}
	
			$user_login = '';
			
			$user_email = '';
			
			$user_pass = '';
			
			$confirm_pass = '';
			
			$purchase_code = '';
			
			if ( $http_post ) {
				
				$user_login = $_POST['user_login'];
				
				$user_email = $_POST['user_email'];
				
				$user_pass = $_POST['user_pass'];
				
				$confirm_pass = $_POST['confirm_pass'];
				
				$purchase_code = $_POST['purchase_code'];
				
				$errors = $this->register_user($user_login, $user_email, $user_pass, $confirm_pass, $purchase_code);
				
				if ( !is_wp_error($errors) ) {
			
					$redirect_to = apply_filters( 'cw_verifi_redirect', !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : home_url() );
							
					wp_safe_redirect( $redirect_to );
				
				exit();
				
				}
					
			}

			$redirect_to = apply_filters( 'registration_redirect', !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '' );
			
			login_header(__('Registration Form'), '<p class="message register">' . __('Register For This Site') . '</p>', $errors);
			
			?>

			<form name="registerform" id="registerform" action="<?php echo esc_url( site_url('wp-login.php?action=register', 'login_post') ); ?>" method="post">
				
				<p>
					
					<label for="user_login"><?php _e('Username', 'cw-verifi' ) ?><br />
					
					<input type="text" name="user_login" id="user_login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" size="20" /></label>
				
				</p>
				
				<p>
					
					<label for="user_email"><?php _e('E-mail', 'cw-verifi') ?><br />
					
					<input type="text" name="user_email" id="user_email" class="input" value="<?php echo esc_attr(stripslashes($user_email)); ?>" size="25" /></label>
				
				</p>
				
				<p>
					
					<label for="user_pass"><?php _e('Password', 'cw-verifi') ?><br />
					
					<input type="password" name="user_pass" id="user_pass" class="input" value="<?php echo esc_attr(stripslashes($user_pass)); ?>" size="25" /></label>
				
				</p>
				
				<p>
					
					<label for="confirm_pass"><?php _e('Confirm Password', 'cw-verifi') ?><br />
					
					<input type="password" name="confirm_pass" id="confirm_pass" class="input" value="<?php echo esc_attr(stripslashes($confirm_pass)); ?>" size="25" /></label>
				
				</p>
				
				<p>
					
					<label for="purchase_code"><?php  _e('Purchase Code', 'cw-verifi')?><span>&nbsp;(<a class="thickbox" href="<?php echo  trailingslashit( CWV_IMAGES ) . 'purchasecode.jpg'; ?>">what's this</a>)</span><br />
	
					<input type="text" name="purchase_code" id="purchase_code" class="input" value="<?php echo esc_attr(stripslashes($purchase_code)); ?>" size="20"  /></label>

				</p>
				
				<?php do_action('register_form'); ?>
				
				<p>Password must be at least 6 characters</p>
				
				<br class="clear" />
				
				<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
				
				<p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e('Register'); ?>" /></p>
				
			</form>

			<p id="nav">
				
				<a href="<?php echo esc_url( wp_login_url() ); ?>"><?php _e( 'Log in' ); ?></a> 
				
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" title="<?php esc_attr_e( 'Password Lost and Found' ) ?>"><?php _e( 'Lost your password?' ); ?></a>
			</p>

			<?php

		login_footer('user_login');
		
		//This prevents the switch from running
		exit;
		
		}
		
	}
	/**
	 * login_scripts function.
	 * 
	 * @since 0.3
	 * @access public
	 * @return void
	 */
	function login_scripts(){
		
		wp_enqueue_script('jquery');
		
		wp_enqueue_script('thickbox', null,  array('jquery'));
		
		wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
	
		wp_enqueue_style( 'cw-verifi-css', trailingslashit( CWV_CSS ) . 'cw-verifi-login.css', null, '1.0' );

		wp_enqueue_script('cw-verifi-check',trailingslashit( CWV_JS) . 'cw-verifi-check.js' );

	}
	
}

endif; //end if class exists

//Remember Kids
$verifi_login = new cw_Verfi_Login;
//Drugs are bad
?>
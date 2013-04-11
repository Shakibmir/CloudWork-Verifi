<?php

/**
 * @package CloudWork Verifi
 * @subpackage registration-form.php
 * @version 0.4
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

if ( !class_exists( 'cw_Verfi_Registration' ) ) :

class cw_Verfi_Registration {
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
				
		add_action('login_form_register', array( &$this, 'register_form'), 0);
		
		add_action('login_enqueue_scripts', array( &$this, 'login_scripts' ));
			
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
	
		if( $_REQUEST['action'] = 'register'){
			
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
				
				$errors = cw_verifi_register_user($user_login, $user_email, $user_pass, $confirm_pass, $purchase_code);
				
				if ( !is_wp_error($errors) ) {
			
					$options = get_option('cw_verifi_options');
					
					$redirect_url = $options['cw_redirect_url'];

					$redirect_to = apply_filters( 'cw_verifi_redirect', !empty( $_REQUEST['redirect_to'] ) ? $_REsQUEST['redirect_to'] : $redirect_url );
												
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
					
					<input type="text" name="user_login" id="user_login" class="input cw_username" value="<?php echo esc_attr(stripslashes($user_login)); ?>" size="20" /></label>
				
				</p>
				
				<p>
					
					<label for="user_email"><?php _e('E-mail', 'cw-verifi') ?><br />
					
					<input type="text" name="user_email" id="user_email" class="input" value="<?php echo esc_attr(stripslashes($user_email)); ?>" size="25" /></label>
				
				</p>
				
				<p>
					
					<label for="user_pass"><?php _e('Password', 'cw-verifi') ?><br />
					
					<input type="password" name="user_pass" id="user_pass" class="input cw_pass" value="<?php echo esc_attr(stripslashes($user_pass)); ?>" size="25" /></label>
				
				</p>
				
				<p>
					
					<label for="confirm_pass"><?php _e('Confirm Password', 'cw-verifi') ?><br />
					
					<input type="password" name="confirm_pass" id="confirm_pass" class="input cw_confirm" value="<?php echo esc_attr(stripslashes($confirm_pass)); ?>" size="25" /></label>
				
				</p>
				
				<div id="pass-strength-result"><?php _e('Strength indicator'); ?></div>
				

				<p>
					
					<label for="purchase_code"><?php  _e('Purchase Code', 'cw-verifi')?><span>&nbsp;(<a class="thickbox" href="<?php echo  trailingslashit( CWV_IMAGES ) . 'purchasecode.jpg'; ?>">what's this</a>)</span><br />
	
					<input type="text" name="purchase_code" id="purchase_code" class="input" value="<?php echo esc_attr(stripslashes($purchase_code)); ?>" size="20"  /></label>

				</p>
				
				<?php do_action('register_form'); ?>
				
				<p><?php _e('Password must be at least 7 characters', 'cw-verifi'); ?></p>
				
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
		
		//This prevents the switch from running and duplicating login form
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
	
		wp_enqueue_style( 'cw-login-css', trailingslashit( CWV_CSS ) . 'login.css', null, '1.0' );

		wp_enqueue_script('cw-pass-strength' );

	}
	
}

endif; //end if class exists

//Remember Kids
$verifi_registration = new cw_Verfi_Registration;
//Drugs are bad
?>
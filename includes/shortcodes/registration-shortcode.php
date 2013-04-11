<?php

/**
 * @package CloudWork Verifi
 * @subpackage cw-verifi-shortcode.php
 * @version 0.2
 * @author Chris Kelley <chris@organicbeemedia.com)
 * @copyright Copyright © 2013 CloudWork Themes
 * @link http://cloudworkthemes.com
 * @since 0.1
 *
 * Table Of Contents
 *
 * cw_Verfi_Shortcode
 *	registration_form
 *	errors
 *	enqueue_scripts
 *	registration
 *	register_new_user
 *	display_message
 *
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'cwv_Registration_Shortcode' ) ) :

final class cwv_Registration_Shortcode {

	private static $add_script;

	private $success = '';
	
	public function __construct() {
		
		add_shortcode('cw-verifi-registration', array( &$this, 'registration_form'));
		
		add_action('init', array( &$this, 'register_user' ));

		add_action('wp_footer', array( &$this , 'enqueue_scripts'));
		
	}
	
	 /**
	  * Create the Registration form shortcode
	  *
	  * @since 0.1
	  * @access public
	  * @return string
	  */
	 function registration_form() {
		
		self::$add_script = true;					

		$output ='';
		
		//Who wants to Register if theyre logged in
		if(!is_user_logged_in()) {

			//Check to see if user registration is enabled 
			$can_register = get_option( 'users_can_register' );
		
			//only display form if registration is allowed
			if ( $can_register ){
			
				$output = $this->registration();
			
			} else {
			
				$output = apply_filters( 'cw_verifi_closed_message', __('Sorry Registration is Closed', 'cw-verifi'));
					
			}
		
		} else {
			
			$output = apply_filters( 'cw_verifi_logged_message', __('You&apos;re already logged in', 'cw-verifi') );
			
		}
		
		return $output;

	}
	
	/**
	 * Load Scripts and Styles.
	 *
	 * @since 0.1 
	 * @access public
	 * @return void
	 */
	function enqueue_scripts() {
				
		if ( ! self::$add_script )
		
			return;
			
		wp_enqueue_script('jquery');
		
		wp_enqueue_script('thickbox', null,  array('jquery'));
		
		wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
		
		wp_enqueue_script('cw-pass-strength');

		wp_enqueue_style( 'cw-reg-shortcode', trailingslashit( CWV_CSS ) . 'shortcode-registration.css', null, CWV_VERSION );
		
		do_action( 'cw_verifi_shortcode_scripts' );
		
	}
	/**
	 * Load Errors
	 * 
	 * Thanks Pippin http://pippinsplugins.com
	 * @since 0.1
	 * @access public
	 * @return mixed
	 */
	function errors(){
	
		static $wp_error; // Will hold global variable safely
    
		return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
	
	}
	
	/**
	 * Registration Form Output, based of the default login form found wp-login.php
	 * 
	 * @since 0.1
	 * @access public
	 * @return string
	 * @todo filter
	 */
	function registration(){
	
		?>
	
		<h3 class="cw-verifi-header"><?php _e('Register New Account', 'cw-verifi'); ?></h3> 
		
		<?php $this->display_message(); ?>
		
		<form id="cw-verifi-form" class="verifi-form" action="" method="POST">
			
				<p>
				
					<label for="cw_verifi_user_name"><?php _e('Username', 'cw-verifi' ); ?><br />
				
					<input name="cw_verifi_user_name" id="cw_verifi_user_name" class="required cw_username" type="text" placeholder="<?php _e('Please enter a unique username', 'cw-verifi');?>"/></label>
				
				</p>
				<p>
				
					<label for="cw_verifi_user_email"><?php _e('Email', 'cw-verifi'); ?><br />
				
					<input name="cw_verifi_user_email" id="cw_verifi_user_email" class="required" type="text" placeholder="<?php _e('Please enter your email', 'cw-verifi');?>"/></label>
				
				</p>
				<p>
					
					<label for="cw_verifi_user_pass"><?php _e('Password', 'cw-verifi') ?><br />
					
					<input type="password" name="cw_verifi_user_pass" id="cw_verifi_user_pass" class="required cw_pass"  size="25" /></label>
				
				</p>
				
				<p>
					
					<label for="cw_verifi_confirm_pass"><?php _e('Confirm Password', 'cw-verifi') ?><br />
					
					<input type="password" name="cw_verifi_confirm_pass" id="cw_verifi_confirm_pass" class="required cw_confirm" size="25" /></label>
				
				</p>
				
				<div id="pass-strength-result"><?php _e('Strength indicator'); ?></div>
				<p>
				
					<label for="cw_verifi_purchase_code"><?php _e('Purchase Code', 'cw-verifi'); ?><span>&nbsp;(<a class="thickbox" href="<?php echo  trailingslashit( CWV_IMAGES ) . 'purchasecode.jpg'; ?>">whats this</a>)</span><br />
					
					<input name="cw_verifi_purchase_code" id="cw_verifi_purchase_code" class="required" type="text" placeholder="<?php _e('Please enter your purchase code', 'cw-verifi');?>"/></label>
				
				</p>
				
				<p><?php _e('Password must be at least 7 characters', 'cw-verifi'); ?></p>

				<p>
				
					<input type="hidden" name="cw_verifi_nonce" value="<?php echo wp_create_nonce('cw-verifi-nonce'); ?>"/>
				
					<input type="submit" id="cw_verifi_nonce" value="<?php _e('Register an Account', 'cw-verifi'); ?>"/>
				
				</p>
					
		</form>
		
		<?php 
		
		}
		function register_user(){
	
			if (isset( $_POST["cw_verifi_user_name"] ) &&  wp_verify_nonce($_POST['cw_verifi_nonce'], 'cw-verifi-nonce')) {
				
				$sanitized_user_login = sanitize_user($_POST["cw_verifi_user_name"]);	
			
				$user_email	= apply_filters( 'user_registration_email', $_POST["cw_verifi_user_email"]);
				
				$user_pass = $_POST["cw_verifi_user_pass"];
		
				$confirm_pass = $_POST["cw_verifi_confirm_pass"];
				
				$purchase_code = $_POST["cw_verifi_purchase_code"];

				// Check the username
				if ( $sanitized_user_login == '' ) {
		
					$this->errors()->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username.', 'cw-verifi' ) );
		
				} elseif ( ! validate_username( $sanitized_user_login ) ) {
		
					$this->errors()->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.', 'cw-verifi' ) );
			
				} elseif ( username_exists( $sanitized_user_login ) ) {
		
					$this->errors()->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered. Please choose another one.', 'cw-verifi' ) );
		
				}
		
				if ( $sanitized_user_login === $user_pass ) {
						
					$this->errors()->add( 'bad_combo', __( '<strong>ERROR</strong>: Your Username and Password cannot match.', 'cw-verifi' ) );
			
				}
	
				// Check the e-mail address
				if ( $user_email == '' ) {
			
					$this->errors()->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.', 'cw-verifi' ) );
			
				} elseif ( ! is_email( $user_email ) ) {
			
					$this->errors()->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.', 'cw-verifi' ) );
			
					$user_email = '';
			
				} elseif ( email_exists( $user_email ) ) {
				
					$this->errors()->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.', 'cw-verifi' ) );
				
				}
			
				// Check the password fields
				if ( $user_pass == '' ) {
				
					$this->errors()->add( 'empty_pass', __( '<strong>ERROR</strong>: Please enter a password.', 'cw-verifi' ) );
		
				} elseif ( $user_pass != $confirm_pass ) {
				
					$this->errors()->add( 'pass_match', __( '<strong>ERROR</strong>: Your passwords dont match!', 'cw-verifi' ) );
			
				} elseif( strlen($user_pass) < 6 ){ 
		
					$this->errors()->add( 'short_pass', __( '<strong>ERROR</strong>: Your Password is too short.', 'cw-verifi' ) );

				}
			
				if (  $confirm_pass == '' ){
			
					$this->errors()->add( 'empty_confirm', __( '<strong>ERROR</strong>: Please confirm your password', 'cw-verifi' ) );
			
				}
				
				// Check the purchase code
				if ( $purchase_code == '' ) {

					$this->errors()->add( 'empty_purchase_code', __( '<strong>ERROR</strong>: Please enter your purchase code', 'cw-verifi' ) );
		
				} elseif ( !cw_validate_api( $purchase_code, true ) ) {

					$this->errors()->add( 'invalid_purchase_code', __( '<strong>ERROR</strong>: Please enter a valid purchase code', 'cw-verifi' ) );

				} elseif ( cw_purchase_exists( $purchase_code ) ) {

					$this->errors()->add( 'used_purchase_code', __( '<strong>ERROR</strong>: Sorry this purchase code exsits', 'cw-verifi' ) );

				}
					

				if ( $this->errors()->get_error_code() ){
	
					$errors = $this->errors()->get_error_messages();

					return $errors;
			
				} 
				
				$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
		
				if ( ! $user_id ) {
			
					$this->errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', 'cw-verifi' ), get_option( 'admin_email' ) ) );
		
					return $errors;
	
				}
	
				$meta = cw_get_purchase_data($purchase_code);			

				//Add all meta to db
				update_user_meta( $user_id, '_cw_purchase_code' , $meta );
				
				wp_new_user_notification( $user_id );	
		
				//Lets set a cookie for 60 minutes so we can display cool messages 
				if(!isset($_COOKIE['cw_verifi_new_user'])){
		
					setcookie('cw_verifi_new_user', 1,  time() + (60 * 60), COOKIEPATH, COOKIE_DOMAIN, false );
							
				}
			
				$credentials = array();
		
				$credentials['user_login'] = $sanitized_user_login;
		
				$credentials['user_password'] = $user_pass;
		
				$credentials['remember'] = true;
		
				wp_signon( $credentials );
				
				$options = get_option('cw_verifi_options');
					
				$redirect_url = $options['cw_redirect_url'];

				$redirect_to = apply_filters( 'cw_verifi_redirect', !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : $redirect_url );
											
				wp_safe_redirect( $redirect_to );  exit;		
				
			}//end if
		
		}
			/**
	 * Displays error or succuess message
	 * 
	 * @since 0.1
	 * @access public
	 * @return string
	 */
	function display_message(){
	
		if($error_codes = $this->errors()->get_error_codes()) {
			
			echo '<div class="cw_verifi_errors">';
			
			// Loop error codes and display errors
			foreach($error_codes as $code){
					        	
		       	$message = $this->errors()->get_error_message($code);
		        
		       	echo '<span class="cw-error">' . $message . '</span><br/>';
		        
		   }
		       
		   echo '</div>';
		   
		}
	
	}
	

}

endif; //end if class exists

//VooDoo Magic 
$verifi_shortcode = new cwv_Registration_Shortcode();
//Poof!
?>
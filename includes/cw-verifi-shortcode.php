<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
/**
 * @package CloudWork Verifi
 * @subpackage cw-verifi-shortcode.php
 * @version 1.0
 * @author Chris Kelley <chris@organicbeemedia.com)
 * @copyright Copyright © 2013 CloudWork Themes
 * @link http://cloudworkthemes.com
 * @since 0.1
 *
 * Table Of Contents
 *
 * cw_Verfi_Shortcode
 *	registration_form
 *	register_script
 *	enqueue_scripts
 *	registration
 *	errors
 *	register_new_user
 *	display_message
 *
*/

if ( !class_exists( 'cw_Verfi_Shortcode' ) ) :

class cw_Verfi_Shortcode {

	private static $add_script;

	private $success = '';
	
	public function __construct() {
		
		add_shortcode('cw-verifi-registration', array( $this, 'registration_form'));
		
		add_action('init', array( $this, 'register_new_user' ));

		add_action('wp_enqueue_scripts', array( $this , 'register_script'));
		
		add_action('wp_footer', array( $this , 'enqueue_scripts'));
		
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
			
				$output = __('Sorry Registration is Closed', 'cw-verifi');
					
			}
		
		} else {
			
			$output = __('Your are already logged in', 'cw-verifi');
			
		}
		
		return $output;

	}
	
	/**
	 * Register the css
	 * 
	 * @since 0.1
	 * @access public
	 * @return void
	 */
	function register_script() {
	
		wp_register_style('cw-verifi-css', trailingslashit( CWV_CSS ) . 'cw-verifi.css', null, '1.0');
	
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
		
		wp_enqueue_style( 'cw-verifi-css' );
		
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
				
					<input name="cw_verifi_user_name" id="cw_verifi_user_name" class="required" type="text" placeholder="<?php _e('Please enter a unique username', 'cw-verifi');?>"/></label>
				
				</p>
				<p>
				
					<label for="cw_verifi_user_email"><?php _e('Email', 'cw-verifi'); ?><br />
				
					<input name="cw_verifi_user_email" id="cw_verifi_user_email" class="required" type="text" placeholder="<?php _e('Please enter your email', 'cw-verifi');?>"/></label>
				
				</p>
				
				<p>
				
					<label for="cw_verifi_purchase_code"><?php _e('Purchase Code', 'cw-verifi'); ?><span>&nbsp;(<a class="thickbox" href="<?php echo  trailingslashit( CWV_IMAGES ) . 'purchasecode.jpg'; ?>">whats this</a>)</span><br />
					
					<input name="cw_verifi_purchase_code" id="cw_verifi_purchase_code" class="required" type="text" placeholder="<?php _e('Please enter your purchase code', 'cw-verifi');?>"/></label>
				
				</p>
	
				<p>
				
					<input type="hidden" name="cw_verifi_nonce" value="<?php echo wp_create_nonce('cw-verifi-nonce'); ?>"/>
				
					<input type="submit" id="cw_verifi_nonce" value="<?php _e('Register an Account', 'cw-verifi'); ?>"/>
				
				</p>
					
		</form>
		
		<?php

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
	 * Register the new user
	 * 
	 * Most the code is pulled from wp-login.php
	 *
	 * @since 0.1
	 * @access public
	 * @return mixed
	 */
	function register_new_user() {

  		if (isset( $_POST["cw_verifi_user_name"] ) &&  wp_verify_nonce($_POST['cw_verifi_nonce'], 'cw-verifi-nonce')) {
			
			$sanitized_user_login = sanitize_user($_POST["cw_verifi_user_name"]);	
			
			$user_email		= $_POST["cw_verifi_user_email"];
			
			$purchase_code = $_POST["cw_verifi_purchase_code"];
			
		
			if(username_exists($sanitized_user_login)) {
				
				// Username already registered
				$this->errors()->add('username_unavailable', __('<strong>ERROR</strong>:Username already taken', 'cw-verifi'));
				
			}
		
			if(!validate_username($sanitized_user_login)) {
			
				// invalid username
				$this->errors()->add('username_invalid', __('<strong>ERROR</strong>:Invalid username', 'cw-verifi'));
				
			} elseif ($sanitized_user_login == '') {
			
				// empty username
				$this->errors()->add('username_empty', __('<strong>ERROR</strong>:Please enter a username', 'cw-verifi'));
			}
		
			if(!is_email($user_email)) {
		
				//invalid email
				$this->errors()->add('email_invalid', __('<strong>ERROR</strong>:Invalid email', 'cw-verifi'));
		
			} elseif (email_exists($user_email)) {
			
				//Email address already registered
				$this->errors()->add('email_used', __('<strong>ERROR</strong>:Email already registered', 'cw-verifi'));
			
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
			
			$this->success =apply_filters( 'cw_verifi_sucess_message', __('A Password will be sent to your email', 'cw-verifi') ); 

			$user_pass = wp_generate_password( 12, false);
			
			$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
			
			if ( ! $user_id ) {
				
				$this->errors()->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', 'cw-verifi' ), get_option( 'admin_email' ) ) );
			
				return $errors;
			
			}

			update_user_option( $user_id, 'default_password_nag', true, true ); 
	
			update_user_meta( $user_id, 'cw_purchase_code' , $purchase_code );

			wp_new_user_notification( $user_id, $user_pass );

			return $user_id;

		}
	
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
		   
		}elseif( $this->success != ''){
			
			echo '<span class="cw-success">' . $this->success . '</span>';
		}
	
	}

}

endif; //end if class exists

//VooDoo Magic 
$verifi_shortcode = new cw_Verfi_Shortcode;
//Poof!
?>

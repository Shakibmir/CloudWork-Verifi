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

if ( !class_exists( 'cw_Verfi_Shortcode' ) ) :

final class cw_Verfi_Shortcode {

	private static $add_script;

	private $success = '';
	
	public function __construct() {
		
		add_shortcode('cw-verifi-registration', array( $this, 'registration_form'));
		
		add_action('init', array( $this, 'register_new_user' ));
		
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
			
				$output = apply_filters( 'cw_verifi_closed_message', __('Sorry Registration is Closed', 'cw-verifi'));
					
			}
		
		} else {
			
			$output = apply_filters( 'cw_verifi_logged_message', __('Your are already logged in', 'cw-verifi') );
			
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
		
		wp_enqueue_script('cw-verifi-check',trailingslashit( CWV_JS) . 'cw-verifi-short-reg.js' );

		wp_enqueue_style( 'cw-verifi-css', trailingslashit( CWV_CSS ) . 'cw-verifi.css', null, '1.0' );
		
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
	function registration($args = array()){
				
		$fields = array(
			'username' =>'<p>' . '<label for="cw_verifi_user_name">'. __('Username', 'cw-verifi' ) .'<br />' .
						 '<input name="cw_verifi_user_name" id="cw_verifi_user_name" class="required" type="text" placeholder="' . __('Please enter a unique username', 'cw-verifi') . '" size="25" /></label></p>',
			
			
			
			'user_email' =>'<p>' . '<label for="cw_verifi_user_email">'. __('Email', 'cw-verifi') . '<br />' .
						   '<input name="cw_verifi_user_email" id="cw_verifi_user_email" class="required" type="text" placeholder="'. __('Please enter your email', 'cw-verifi').'" size="25" /></label></p>',
			
			
			'user_pass' =>'<p>' . '<label for="cw_verifi_user_pass">'. __('Password', 'cw-verifi') . '<br />' .
						   '<input name="cw_verifi_user_pass" id="cw_verifi_user_pass" class="required" type="password" placeholder="******" size="25" /></label></p>',		  
		    
		    'confirm_pass' =>'<p>' . '<label for="cw_verifi_confirm_pass">'. __('Confirm Password', 'cw-verifi') . '<br />' .
						   '<input name="cw_verifi_confirm_pass" id="cw_verifi_confirm_pass" class="required" type="password" placeholder="******" size="25" /></label></p>',
			
			'purchase_code' =>'<p>' . '<label for="cw_verifi_purchase_code">'. __('Purchase Code', 'cw-verifi') . '<span>&nbsp;(<a class="thickbox" href="' . trailingslashit( CWV_IMAGES ) . 'purchasecode.jpg' .'">whats this</a>)</span>' .
							  '<br /><input name="cw_verifi_purchase_code" id="cw_verifi_purchase_code" class="required" type="text" placeholder="'. __('Please enter your purchase code', 'cw-verifi') .'" size="25" /></label></p>'

		);
		
		$defaults = array(
			'fields' => apply_filters('cw_verifi_short_fields', $fields ),
			'form_id' =>'cw-verifi-form',
			'page_title' => __('Register New Account', 'cw-verifi'),
		
		);
		
	
		$args = wp_parse_args( $args, apply_filters('cw_verifi_form_defaults', $defaults ));
		
		?>	
		
		<h3 class="cw-verifi-header"><?php echo $args['page_title']; ?></h3>
		
		<?php $this->display_message(); ?>
		
		<form id="<?php echo $args['form_id']; ?>" class="verifi-form" action="" method="POST">
		
				<?php do_action('cw_verifi_before_form_fields'); ?>
			
				<?php
			
				foreach ( (array) $args['fields'] as $name => $field ) {
								echo apply_filters( "cw_registration_form_field_{$name}", $field ) . "\n";
							}
							
				?>
			
				<?php do_action('cw_verifi_after_form_fields'); ?>
				
				<p>Password must be at least 6 characters</p>
				
				<p>
				
					<input type="hidden" name="cw_verifi_nonce" value="<?php echo wp_create_nonce('cw-verifi-nonce'); ?>"/>
				
					<input type="submit" id="cw_verifi_nonce" value="<?php _e('Register an Account', 'cw-verifi'); ?>"/>
				
				</p>
					
		</form>
		
		<?php
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
	
		static $wp_error; // Will hold global variable safely
    
		isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
			
  		if (isset( $_POST['cw_verifi_user_name'] ) &&  wp_verify_nonce($_POST['cw_verifi_nonce'], 'cw-verifi-nonce')) {
			
			$sanitized_user_login = sanitize_user($_POST['cw_verifi_user_name']);	
			
			$user_email	= $_POST['cw_verifi_user_email'];
			
			$user_pass =$_POST['cw_verifi_user_pass'];
			
			$confirm_pass =$_POST['cw_verifi_confirm_pass'] ;
			
			$purchase_code = $_POST['cw_verifi_purchase_code'];
		
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
			
			if ( $user_pass == '' ) {
				
				$this->errors()->add( 'empty_pass', __( '<strong>ERROR</strong>: Please enter a password.' ) );
		
			} elseif ( $user_pass != $confirm_pass ) {
				
				$this->errors()->add( 'pass_match', __( '<strong>ERROR</strong>: Your passwords dont match!' ) );
			
			} elseif( strlen($user_pass) < 6 ){ 
		
				$this->errors()->add( 'short_pass', __( '<strong>ERROR</strong>: Your Password is too short.' ) );

			}	
			
			if (  $confirm_pass == '' ){
			
				$this->errors()->add( 'empty_confirm', __( '<strong>ERROR</strong>: Please confirm your password' ) );
			
			}
		
			if ( $purchase_code == '' ) {
				
				//empty purchase code
				$this->errors()->add( 'empty_purchase_code', __( '<strong>ERROR</strong>: Please enter your purchase code', 'cw-verifi' ) );
		
			} elseif ( !cw_validate_api( $purchase_code, true ) ) {

				//false purchase code
				$this->errors()->add( 'invalid_purchase_code', __( '<strong>ERROR</strong>: Please enter a valid purchase code', 'cw-verifi' ) );

			} elseif ( cw_purchase_exists( $purchase_code ) ) {

				//purchase code already exists
				$this->errors()->add( 'used_purchase_code', __( '<strong>ERROR</strong>: Sorry this purchase code exsits', 'cw-verifi' ) );

			}
			
			if ( $this->errors()->get_error_code() ){
	
				$errors = $this->errors()->get_error_messages();

			return $errors;
			
			} 
						
			$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
			
			if ( ! $user_id ) {
				
				$this->errors()->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', 'cw-verifi' ), get_option( 'admin_email' ) ) );
			
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
			$credentials['user_login'] = $sanitized_user_login;
			$credentials['user_password'] = $user_pass;
			$credentials['remember'] = true;
		
			wp_signon( $credentials );
			
			$redirect = apply_filters('cw_short_redirect', home_url());
			
			wp_safe_redirect($redirect); exit;

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
			
			echo '<div class="cw-verifi-errors">';
			
			// Loop error codes and display errors
			foreach($error_codes as $code){
					        	
		       	$message = $this->errors()->get_error_message($code);
		        
		       	echo '<span>' . $message . '</span><br/>';
		        
		   }
		       
		   echo '</div>';
		   
		}
	
	}

}

endif; //end if class exists

//VooDoo Magic 
$verifi_shortcode = new cw_Verfi_Shortcode;
//Poof!
?>

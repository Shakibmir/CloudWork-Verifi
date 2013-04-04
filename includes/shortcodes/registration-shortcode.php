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
	 * Registration Form Output, based of the default login form found wp-login.php
	 * 
	 * @since 0.1
	 * @access public
	 * @return string
	 * @todo filter
	 */
	function registration($args = array()){
	
		if (isset( $_POST['cw_verifi_user_name'] ) &&  wp_verify_nonce($_POST['cw_verifi_nonce'], 'cw-verifi-nonce')) {
			
			$http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
				
			$user_login = '';
			
			$user_email = '';
			
			$user_pass = '';
			
			$confirm_pass = '';
			
			$purchase_code = '';
			
			if ( $http_post ) {
				
				$user_login = $_POST['cw_verifi_user_name'];
				
				$user_email = $_POST['cw_verifi_user_email'];
				
				$user_pass = $_POST['cw_verifi_user_pass'];
				
				$confirm_pass = $_POST['cw_verifi_confirm_pass'];
				
				$purchase_code = $_POST['cw_verifi_purchase_code'];
				
				$errors = cw_verifi_register_user($user_login, $user_email, $user_pass, $confirm_pass, $purchase_code);
				
				if ( !is_wp_error($errors) ) {
				
					$options = get_option('cw_verifi_options');
					
					$redirect_url = $options['cw_redirect_url'];

					$redirect_to = apply_filters( 'cw_verifi_redirect', !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : $redirect_url );
				
					ob_start();

					if(!isset($_COOKIE['cw_verifi_new_user'])){
					
						setcookie('cw_verifi_new_user', 1,  time() + (60 * 60), COOKIEPATH, COOKIE_DOMAIN, false );
			
						
			
					}	
					
					ob_end_flush();	
										
					wp_safe_redirect( $redirect_to );
				
					exit();
				
				} elseif( $error_codes = $errors->get_error_codes() ) {
			
					echo '<div class="cw-verifi-errors">';
			
					// Loop error codes and display errors
					foreach($error_codes as $code){
					        	
						$message =  $errors->get_error_message($code);
		        
						echo '<span>' . $message . '</span><br/>';
						
					}
		       
					echo '</div>';
		   
				}
		
			}
		
		}
		
		$fields = array(
			'username' =>'<p>' . '<label for="cw_verifi_user_name">'. __('Username', 'cw-verifi' ) .'<br />' .
						 '<input name="cw_verifi_user_name" id="cw_verifi_user_name" class="required cw_username" type="text" placeholder="' . __('Please enter a unique username', 'cw-verifi') . '" size="25" /></label></p>',
			
			
			
			'user_email' =>'<p>' . '<label for="cw_verifi_user_email">'. __('Email', 'cw-verifi') . '<br />' .
						   '<input name="cw_verifi_user_email" id="cw_verifi_user_email" class="required" type="text" placeholder="'. __('Please enter your email', 'cw-verifi').'" size="25" /></label></p>',
			
			
			'user_pass' =>'<p>' . '<label for="cw_verifi_user_pass">'. __('Password', 'cw-verifi') . '<br />' .
						   '<input name="cw_verifi_user_pass" id="cw_verifi_user_pass" class="required cw_pass" type="password" placeholder="******" size="25" /></label></p>',		  
		    
		    'confirm_pass' =>'<p>' . '<label for="cw_verifi_confirm_pass">'. __('Confirm Password', 'cw-verifi') . '<br />' .
						   '<input name="cw_verifi_confirm_pass" id="cw_verifi_confirm_pass" class="required cw_confirm" type="password" placeholder="******" size="25" /></label></p>',
			
			'password_strength' => '<div id="pass-strength-result">'. __('Strength indicator', 'cw-verifi') .'</div>',
			
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
		
		
		<form id="<?php echo $args['form_id']; ?>" class="verifi-form" action="" method="POST">
		
				<?php do_action('cw_verifi_before_form_fields'); ?>
			
				<?php
			
				foreach ( (array) $args['fields'] as $name => $field ) {
							
								echo apply_filters( "cw_registration_form_field_{$name}", $field ) . "\n";
							
							}
							
				?>
			
				<?php do_action('cw_verifi_after_form_fields'); ?>
				
				<p><?php _e('Password must be at least 7 characters', 'cw-verifi'); ?></p>
				
				<p>
				
					<input type="hidden" name="cw_verifi_nonce" value="<?php echo wp_create_nonce('cw-verifi-nonce'); ?>"/>
				
					<input type="submit" id="cw_verifi_nonce" value="<?php _e('Register an Account', 'cw-verifi'); ?>"/>
				
				</p>
					
		</form>
		
		<?php
	}
	

}

endif; //end if class exists

//VooDoo Magic 
$verifi_shortcode = new cwv_Registration_Shortcode();
//Poof!
?>
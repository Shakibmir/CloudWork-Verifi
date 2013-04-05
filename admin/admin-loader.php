<?php
/**
 * @package CloudWork Verifi
 * @subpackage admin-loader.php
 * @version 0.4
 * @author Chris Kelley <chris@organicbeemedia.com)
 * @copyright Copyright � 2013 CloudWork Themes
 * @link http://cloudworkthemes.com
 * @since 0.4
 *
 * Table Of Contents
 *
 *
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'cw_VerifiAdmin_loader' ) ) :

final class cw_VerifiAdmin_loader{
	
	/**
	 * Just doing our thang!
	 * 
	 * @access public
	 * @return void
	 */
	function __construct(){
	
		add_action('admin_menu', array( &$this , 'admin_menu'));
		
		add_action('admin_init', array( &$this, 'register_scripts'));
		
		//add_action('admin_notices', array( &$this, 'admin_notice' ));
		
		$this->includes();

	
	}
	
	/**
	 * Creates the main menu for other adminmenus to hook into.
	 * 
	 * @access public
	 * @return void
	 */
	function admin_menu(){
		
		//We're adding a fake capability to prevent menu page theme utility from showing in the menu
		add_menu_page('CloudWork Verifi', 'CloudWork Verifi', 'i_dont_exist' , 'cloudwork-verifi', null , null, 100 );
		
	}
	
	/**
	 * We're Going to Register all of our admin scripts in one place to prevent duplicates.
	 * each will be enqueue'd in the proper place.
	 * 
	 * @access public
	 * @return void
	 */
	function register_scripts(){

		//Register the Ace edior
		wp_register_script( 'ace-core', trailingslashit( CWV_JS ) . 'ace/ace.js', array() , false, 1 );
		
		wp_register_script( 'ace-mode-css', trailingslashit( CWV_JS ) . 'ace/mode-css.js', array() , false, 1 );
		
		wp_register_script( 'ace-worker-css', trailingslashit( CWV_JS ) . 'ace/worker-css.js', array() , false, 1 );

		wp_register_script( 'ace-custom-css', trailingslashit( CWV_JS ) . 'editor.css.js', array() , false, 1 );


		if ( !wp_style_is( 'wp-color-picker','registered' ) ) {
		
			wp_register_style('wp-color-picker', trailingslashit( CWV_CSS ) . 'color-picker.min.css');
		
		}
		
		wp_register_script( 'cwtu-options', trailingslashit( CWV_JS ) .'cw.custom.options.js', array( 'jquery', 'wp-color-picker', 'iris' ) );
						
		wp_register_script( 'cwtu-uploader', trailingslashit( CWV_JS ) .'cw.uploader.js', array( 'jquery', 'thickbox' ) );
			
		if ( !wp_script_is( 'wp-color-picker', 'registered' ) ) {
			
			wp_register_script( 'iris', trailingslashit( CWV_JS ) . 'iris.min.js', array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );

			wp_register_script( 'wp-color-picker', trailingslashit( CWV_JS ) . 'color-picker.min.js', array( 'jquery', 'iris' ) );
			
				$colorpicker_l10n = array(
					'clear' => __( 'Clear', 'cwtu' ),
					'defaultString' => __( 'Default', 'cwtu' ),
					'pick' => __( 'Select Color', 'cwtu' )
				);
				
				wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
		
		}
		
		//Register Styles
		wp_register_style('cwtu-admin', trailingslashit( CWV_CSS ) . 'cwtu-admin.css', array(), 1.0 , false );
		
	
	}
	/**
	 * Include all of the Administration files
	 * 
	 * @access public
	 * @return void
	 */
	function includes(){
	
		//We Should already be in admin from the core loader but lets make sure
		if( is_admin()){
		
			require_once trailingslashit( CWV_ADMIN ) . 'settings.php';

			require_once trailingslashit( CWV_ADMIN ) . 'admin-functions.php';
		
			require_once trailingslashit( CWV_ADMIN ) . 'admin-sanitize.php';
			
			require_once trailingslashit( CWV_ADMIN ) . 'options-general.php';
			
		}
	
	}
		
	/**
	 * Creates Error notices if options arent set.
	 * 
	 * @access public
	 * @param mixed $message
	 * @return void
	 */
	function admin_notice(){
	
		//Wrap notices with link to options page
		$url = admin_url( 'options-general.php?page=cw-verifi-options' );
	
		//Dont display if user cant manage options
		if ( current_user_can( 'manage_options' ) ){
			
			if( $this->username == ''){
	
			echo '<div class="error"><a href="'. $url .'"><p>' . __('Please enter your Envato username', 'cw-verifi') . '</p></a></div>';
			
			}
			
			if( $this->apikey == ''){
	
			echo '<div class="error"><a href="'. $url .'"><p>' . __('Please enter your Envato API Key', 'cw-verifi') . '</p></a></div>';
			
			}
		
		}
		
	}
	
}

endif; //end if class exists

//High Five
$cwtu_admin_loader = new cw_VerifiAdmin_loader();
//Low Five
?>
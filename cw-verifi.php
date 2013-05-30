<?php
/*
Plugin Name: CloudWork Verifi
Plugin URI: http://cloudworkthemes.com
Description: Uses Envato API to verify purchase at registration, prevents duplicate purchase codes
Version: 0.4.4
Author: Chris Kelley <chris@organicbeemedia.com>
Author URI: http://cloudworkthemes.com
License: GPLv2
*
* Table of Contents
*
* register_activation_hook
* register_deactivation_hook
*
* Class cw_Verifi
*
*	instance
*	constants
*	includes
*	globals
*	load_textdomain
*	install
*	deactivate
*
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'cw_Verifi' ) ) :

final class cw_Verifi{
	
	public static $instance;
	
	public $envato; 		
	
	private $apikey;
	
	public $username;

	/**
	 * __construct function.
	 * 	
	 * @since 0.1
	 * @access private
	 * @return void
	 */
	private function __construct(){

		register_activation_hook( __FILE__, array( &$this, 'install') );

		register_deactivation_hook(__FILE__, array( &$this, 'deactivate') );
		
		add_action('init', array( &$this , 'register_scripts' ));
	}
	
	/**
	 * instance function.
	 * 
	 * @since 0.1
	 * @access public
	 * @static
	 * @return Only instance of Cw_Verifi
	 */
	public static function instance(){
		
		if ( ! isset( self::$instance ) ) {
		
			self::$instance = new cw_Verifi;
			self::$instance->constants();
			self::$instance->includes();
			self::$instance->globals();
			self::$instance->load_textdomain();
		
		}
		
		return self::$instance;
	
	}
		
	/**
	 * Setup Constants
	 * 
	 * @since 0.1
	 * @access private
	 * @return void
	 */
	private function constants(){
	
		if( !defined( 'CWV_VERSION' )){
		
			define( 'CWV_VERSION', '0.4' );
			
		}
		
		if( !defined( 'CWV_PLUGIN_URL' )){
		
			define( 'CWV_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	
		}
		if( !defined( 'CWV_PLUGIN_DIR' )){
		
			define( 'CWV_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			
		}
		if( !defined( 'CWV_MEDIA' )){
		
			define( 'CWV_MEDIA', trailingslashit( CWV_PLUGIN_URL ) . 'media' );
	
		}
		if( !defined( 'CWV_IMAGES' )){
		
			define( 'CWV_IMAGES', trailingslashit( CWV_MEDIA ) . 'images' );
	
		}
		
		if( !defined( 'CWV_CSS' )){
		
			define( 'CWV_CSS', trailingslashit( CWV_MEDIA ) . 'css' );
	
		}
		
		if( !defined( 'CWV_JS' )){
		
			define( 'CWV_JS', trailingslashit( CWV_MEDIA ) . 'javascript' );
			
		}
		
		if( !defined( 'CWV_LANG' )){
		
			define( 'CWV_LANG', trailingslashit( CWV_PLUGIN_URL ) . 'languages' );
	
		}
		
		if( !defined( 'CWV_INCLUDES' )){
		
			define( 'CWV_INCLUDES', trailingslashit( CWV_PLUGIN_DIR ) . 'includes' );
			
		}
		if( !defined( 'CWV_SHORTCODES' )){
		
			define( 'CWV_SHORTCODES', trailingslashit( CWV_INCLUDES ) . 'shortcodes' );
			
		}
		if( !defined( 'CWV_ADMIN' )){
		
			define( 'CWV_ADMIN', trailingslashit( CWV_PLUGIN_DIR ) . 'admin' );
			
		}
				
		
	}
	
	/**
	 * Includeds Envato Marketplace Class and Admin
	 * 
	 * @since 0.1
	 * @access private
	 * @return void
	 */
	private function includes(){
					require_once trailingslashit( CWV_ADMIN ) . 'admin-loader.php';

		if( is_admin()){
	
		
		}
		
		require_once trailingslashit( CWV_INCLUDES ) . 'class-cw-envato-api.php';
		
		require_once trailingslashit( CWV_INCLUDES ) . 'utility-functions.php';

		require_once trailingslashit( CWV_INCLUDES ) . 'registration-form.php';

		require_once trailingslashit( CWV_SHORTCODES ) . 'registration-shortcode.php';
		
		require_once trailingslashit( CWV_SHORTCODES ) . 'user-shortcodes.php';

	}
	
	/**
	 * globals function.
	 * 
	 * @since 0.1
	 * @access public
	 * @return void
	 */
	public function globals(){
			
		$this->username = cw_get_option('cw_verifi_options' , 'username');
		
		$this->apikey = cw_get_option('cw_verifi_options' , 'api_key');;
		
		$this->envato = new cw_WP_EnvatoAPI( $this->username , $this->apikey);
	
	}

	/**
	 * Load Textdomain.
	 * 
	 * @since 0.1
	 * @access public
	 * @return void
	 */
	public function load_textdomain(){
		
		load_plugin_textdomain('cw_verifi', false, CWV_LANG );

	}
	
	/**
	 * register_scripts function.
	 * 
	 * @since 0.4
	 * @access public
	 * @return void
	 */
	function register_scripts(){
		
		wp_register_script( 'cw-pass-strength', trailingslashit( CWV_JS ) . 'cw.jquery.password.js', array('jquery'), CWV_VERSION , true );
	
	}
	/**
	 * install function.
	 * 
	 * @since 0.4
	 * @access public
	 * @return void
	 */
	function install(){
	
		do_action('cwv_install');
		
	}
	
	/**
	 * deactivate function.
	 * 
	 * @since 0.4
	 * @access public
	 * @return voidå
	 */
	function deactivate(){
	
		do_action('cwv_deactivate');
		
	}

	

}//Ends Class

//Jedi Mind Tricks
$verifi = cw_Verifi::instance();
//May the force be with you

endif; //end if 
?>
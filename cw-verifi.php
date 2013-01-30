<?php
/*
Plugin Name: CloudWork Verifi
Plugin URI: http://cloudworkthemes.com
Description: Uses Envato API to verify purchase at registration, prevents duplicate purchase codes
Version: 0.3.1
Author: Chris Kelley <chris@organicbeemedia.com>
Author URI: http://cloudworkthemes.com
License: GPLv2
*
* Table of Contents
*
* Class cw_Verifi
* 	__contstruct
*	instance
*	constants
*	includes
*	globals
*	load_textdomain
*
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'cw_Verifi' ) ) :

final class cw_Verifi{
	
	public static $instance;
	
	private $options;
	
	private $apikey;
	
	public $username;
	
	public $envato; 		

	/**
	 * __construct function.
	 * 	
	 * @since 0.1
	 * @access private
	 * @return void
	 */
	private function __construct(){

		add_action('admin_notices', array( $this, 'admin_notice' ));

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
		
			define( 'CWV_VERSION', '1.0' );
			
		}
		
		if( !defined( 'CWV_PLUGIN_URL' )){
		
			define( 'CWV_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	
		}
	
		if( !defined( 'CWV_IMAGES' )){
		
			define( 'CWV_IMAGES', trailingslashit( CWV_PLUGIN_URL ) . 'images' );
	
		}
		
		if( !defined( 'CWV_CSS' )){
		
			define( 'CWV_CSS', trailingslashit( CWV_PLUGIN_URL ) . 'css' );
	
		}
		
		if( !defined( 'CWV_JS' )){
		
			define( 'CWV_JS', trailingslashit( CWV_PLUGIN_URL ) . 'javascript' );
			
		}
		
		if( !defined( 'CWV_LANG' )){
		
			define( 'CWV_LANG', trailingslashit( CWV_PLUGIN_URL ) . 'languages' );
	
		}
		
		if( !defined( 'CWV_PLUGIN_DIR' )){
		
			define( 'CWV_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			
		}
		
		if( !defined( 'CWV_INCLUDES' )){
		
			define( 'CWV_INCLUDES', trailingslashit( CWV_PLUGIN_DIR ) . 'includes' );
			
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
		
		//Loads Envato Marketplace Class by @Jeffery Way
		require_once trailingslashit( CWV_INCLUDES ) . 'envato-marketplaces.php';
		
		require_once trailingslashit( CWV_INCLUDES ) . 'cw-verifi-functions.php';

		require_once trailingslashit( CWV_INCLUDES ) . 'cw-verifi-login.php';

		require_once trailingslashit( CWV_INCLUDES ) . 'cw-verifi-shortcode.php';
		
		require_once trailingslashit( CWV_INCLUDES ) . 'cw-verifi-usershort.php';


		if( is_admin()){
	
			require_once trailingslashit( CWV_INCLUDES ) . 'cw-verifi-admin.php';
		
		}

	}
	
	/**
	 * globals function.
	 * 
	 * @since 0.1
	 * @access public
	 * @return void
	 */
	public function globals(){
	
		$this->options = get_option('cw_verifi_options');
		
		$this->username = $this->options['username'];

		$this->apikey = $this->options['api_key'];
		
		$this->envato = new Envato_marketplaces();
		
		$this->envato->set_api_key( $this->apikey );
	
	}

	/**
	 * Load Textdomain.
	 * 
	 * @since 0.1
	 * @access public
	 * @return void
	 */
	public function load_textdomain(){
		
		load_plugin_textdomain('cw-verifi', false, CWV_LANG );

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
	

}//Ends Class

endif; //end if 

//Jedi Mind Tricks
$verifi = cw_Verifi::instance();
//May the force be with you
?>
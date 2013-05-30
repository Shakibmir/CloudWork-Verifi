<?php
/**
 * @package CloudWork Verifi
 * @subpackage options-general.php
 * @version 0.4
 * @author Chris Kelley <chris@organicbeemedia.com)
 * @copyright Copyright � 2013 CloudWork Themes
 * @link http://cloudworkthemes.com
 * @since 0.4
 *
 * Table Of Contents
 * cwv_GeneralSettings
 *	__construct
 *	set_options
 *	init
 *	admin_menu
 *	enqueue_scripts
 *	page
 *  validate
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'cwv_GeneralSettings' ) ) :

final class cwv_GeneralSettings{
	
	private $options;
	
	private $page;
	/**
	 * __construct function.
	 * 
	 * @since 0.4
	 * @access public
	 * @return void
	 */
	function __construct(){
			
		$this->set_options();

		add_action('admin_menu', array( &$this, 'admin_menu' ));	
		
		add_action( 'admin_enqueue_scripts', array( &$this , 'enqueue_scripts') );
			
		add_action('admin_init', array( &$this, 'init') );
			
		//add_action('cwv_install', array($this, 'install'));
	
	}
	
	/**
	 * Set our Options $var with a filter
	 * 
	 * @access public
	 * @return void
	 */
	function set_options(){
		
		$this->options = apply_filters ( 'cwv_general_settings' , $this->options );
	
	}
	
	/**
	* Framework/Theme Install.
	* 
	* @since 0.4
	* @access public
	* @return void
	*/
	function init() {
		
		$cwv_settings = get_option('cw_verifi_options' );
	
		// Gets the unique id, returning a default if it isn't defined
		if ( isset($cwv_settings) ) {
	
			$option_name = 'cw_verifi_options';
	
		} else {
	
			$option_name = 'cw_verifi_options';
	
		}
	
		// If the option has no saved data, load the defaults
		if ( ! get_option($option_name) ) {
	
			cw_setdefaults($this->options, $option_name);
			
		}

		// Registers the settings fields and callback
		register_setting( 'cw_verifi_options', $option_name, array( &$this , 'validate') );
	
	}
	
	/**
	 * Add the submenu page hooks on theme-utility which is registered in the cwtu-admin-loader.php .
	 * 
	 * @access public
	 * @return void
	 */
	function admin_menu(){
		
		$this->page = add_options_page( __('CloudWork Verifi', 'cw_verifi') , __('CloudWork Verifi', 'cw_verifi') , 'manage_options', 'cwv-general', array( &$this, 'page' ));	
		
	}
	
	/**
	 * admin_scripts function.
	 * 
	 * @access public
	 * @param mixed $hook
	 * @return void
	 */
	function enqueue_scripts($hook){
		
		if($hook != $this->page )
		
			return;
			 
			wp_enqueue_media();
			
			wp_enqueue_style( 'wp-color-picker' );

			wp_enqueue_script( 'cwv-options' );	
					 
			wp_enqueue_script( 'cw-uploader' );
			
			wp_enqueue_style('cw-admin');
		
	}
	
	function page() { ?>

			<div id="cwv_options-wrap" class="wrap">
        
				<h2 class="nav-tab-wrapper"><?php echo cw_tabs($this->options); ?></h2>
	
				<form action="options.php" method="post">
	
					<?php settings_fields('cw_verifi_options'); ?>
	
					<?php cw_fields($this->options, 'cw_verifi_options');/* Settings */ ?>
	
					<div id="cwv_options-submit">
	
						<input type="submit" class="button-primary" name="update" value="<?php esc_attr_e( 'Save Options', 'cw_verifi' ); ?>" />
	
						<div class="clear"></div>
	
					</div>
	
				</form>
			
			</div> <!-- / .wrap -->
	
	<?php }

	/**
	 * validate function.
	 * 
	 * @access public
	 * @param mixed $input
	 * @return void
	 */
	function validate( $input ) {
	
		$clean = array();
	
		$test_options = $this->options;
	
		foreach ( $test_options as $option ) {

			if ( ! isset( $option['id'] ) ) {
	
				continue;
	
			}

			if ( ! isset( $option['type'] ) ) {
	
				continue;
	
			}

			$id = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower( $option['id'] ) );

			// Set checkbox to false if it wasn't sent in the $_POST
			if ( 'checkbox' == $option['type'] && ! isset( $input[$id] ) ) {
		
				$input[$id] = false;
		
			}

			// Set each item in the multicheck to false if it wasn't sent in the $_POST
			if ( 'multicheck' == $option['type'] && ! isset( $input[$id] ) ) {
		
				foreach ( $option['options'] as $key => $value ) {
		
					$input[$id][$key] = false;
		
				}
		
			}

			// For a value to be submitted to database it must pass through a sanitization filter
			if ( has_filter( 'cw_sanitize_' . $option['type'] ) ) {
		
				$clean[$id] = apply_filters( 'cw_sanitize_' . $option['type'], $input[$id], $option );
				
			}
	
		}
	
		// Hook to run after validation
		do_action( 'cw_after_validate', $clean );
	
		return $clean;
	
	}
	
}

//High Five
$cwv_generalsettings = new cwv_GeneralSettings;
//Low Five

endif; //end if class exists
?>
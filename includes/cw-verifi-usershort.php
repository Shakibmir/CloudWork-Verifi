<?php

/**
 * @package CloudWork Verifi
 * @subpackage cw-verifi-shortcode.php
 * @version 0.3
 * @author Chris Kelley <chris@organicbeemedia.com)
 * @copyright Copyright © 2013 CloudWork Themes
 * @link http://cloudworkthemes.com
 * @since 0.3
 *
 * Table Of Contents
 *
 * cw_Verfi_UserShort
 *	__construct
 *	add_shortcodes
 *	new_user_message
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'cw_Verfi_UserShortcodes' ) ) :

class cw_Verfi_UserShortcodes {
		
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
				
		add_action('init', array( $this , 'add_shortcodes' ) );
	
	}
	function add_shortcodes(){
		
		add_shortcode('cw-new-user', array($this, 'new_user_message'));
	}

	
	function new_user_message($atts, $content = null){
		
		if (isset($_COOKIE['cw_verifi_new_user'])) { 

			return '<div class="cw-user-message">'.$content.'</div>';  

		} 
		
	}

}

endif; //end if class exists

//Lego
$verifi_usershort = new cw_Verfi_UserShortcodes;
//My Lego
?>

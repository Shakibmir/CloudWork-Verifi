<?php
if ( ! function_exists( 'cw_setdefaults' ) ){
/**
 * cw_setdefaults function.
 * 
 * @access public
 * @param mixed $options
 * @param mixed $section
 * @return void
 */
function cw_setdefaults( $options, $section ) {
	
	// Gets the unique option id
	$option_name = $section;
	
	// Gets the default options data from the array in options.php
	$options = $options;
	
	// If the options haven't been added to the database yet, they are added now
	$values = cw_get_default_values($options);
	
	if ( isset($values) ) {
		
		add_option( $option_name, $values ); // Add option with default settings
	
	}

}

}//End function_exists

if ( ! function_exists( 'cw_get_default_values' ) ){

/**
 * cw_get_default_values function.
 * 
 * @access public
 * @param mixed $options
 * @return void
 */
function cw_get_default_values($options) {
	
	$output = array();
	
	$config = $options;
	
	foreach ( (array) $config as $option ) {
		
		if ( ! isset( $option['id'] ) ) {
		
			continue;
		
		}
		
		if ( ! isset( $option['std'] ) ) {
		
			continue;
		
		}
		
		if ( ! isset( $option['type'] ) ) {
		
			continue;
		
		}
		
		if ( has_filter( 'cw_sanitize_' . $option['type'] ) ) {
		
			$output[$option['id']] = apply_filters( 'cw_sanitize_' . $option['type'], $option['std'], $option );
		
		}
	
	}
	
	return $output;
}

}//End function_exists

if ( ! function_exists( 'cw_tabs' ) ){

/**
 * Generates the tabs that are used in the options menu
 *
 * cw_tabs function.
 * 
 * @since 0.1
 * @access public
 * @return string
 */

function cw_tabs( $options ) {

	$counter = 0;
	
	$menu = '';

	foreach ($options as $value) {
		
		$counter++;
		
		// Heading for Navigation
		if ($value['type'] == "heading") {
			
			$id = ! empty( $value['id'] ) ? $value['id'] : $value['name'];
			
			$jquery_click_hook = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($id) );
			
			$jquery_click_hook = "cw-option-" . $jquery_click_hook . $counter;
			
			$menu .= '<a id="'.  esc_attr( $jquery_click_hook ) . '-tab" class="nav-tab" title="' . esc_attr( $value['name'] ) . '" href="' . esc_attr( '#'.  $jquery_click_hook ) . '">' . esc_html( $value['name'] ) . '</a>';
		
		}
	}
	
	return $menu;


}

}//End function_exists

if ( ! function_exists( 'cw_fields' ) ){

/**
 * Generates the options fields that are used in the form.
 *
 * @since 0.1 
 * @access public
 * @return string
 */
function cw_fields($options, $settings) {

	global $allowedtags;
	
	$option_name = $settings;

	$settings = get_option($option_name);
	
	$counter = 0;
	
	$menu = '';

	foreach ( $options as $value ) {

		$counter++;
		
		$val = '';
		
		$select_value = '';
		
		$checked = '';
		
		$output = '';

		// Wrap all options
		if ( ( $value['type'] != "heading" ) && ( $value['type'] != "info" )) {

			// Keep all ids lowercase with no spaces
			$value['id'] = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($value['id']) );

			$id = 'section-' . $value['id'];

			$class = 'section ';
			
			if ( isset( $value['type'] ) ) {
			
				$class .= ' section-' . $value['type'];
			
			}
			
			if ( isset( $value['class'] ) ) {
			
				$class .= ' ' . $value['class'];
			
			}

			$output .= '<div id="' . esc_attr( $id ) .'" class="' . esc_attr( $class ) . '">'."\n";
			
			if ( isset( $value['name'] ) ) {
			
				$output .= '<h4 class="heading">' . esc_html( $value['name'] ) . '</h4>' . "\n";
			
			}
			
			if ( ($value['type'] != 'editor') && ( $value['type'] != 'code' )) {
			
				$output .= '<div class="option">' . "\n" . '<div class="controls">' . "\n";
			
			} else {
			
				$output .= '<div class="option">' . "\n" . '<div>' . "\n";
			
			}
			
		}

		// Set default value to $val
		if ( isset( $value['std'] ) ) {
		
			$val = $value['std'];
		
		}

		// If the option is already saved, ovveride $val
		if ( ( $value['type'] != 'heading' ) && ( $value['type'] != 'info') ) {
			
			if ( isset( $settings[($value['id'])]) ) {
			
				$val = $settings[($value['id'])];
			
				// Striping slashes of non-array options
			
				if ( !is_array($val) ) {
			
					$val = stripslashes( $val );
			
				}
			
			}
		
		}

		// If there is a description save it for labels
		$explain_value = '';
		
		if ( isset( $value['desc'] ) ) {
		
			$explain_value = $value['desc'];
		
		}

		switch ( $value['type'] ) {

		// Basic text input
		case 'text':
		
			$output .= '<input id="' . esc_attr( $value['id'] ) . '" class="cw-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="text" value="' . esc_attr( $val ) . '" />';
		
			break;

		// Password input
		case 'password':
		
			$output .= '<input id="' . esc_attr( $value['id'] ) . '" class="cw-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="password" value="' . esc_attr( $val ) . '" />';
		
			break;

		// Textarea
		case 'textarea':
		
			$rows = '8';

			if ( isset( $value['settings']['rows'] ) ) {
		
				$custom_rows = $value['settings']['rows'];
		
				if ( is_numeric( $custom_rows ) ) {
		
					$rows = $custom_rows;
		
				}
		
			}

			$val = stripslashes( $val );
		
			$output .= '<textarea id="' . esc_attr( $value['id'] ) . '" class="cw-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" rows="' . $rows . '">' . esc_textarea( $val ) . '</textarea>';
		
			break;

		// Select Box
		case 'select':
		
			$output .= '<select class="cw-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . esc_attr( $value['id'] ) . '">';

			foreach ($value['options'] as $key => $option ) {
		
				$selected = '';
		
				if ( $val != '' ) {
		
					if ( $val == $key) { $selected = ' selected="selected"';}
		
				}
		
				$output .= '<option'. $selected .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
		
			}
		
			$output .= '</select>';
		
			break;

		// Radio Box
		case "radio":
		
			$name = $option_name .'['. $value['id'] .']';
		
			foreach ($value['options'] as $key => $option) {
		
				$id = $option_name . '-' . $value['id'] .'-'. $key;
		
				$output .= '<input class="cw-input cw-radio" type="radio" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="'. esc_attr( $key ) . '" '. checked( $val, $key, false) .' /><label for="' . esc_attr( $id ) . '">' . esc_html( $option ) . '</label>';
		
			}
		
			break;

		// Image Selectors
		case "images":
		
			$name = $option_name .'['. $value['id'] .']';
		
			foreach ( $value['options'] as $key => $option ) {
		
				$selected = '';
		
				$checked = '';
		
				if ( $val != '' ) {
		
					if ( $val == $key ) {
		
						$selected = ' cw-radio-img-selected';
		
						$checked = ' checked="checked"';
		
					}
		
				}
		
				$output .= '<input type="radio" id="' . esc_attr( $value['id'] .'_'. $key) . '" class="cw-radio-img-radio" value="' . esc_attr( $key ) . '" name="' . esc_attr( $name ) . '" '. $checked .' />';
		
				$output .= '<div class="cw-radio-img-label">' . esc_html( $key ) . '</div>';
		
				$output .= '<img src="' . esc_url( $option ) . '" alt="' . $option .'" class="cw-radio-img-img' . $selected .'" onclick="document.getElementById(\''. esc_attr($value['id'] .'_'. $key) .'\').checked=true;" />';
		
			}
		
			break;

		// Checkbox
		case "checkbox":
		
			$output .= '<input id="' . esc_attr( $value['id'] ) . '" class="checkbox cw-input" type="checkbox" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" '. checked( $val, 1, false) .' />';
		
			$output .= '<label class="explain" for="' . esc_attr( $value['id'] ) . '">' . wp_kses( $explain_value, $allowedtags) . '</label>';
		
			break;

		// Multicheck
		case "multicheck":

			foreach ($value['options'] as $key => $option) {

				$checked = '';

				$label = $option;

				$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($key));
				
				$id = $option_name . '-' . $value['id'] . '-'. $option;
				
				$name = $option_name . '[' . $value['id'] . '][' . $option .']';

				if ( isset($val[$option]) ) {
				
					$checked = checked($val[$option], 1, false);
				
				}

				$output .= '<input id="' . esc_attr( $id ) . '" class="checkbox cw-input" type="checkbox" name="' . esc_attr( $name ) . '" ' . $checked . ' /><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
			
			}
			
			break;

		// Color picker
		case "color":
			
			$default_color = '';
			
			if ( isset($value['std']) ) {
			
				if ( $val !=  $value['std'] )
			
					$default_color = ' data-default-color="' .$value['std'] . '" ';
			
			}
			
			$output .= '<input name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . esc_attr( $value['id'] ) . '" class="cw-color"  type="text" value="' . esc_attr( $val ) . '"' . $default_color .' />';
 	
			break;
		// Uploader
		case "upload":
			
			$output .= cw_medialibrary_uploader( $value['id'], $val, null, null, $option_name );
			
			break;	
			
		// Editor
		case 'editor':
			
			$output .= '<div class="explain">' . wp_kses( $explain_value, $allowedtags) . '</div>'."\n";
			
			echo $output;
			
			$textarea_name = esc_attr( $option_name . '[' . $value['id'] . ']' );
			
			$default_editor_settings = array(
				'textarea_name' => $textarea_name,
				'media_buttons' => false,
				'tinymce' => array( 'plugins' => 'wordpress' )
			);
			
			$editor_settings = array();
			
			if ( isset( $value['settings'] ) ) {
			
				$editor_settings = $value['settings'];
			
			}
			
			$editor_settings = array_merge($editor_settings, $default_editor_settings);
			
			wp_editor( $val, $value['id'], $editor_settings );
			
			$output = '';
			
			break;

		//This wont work by default it requires the proper JS to be added and currently only 1 per options
		case "code":
		
			$output .='<p>'. $explain_value .'</p>';
		
			$output .='<div id="code-container"><div name="custom-code" id="custom-code"></div></div>';
							
			$val = stripslashes( $val );
		
			$output .= '<textarea id="' . esc_attr( $value['id'] ) . '" class="cw-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '">' . esc_textarea( $val ) . '</textarea>';		
		break;
		
		
		// Info
		case "info":
			
			$id = '';
			
			$class = 'section';
			
			if ( isset( $value['id'] ) ) {
			
				$id = 'id="' . esc_attr( $value['id'] ) . '" ';
			
			}
			
			if ( isset( $value['type'] ) ) {
			
				$class .= ' section-' . $value['type'];
			
			}
			
			if ( isset( $value['class'] ) ) {
			
				$class .= ' ' . $value['class'];
			
			}

			$output .= '<div ' . $id . 'class="' . esc_attr( $class ) . '">' . "\n";
			
			if ( isset($value['name']) ) {
			
				$output .= '<h4 class="heading">' . esc_html( $value['name'] ) . '</h4>' . "\n";
			
			}
			
			if ( $value['desc'] ) {
			
				$output .= apply_filters('cw_sanitize_info', $value['desc'] ) . "\n";
			
			}
			
			$output .= '</div>' . "\n";
			
			break;

		// Heading for Navigation
		case "heading":
			
			if ($counter >= 2) {
			
				$output .= '</div>'."\n";
			
			}
			
			$jquery_click_hook = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($value['name']) );
			
			$jquery_click_hook = "cw-option-" . $jquery_click_hook . $counter;
			
			$menu .= '<a id="'.  esc_attr( $jquery_click_hook ) . '-tab" class="nav-tab" title="' . esc_attr( $value['name'] ) . '" href="' . esc_attr( '#'.  $jquery_click_hook ) . '">' . esc_html( $value['name'] ) . '</a>';
			
			$output .= '<div class="group" id="' . esc_attr( $jquery_click_hook ) . '">';
						
			break;

		}

		if ( ( $value['type'] != "heading" ) && ( $value['type'] != "info" )  ) {
			
			$output .= '</div>';
			
			if ( ( $value['type'] != "checkbox" ) && ( $value['type'] != "editor" )&& ( $value['type'] != 'code') ) {
			
				$output .= '<div class="explain">' . wp_kses( $explain_value, $allowedtags) . '</div>'."\n";
			
			}
			
			$output .= '</div></div>'."\n";
		}

		echo $output;
	
	}
	
	echo '</div>';

}

}//End function_exists

if ( ! function_exists( 'cw_medialibrary_uploader' ) ){

/**
 * Media Uploader Using the WordPress Media Library.
 * 
 * @access public
 * @param mixed $_id
 * @param mixed $_value
 * @param string $_mode (default: 'full')
 * @param string $_desc (default: '')
 * @param int $_postid (default: 0)
 * @param string $_name (default: '')
 * @return string
 *
 */
function cw_medialibrary_uploader( $_id, $_value, $_desc = '', $_name = '', $section) {
	
	// Gets the unique option id
	$option_name = $section;

	$output = '';
	
	$id = '';
	
	$class = '';
	
	$int = '';
	
	$value = '';
	
	$name = '';

	
	$id = strip_tags( strtolower( $_id ) );
	
	// If a value is passed and we don't have a stored value, use the value that's passed through.
	if ( $_value != '' && $value == '' ) {
		$value = $_value;
	}
	
	if ( $_name != '' ) {
		$name = $_name;
	}
	else {
		$name = $option_name.'['.$id.']';
	}

	if ( $value ) { $class = ' has-file'; }
	
	$output .= '<div class="uploader"><input id="' . $id . '" class="custom_media_url' . $class . '" type="text" name="'.$name.'" value="' . $value . '" />' . "\n";
	
	$output .= '<input id="' . $id . '_button" class="button" type="button" value="' . __( 'Upload', 'cloudwork' ) . '" rel="' . $int . '" /></div>' . "\n";
	
	if ( $_desc != '' ) {
	
		$output .= '<span class="cw_metabox_desc">' . $_desc . '</span>' . "\n";
	
	}
	
	$output .= '<div class="screenshot" id="' . $id . '_image">' . "\n";
	
	if ( $value != '' ) { 
	
		$remove = '<a href="javascript:(void);" class="cw-remove button">Remove</a>';
	
		$image = preg_match( '/(^.*\.jpg|jpeg|png|gif|ico*)/i', $value );
	
		if ( $image ) {
	
			$output .= '<img src="' . $value . '" alt="" />'.$remove.'';
	
		} else {
	
			$parts = explode( "/", $value );
	
			for( $i = 0; $i < sizeof( $parts ); ++$i ) {
	
				$title = $parts[$i];
	
			}

			// No output preview if it's not an image.			
			$output .= '';
		
			// Standard generic output if it's not an image.	
			$title = __( 'View File', 'cloudwork' );
	
			$output .= '<div class="no_image"><span class="file_link"><a href="' . $value . '" target="_blank" rel="external">'.$title.'</a></span>' . $remove . '</div>';
	
		}	

	}

	$output .= '</div>' . "\n";
	
	return $output;

}

}//End function_exists

if ( ! function_exists( 'cw_get_option' ) ){

function cw_get_option( $set , $option, $default = false ){

	$config = get_option( $set );
	
	if ( ! isset($config)){
		
		return $default;
		
	}

	$options = $config[$option];
	
	if ( isset( $options ) ) {
		
		return $options;
	}
	
	return $default;
	
}

}//End function_exists

?>
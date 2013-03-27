<?php
/**
 * @package CloudWork
 * @subpackage cw-sanitize.php
 * @version 1.0
 * @author Chris Kelley <chris@organicbeemedia.com)
 * @copyright Copyright � 2013 Organic Bee Media
 * @link http://organicbeemedia.com
 * @since 0.1
 * @todo
 *
 *
 * Table Of Contents
 *
 * cwv_sanitize_text
 * cwv_sanitize_checkbox
 * cwv_sanitize_multicheck
 * cwv_sanitize_upload
 * cwv_sanitize_editor
 * cwv_sanitize_allowedtags
 * cwv_sanitize_hex
 * cwv_validate_hex
 *
 */
/*

/* Text */

add_filter( 'cwv_sanitize_text', 'sanitize_text_field' );

/* Textarea */
add_filter( 'cwv_sanitize_textarea', 'cwv_sanitize_textarea' );

/**
 * Sanitize Textarea
 * 
 * @since 0.1
 * @access public
 * @param mixed $input
 * @return string
 */
function cwv_sanitize_textarea($input) {
	
	global $allowedposttags;
	
	$output = wp_kses( $input, $allowedposttags);
	
	return $output;
}
/* Textarea */
add_filter( 'cwv_sanitize_code', 'cwv_sanitize_code' );

/**
 * Sanitize Textarea
 * 
 * @since 0.1
 * @access public
 * @param mixed $input
 * @return string
 */
function cwv_sanitize_code($input) {

	global $allowedposttags;
	
	$output = wp_kses( $input, $allowedposttags);
	
	return $output;

}


/* Checkbox */
add_filter( 'cwv_sanitize_checkbox', 'cwv_sanitize_checkbox' );

/**
 * Sanitize Checkbox
 * 
 * @since 0.1
 * @access public
 * @param mixed $input
 * @return int|bool
 */
function cwv_sanitize_checkbox( $input ) {
	
	if ( $input ) {
		
		$output = '1';
	
	} else {
		
		$output = false;
	
	}
	
	return $output;
}

//
add_filter( 'cwv_sanitize_multicheck', 'cwv_sanitize_multicheck', 10, 2 );

/**
 * Sanitize Multicheck 
 * 
 * @since 0.1
 * @access public
 * @param mixed $input
 * @param mixed $option
 * @return int
 */
function cwv_sanitize_multicheck( $input, $option ) {
	
	$output = '';
	
	if ( is_array( $input ) ) {
		
		foreach( $option['options'] as $key => $value ) {
			
			$output[$key] = "0";
		
		}
		
		foreach( $input as $key => $value ) {
			
			if ( array_key_exists( $key, $option['options'] ) && $value ) {
			
				$output[$key] = "1";
			
			}
		
		}
	
	}
	
	return $output;
}




/* Uploader */
add_filter( 'cwv_sanitize_upload', 'cwv_sanitize_upload' );

/**
 * Sanitize Upload
 * 
 * @since 0.1
 * @access public
 * @param mixed $input
 * @return string
 */
function cwv_sanitize_upload( $input ) {
	
	$output = '';
	
	$filetype = wp_check_filetype($input);
	
	if ( $filetype["ext"] ) {
	
		$output = $input;
	
	}
	
	return $output;

}


/* Editor */
add_filter( 'cwv_sanitize_editor', 'cwv_sanitize_editor' );


/**
 * Sanitize Editor 
 * 
 * @since 0.1
 * @access public
 * @param mixed $input
 * @return string
 */
function cwv_sanitize_editor($input) {
	
	if ( current_user_can( 'unfiltered_html' ) ) {
	
		$output = $input;
	
	} else {
	
		global $allowedtags;
	
		$output = wpautop(wp_kses( $input, $allowedtags));
	
	}
	
	return $output;

}

/**
 * Allowed Tags.
 * 
 * @since 0.1
 * @access public
 * @param mixed $input
 * @return string
 */
function cwv_sanitize_allowedtags($input) {
	
	global $allowedtags;
	
	$output = wpautop(wp_kses( $input, $allowedtags));
	
	return $output;

}

//
add_filter( 'cwv_sanitize_info', 'cwv_sanitize_allowedposttags' );

/**
 * Allowed Post Tags.
 * 
 * @since 0.1
 * @access public
 * @param mixed $input
 * @return string
 */
function cwv_sanitize_allowedposttags($input) {
	
	global $allowedposttags;
	
	$output = wpautop(wp_kses( $input, $allowedposttags));
	
	return $output;

}

//
add_filter( 'cwv_sanitize_select', 'cwv_sanitize_enum', 10, 2);
add_filter( 'cwv_sanitize_radio', 'cwv_sanitize_enum', 10, 2);
add_filter( 'cwv_sanitize_images', 'cwv_sanitize_enum', 10, 2);

/**
 * Check that the key value sent is valid
 * 
 * @since 0.1
 * @access public
 * @param mixed $input
 * @param mixed $option
 * @return mixed
 */
function cwv_sanitize_enum( $input, $option ) {
	
	$output = '';
	
	if ( array_key_exists( $input, $option['options'] ) ) {
	
		$output = $input;
	
	}
	
	return $output;
}

//
add_filter( 'cwv_sanitize_color', 'cwv_sanitize_hex' );

/**
 * Sanitize a color represented in hexidecimal notation.
 * 
 * @access public
 * @param string $hex Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @param string $default (default: '')  The value that this function should return if it cannot be recognized as a color.
 * @return string
 */
function cwv_sanitize_hex( $hex, $default = '' ) {
	
	if ( cwv_validate_hex( $hex ) ) {
	
		return $hex;
	
	}
	
	return $default;

}
 
/**
 * Is a given string a color formatted in hexidecimal notation?
 * 
 * @since 0.1
 * @access public
 * @param mixed $hex
 * @return bool
 */
function cwv_validate_hex( $hex ) {
	
	$hex = trim( $hex );
	
	/* Strip recognized prefixes. */
	if ( 0 === strpos( $hex, '#' ) ) {
	
		$hex = substr( $hex, 1 );
	
	}
	
	elseif ( 0 === strpos( $hex, '%23' ) ) {
	
		$hex = substr( $hex, 3 );
	
	}
	
	/* Regex match. */
	if ( 0 === preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
	
		return false;
	
	} else {
	
		return true;
	
	}
}

?>
( function( global, $ ) {
	var editor,
		syncJS = function() {
			$( '#cwtu_custom_js' ).val( editor.getSession().getValue() );
		},
		loadAce = function() {
			editor = ace.edit( 'custom-code' );
			global.safecss_editor = editor;
			editor.getSession().setUseWrapMode( true );
			editor.setShowPrintMargin( false );
			editor.getSession().setValue( $( '#cwtu_custom_js' ).val() );
			editor.getSession().setMode("ace/mode/javascript");
			jQuery.fn.spin&&$( '.code-container' ).spin( false );
			$( '#custom-code-form' ).submit( syncJS );
		};

		$( global ).load( loadAce );
	
	global.acesyncJS = syncJS;
} )( this, jQuery );
( function( global, $ ) {
	var editor,
		syncCSS = function() {
			$( '#cwtu_custom_css' ).val( editor.getSession().getValue() );
		},
		loadAce = function() {
			editor = ace.edit( 'custom-code' );
			global.safecss_editor = editor;
			editor.getSession().setUseWrapMode( true );
			editor.setShowPrintMargin( false );
			editor.getSession().setValue( $( '#cwtu_custom_css' ).val() );
			editor.getSession().setMode("ace/mode/css");
			jQuery.fn.spin&&$( '#code-container' ).spin( false );
			$( '#custom-code-form' ).submit( syncCSS );
		};

		$( global ).load( loadAce );
	
	global.aceSyncCSS = syncCSS;

} )( this, jQuery );
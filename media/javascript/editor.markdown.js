( function( global, $ ) {
	var editor,
		syncCSS = function() {
			$( '#system-info-textarea' ).val( editor.getSession().getValue() );
		},
		loadAce = function() {
			editor = ace.edit( 'custom_css' );
			global.safecss_editor = editor;
			editor.getSession().setUseWrapMode( true );
			editor.setShowPrintMargin( false );
			editor.getSession().setValue( $( '#system-info-textarea' ).val() );
			editor.getSession().setMode("ace/mode/markdown");
			jQuery.fn.spin&&$( '#custom_css_container' ).spin( false );
			$( '#custom_css_form' ).submit( syncCSS );
		};

		$( global ).load( loadAce );
	
	global.aceSyncCSS = syncCSS;
} )( this, jQuery );

jQuery(document).ready(function(jQuery){
			

	var cw_frame;

	jQuery('.cw-remove').live('click', function(e) { 

        jQuery(this).hide();
        
        jQuery(this).parents().parents().children('.upload').attr('value', '');
        
        jQuery(this).parents('.screenshot').slideUp();
        
        return false;
        
      });
     
      jQuery('.uploader .button').click( function( e ){
  
      	e.preventDefault();

      	var button = jQuery(this);
  
      	var id = button.attr('id').replace('_button', '');

      	if ( cw_frame ) {
      	
      		cw_frame.open();
      
      		return;
    
      	}

      	cw_frame = wp.media({

					  title: jQuery( this ).data( 'uploader_title' ),
					  button: {
						text: jQuery( this ).data( 'uploader_button_text' ),
					  },
					  multiple: false  // Set to true to allow multiple files to be selected
    
	   });

	   cw_frame.on( 'select', function() {
      
      attachment = cw_frame.state().get('selection').first().toJSON();

      	jQuery("#"+id).val(attachment.url);
        
     });

    cw_frame.open();
    
  });

});
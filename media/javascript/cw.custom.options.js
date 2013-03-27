/**
 * Prints out the inline javascript needed for the colorpicker and choosing
 * the tabs in the panel.
 */

jQuery(document).ready(function($) {
	
	// Fade out the save message
	jQuery('.fade').delay(1000).fadeOut(1000);
	
	jQuery('.cw-color').wpColorPicker();
	
	// Switches option sections
	jQuery('.group').hide();
	var activetab = '';
	if (typeof(localStorage) != 'undefined' ) {
		activetab = localStorage.getItem("activetab");
	}
	if (activetab != '' && jQuery(activetab).length ) {
		jQuery(activetab).fadeIn();
	} else {
		jQuery('.group:first').fadeIn();
	}
	jQuery('.group .collapsed').each(function(){
		jQuery(this).find('input:checked').parent().parent().parent().nextAll().each( 
			function(){
				if (jQuery(this).hasClass('last')) {
					jQuery(this).removeClass('hidden');
						return false;
					}
				jQuery(this).filter('.hidden').removeClass('hidden');
			});
	});
	
	if (activetab != '' && jQuery(activetab + '-tab').length ) {
		jQuery(activetab + '-tab').addClass('nav-tab-active');
	}
	else {
		jQuery('.nav-tab-wrapper a:first').addClass('nav-tab-active');
	}
	jQuery('.nav-tab-wrapper a').click(function(evt) {
		jQuery('.nav-tab-wrapper a').removeClass('nav-tab-active');
		jQuery(this).addClass('nav-tab-active').blur();
		var clicked_group = jQuery(this).attr('href');
		if (typeof(localStorage) != 'undefined' ) {
			localStorage.setItem("activetab", jQuery(this).attr('href'));
		}
		jQuery('.group').hide();
		jQuery(clicked_group).fadeIn();
		evt.preventDefault();
		
		// Editor Height (needs improvement)
		jQuery('.wp-editor-wrap').each(function() {
			var editor_iframe = jQuery(this).find('iframe');
			if ( editor_iframe.height() < 30 ) {
				editor_iframe.css({'height':'auto'});
			}
		});
	
	});
           					
	jQuery('.group .collapsed input:checkbox').click(unhideHidden);
				
	function unhideHidden(){
		if (jQuery(this).attr('checked')) {
			jQuery(this).parent().parent().parent().nextAll().removeClass('hidden');
		}
		else {
			jQuery(this).parent().parent().parent().nextAll().each( 
			function(){
				if (jQuery(this).filter('.last').length) {
					jQuery(this).addClass('hidden');
					return false;		
					}
				jQuery(this).addClass('hidden');
			});
           					
		}
	}
	
	// Image Options
	jQuery('.cw-radio-img-img').click(function(){
		jQuery(this).parent().parent().find('.cw-radio-img-img').removeClass('cw-radio-img-selected');
		jQuery(this).addClass('cw-radio-img-selected');		
	});
		
	jQuery('.cw-radio-img-label').hide();
	jQuery('.cw-radio-img-img').show();
	jQuery('.cw-radio-img-radio').hide();
		 	
		jQuery('#cw-display-excerpt').click(function() {
  		jQuery('#section-cw-excerpt-length').fadeToggle(400);
  		 jQuery('#section-cw-excerpt-text').fadeToggle(400);

	});

	if (jQuery('#cw-display-excerpt:checked').val() !== undefined) {
		jQuery('#section-cw-excerpt-length').show();	
		jQuery('#section-cw-excerpt-text').show();

	}
		
});	

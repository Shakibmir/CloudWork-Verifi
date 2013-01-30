(function ($) {

	$(document).ready(function () {

		$('#cw_verifi_confirm_pass').keyup(function(){
       		
       		$(".cw-error").hide();
       		
       		var hasError = false;
       		
       		var passwordVal = $("#cw_verifi_user_pass").val();
       		
       		var checkVal = $("#cw_verifi_confirm_pass").val();
        
       		if (passwordVal != checkVal ) {
            
            	$("#cw_verifi_confirm_pass").after('<p class="cw-error">Passwords do not match.</p>');
            
            	hasError = true;
            
            }
            
            if(hasError == true) {return false;}
        
        });
	});

})(jQuery);

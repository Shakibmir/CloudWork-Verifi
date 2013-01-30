(function ($) {

	$(document).ready(function () {

		$('#confirm_pass').keyup(function(){
       		
       		$(".cw-error").hide();
       		
       		var hasError = false;
       		
       		var passwordVal = $("#user_pass").val();
       		
       		var checkVal = $("#confirm_pass").val();
        
       		if (passwordVal != checkVal ) {
            
            	$("#confirm_pass").after('<p class="cw-error">Passwords do not match.</p>');
            
            	hasError = true;
            
            }
            
            if(hasError == true) {return false;}
        
        });
	});

})(jQuery);

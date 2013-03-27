jQuery(document).ready(function ($) {
		
		$('#cw_quote_meta', '#cw_link_meta', '#cw_video_meta').hide();

        $('#post-format-0, #post-format-aside, #post-format-gallery, #post-format-status, #post-format-video, #post-format-audio').is(':checked',function(){
        $('#cw_quote_meta').hide();
        $('#cw_video_meta').hide();
        $('#cw_link_meta').hide();});
         
        $('#post-format-0, #post-format-aside, #post-format-gallery, #post-format-status, #post-format-video, #post-format-audio').click(function() {
        $('#cw_quote_meta').hide();
        $('#cw_video_meta').hide();
        $('#cw_link_meta').hide();
        });
        
        $('#post-format-quote').is(':checked') ? $("#cw_quote_meta").show() : $("#cw_quote_meta").hide();
        $('#post-format-quote').click(function() {
            $("#cw_quote_meta").toggle(this.checked);
            $('#postimagediv').hide();
            $('#cw_video_meta').hide();
            $('#cw_link_meta').hide();  
        });
        
        $('#post-format-link').is(':checked') ? $("#cw_link_meta").show() : $("#cw_link_meta").hide();
        $('#post-format-link').click(function() {
            $("#cw_link_meta").toggle(this.checked);
            $("#postimagediv").hide();
        });
                $('#post-format-video').is(':checked') ? $("#cw_video_meta").show() : $("#cw_video_meta").hide();
        $('#post-format-video').click(function() {
            $("#cw_video_meta").toggle(this.checked);
            $("#postimagediv").hide();
        });
        $('#post-format-image').is(':checked') ? $("#postimagediv").show() : $("#postimagediv").hide();
        $('#post-format-image').click(function() {
            $("#postimagediv").toggle(this.checked);
        $('#cw_quote_meta').hide();
        $('#cw_video_meta').hide();
        $('#cw_link_meta').hide();       
        
         });
        
});
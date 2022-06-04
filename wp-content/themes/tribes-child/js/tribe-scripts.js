
jQuery(document).ready(function(){
    // Share Click
    jQuery(".dashicons-share").click(function() {
        jQuery(this).parent().parent().find(".tribe-share").toggle();
    })
});
(function($) {

    $.fn.startAjax = function(){
        console.log("start");
        $(this).attr("disabled", "disabled");
        $(this).append('<img class="ajax" src="gfx/ajax-status.gif" style="padding-left: 10px"/>');
    };

    $.fn.stopAjax = function(){
        $(this).removeAttr("disabled");
        $(this).find(".ajax").remove();
    };

})(jQuery);
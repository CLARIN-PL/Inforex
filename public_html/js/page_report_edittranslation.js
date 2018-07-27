var url = $.url(window.location.href);
var report_id = url.param('id');

$(function(){
    $("#saveTranslation").click(function(){
        var content = $("#leftContent").val();
        var data = {
            'content' : content,
            'report_id': report_id
        };

        var success = function(){
            location.reload();
        };

        doAjax("translation_add", data, success);
    });
});

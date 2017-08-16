var url = $.url(window.location.href);
var corpus_id = url.param('corpus');

$(function () {

    $(".relation_set_checkbox").click(function () {
        console.log("CLICK????");
        set($(this));
    });
});

function set($element){
    console.log("????");
    var relation_set_id = $($element).attr('relation_set_id');
    var operation_type = $element.is(':checked') ? "add" : "remove";

    var data = {
        corpus_id: corpus_id,
        relation_set_id: relation_set_id,
        operation_type : operation_type
    }

    var success = function(){
        if(operation_type == "add"){
            $element.parent().addClass("selected");
        } else{
            $element.parent().removeClass("selected");
        }
        $(".tablesorter").trigger("update");
    };

    doAjaxSync("corpus_relation_sets", data, success);
}

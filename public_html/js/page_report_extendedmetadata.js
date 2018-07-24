var url = $.url(window.location.href);
var report_id = url.param('id');
var corpus_id = url.param('corpus');
var translations;
var selected_translation;

$(function(){
    var data = {
        'report_id': report_id
    };

    var success = function(data){
        translations = data;
        console.log(translations);

        selected_translation = 0;
        determineButtonStatus();
    };

    doAjax("get_translations", data, success);

    $(".thumbnail img").click(function() {
            var src = $(this).attr('src');
            console.log(src);

        $("#image_preview").attr('src', src);
        $('#image_modal').modal('show');
    });

    $("#translation_language").change(function(){
        var code = $(this).val();
        var first_trans = translations[code][0].content;
        selected_translation = 0;
        determineButtonStatus();

        $("#current_translation").html(first_trans);
        console.log(first_trans);
    });

    $(".next_translation, .previous_translation").click(function(){
        var type = $(this).attr('id');

        console.log("click");

        var code = $("#translation_language").val();
        var number_of_translations = translations[code].length;

        if(type === "next"){
            if(selected_translation + 1 === number_of_translations){

            } else{
                selected_translation = selected_translation + 1;
                determineButtonStatus();

                var next_trans = translations[code][selected_translation].content;
                $("#current_translation").html(next_trans);


            }
        } else{
            if(selected_translation === 0){

            } else{
                selected_translation = selected_translation - 1;
                determineButtonStatus();

                var prev_trans = translations[code][selected_translation].content;
                $("#current_translation").html(prev_trans);
            }
        }

        console.log(selected_translation);


    });
});

function determineButtonStatus(){
    var code = $('#translation_language').val();

    if(translations.length === 0){
        return;
    }

    var number_of_translations = translations[code].length;
    var translation_id = translations[code][selected_translation].id;

    var translation_link = '<a href="index.php?page=report&amp;corpus='+corpus_id+'&amp;subpage=preview&amp;id='+translation_id+'">link</a>';
    var translation_number = selected_translation + 1 + '/' + number_of_translations + ' - ' + translation_link;

    $("#translation_number").html(translation_number);

    if(selected_translation + 1 === number_of_translations){
        $(".next_translation").prop('disabled', true);
    } else{
        $(".next_translation").prop('disabled', false);
    }

    if(selected_translation - 1 < 0){
        console.log("now");
        $(".previous_translation").prop('disabled', true);
    } else{
        $(".previous_translation").prop('disabled', false);
    }
}

$(function(){
    $(".import_anns_conf").change(function(){
        var item = $(this).attr('id');
        var value = $(this).val();
        $.cookie(item, value);

        location.reload();
    });

    $("#import_from_ccl_form").validate({
        rules: {
            cclFile: {
                required: true
            },
            annotation_set: {
                notEqual: "-1"
            }
        },
        messages: {
            cclFile:{
                required: "Upload a .CCL file"
            }
        }
    });

    $("#import_from_ccl_form").submit(function(e){
        if (!$('#import_from_ccl_form').valid()) {
            e.preventDefault();
        }

    });
});

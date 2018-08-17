$(function(){
    $(".inputfile").change(function(val){
        var fileName = val.target.value.split('\\').pop();
        $("#upload_label").html(fileName);
        $("#upload_btn").prop("disabled", false);
    });

    $(".delete_image").click(function(){
        if (confirm('Are you sure you want to delete this picture?')) {
            var image_id = $(this).attr('id');
            var image_name = $(this).attr('name');
            var data = {
                'image_id' : image_id,
                'image_name': image_name
            };

            var success = function(){
                window.location = window.location.href;
            };

            doAjax("image_delete", data, success);
        } else {
            // Do nothing!
        }
    });
});

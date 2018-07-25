$(function(){
    var url = $.url(window.location.href);
    var corpus_id = url.param('corpus');

    $(".corpus_flag_id, .flag_type").change(function(){
        var flag_val = $(".corpus_flag_id").val();
        var flag_status = $(".flag_type").val();
        var status = $(".selected_status_id").attr('id');
        
        if(flag_status !== "-" && flag_val !== "-"){
            window.location.href = "index.php?page=corpus_stats&corpus="+corpus_id+"&status="+status+"&flag="+flag_val+"&flag_status="+flag_status;
        }
    });

    $(".cancel_flags").click(function(){
        $(".corpus_flag_id").val("-");
        $(".flag_type").val("-");
        window.location.href = getRedirectUrl();
    });
});

function getRedirectUrl(){
    var flag_val = $(".corpus_flag_id").val();
    var flag_status = $(".flag_type").val();
    var status = $(".selected_status_id").attr('id');

    var url = "index.php?page=corpus_stats&corpus="+corpus_id+"&status="+status+"&flag="+flag_val+"&flag_status="+flag_status;

    return url;
}
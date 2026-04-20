$(function(){
    var url = $.url(window.location.href);
    var corpus_id = url.param('corpus');

    decorateCorpusStatsStatuses();

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

function decorateCorpusStatsStatuses(){
    $(".corpus-stats-filter-wrapper .metadata-filter-statuses a, .corpus-stats-filter-wrapper .metadata-filter-statuses em").each(function(){
        var $el = $(this);
        var text = $.trim($el.text()).toLowerCase();
        var badgeClass = "status-muted";

        if (text === "all" || text === "wszystkie") {
            badgeClass = "status-all";
        } else if (text.indexOf("przyję") !== -1 || text.indexOf("przyje") !== -1 || text.indexOf("accept") !== -1 || text.indexOf("zaakcept") !== -1 || text.indexOf("done") !== -1 || text.indexOf("ready") !== -1 || text.indexOf("final") !== -1) {
            badgeClass = "status-success";
        } else if (text.indexOf("załącz") !== -1 || text.indexOf("zalacz") !== -1 || text.indexOf("attach") !== -1) {
            badgeClass = "status-pending";
        } else if (text.indexOf("odłoż") !== -1 || text.indexOf("odloz") !== -1 || text.indexOf("process") !== -1 || text.indexOf("trakcie") !== -1 || text.indexOf("running") !== -1 || text.indexOf("progress") !== -1) {
            badgeClass = "status-progress";
        } else if (text.indexOf("odrzu") !== -1 || text.indexOf("usuni") !== -1 || text.indexOf("error") !== -1 || text.indexOf("reject") !== -1 || text.indexOf("błąd") !== -1 || text.indexOf("blad") !== -1) {
            badgeClass = "status-error";
        } else if (text.indexOf("niezn") !== -1 || text.indexOf("unknown") !== -1) {
            badgeClass = "status-muted";
        } else if (text.indexOf("new") !== -1 || text.indexOf("queue") !== -1 || text.indexOf("pending") !== -1 || text.indexOf("oczek") !== -1) {
            badgeClass = "status-pending";
        }

        $el.addClass("corpus-stats-status-badge " + badgeClass);

        if ($el.is("em")) {
            $el.addClass("is-active");
        }
    });
}

function getRedirectUrl(){
    var flag_val = $(".corpus_flag_id").val();
    var flag_status = $(".flag_type").val();
    var status = $(".selected_status_id").attr('id');

    var url = "index.php?page=corpus_stats&corpus="+corpus_id+"&status="+status+"&flag="+flag_val+"&flag_status="+flag_status;

    return url;
}

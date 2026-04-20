/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

//google.charts.load('current', {packages: ['corechart', 'bar']});
google.load("visualization", "1", {packages:["corechart", "bar"]});

var COOKIE_COUNTBY = "countby";

var pageElements = 10;
var wordFrequencies = new Array();
var tablesorterTable = "table#words_frequences";
var MIN_CHART_BAR_SLOTS = 3;
var MIN_WORD_FREQUENCY_HEIGHT = 240;
var MAX_WORD_FREQUENCY_HEIGHT = 360;

// Wysokość nagłówka
var headerH = 170;
// Wysokość stopki
var footerH = 40;
// Wysokość paginacji
var paginateH = 30;

var url = $.url(window.location.href);
var corpus_id = url.param('corpus');
var phrase = "";
var WORD_FREQUENCY_ROWS_PER_PAGE = 15;
var WORD_FREQUENCY_DOWNLOAD_FRAME_ID = "word-frequency-download-frame";

function triggerWordFrequencyDownload(downloadUrl) {
    var $frame = $("#" + WORD_FREQUENCY_DOWNLOAD_FRAME_ID);
    if (!$frame.length) {
        $frame = $("<iframe>", {
            id: WORD_FREQUENCY_DOWNLOAD_FRAME_ID,
            name: WORD_FREQUENCY_DOWNLOAD_FRAME_ID,
            style: "display:none;"
        }).appendTo("body");
    }
    $frame.attr("src", downloadUrl);
}

function buildWordFrequencyExportUrl(pageName) {
    return "index.php?page=" + encodeURIComponent(pageName) +
        "&corpus=" + encodeURIComponent(corpus_id || "") +
        "&ctag=" + encodeURIComponent($("select[name=ctag]").val() || "") +
        "&subcorpus_id=" + encodeURIComponent($("select[name=subcorpus_id]").val() || "") +
        "&phrase=" + encodeURIComponent($("input[name=phrase]").val() || "");
}

function setWordsFrequencyLoading(isLoading) {
    $("#words_frequency_loading").toggle(!!isLoading);
    $("#words_frequency .corpus-word-frequency-flexigrid").toggle(!isLoading);
}

function setWordsFrequencyEmpty(isVisible) {
    $("#words_frequency_empty").toggle(!!isVisible);
    $("#words_frequency .corpus-word-frequency-flexigrid").toggle(!isVisible);
}

function setWordsDistributionLoading(isLoading) {
    $("#words_distribution_loading").toggle(!!isLoading);
}

function getWordsFrequencyRows() {
    return $("table#words_frequences tbody tr").filter(function(){
        var rowId = $(this).attr("id") || "";
        return rowId.indexOf("row") === 0;
    });
}

function preprocessWordsFrequencyResponse(data) {
    var total = data && typeof data.total !== "undefined" ? parseInt(data.total, 10) : 0;
    if (isNaN(total)) {
        total = 0;
    }

    if (total === 0) {
        setWordsFrequencyLoading(false);
        setWordsFrequencyEmpty(true);
        setWordsDistributionLoading(false);
        $("#chart_link").hide().attr("href", "#");
        $("#words_per_subcorpus").html(
            '<div class="corpus-word-frequency-empty-copy">' +
                '<i class="fa fa-bar-chart" aria-hidden="true"></i>' +
                '<div class="corpus-word-frequency-empty-text">' +
                    '<strong>No distribution to display</strong>' +
                    '<span>There are no matching results for the current filters, so the chart is empty.</span>' +
                '</div>' +
            '</div>'
        );
    } else {
        setWordsFrequencyEmpty(false);
    }

    return data;
}

$(document).ready(function() {
	/* Do zmiany trybu zliczania */
    var countby = $.cookie(COOKIE_COUNTBY);
    if ( countby ){
    	$("#countby a").removeClass("active");
        $("#countby a." + countby).addClass("active");
    }
    $("#countby a").click(function(){
    	if ( !$(this).hasClass("active") ){
    		$("#countby a").removeClass("active");
    		$(this).addClass("active");
    		loadWordFrequencyPerCorpus();
    		$.cookie(COOKIE_COUNTBY, $("#countby a.active").attr("type"));
    	}
    })

    $("#chart_link").on("click", function(event){
        var href = $(this).attr("href");
        if (!href || href === "#") {
            event.preventDefault();
        }
    });
	
    /* Tabelka z frekwencją */
    var colModel = [
            {display: "No.", name : "no", width : 32, sortable : true, align: 'right'},
            {display: "Base", name : "base", width : 150, sortable : true, align: 'left'},
            {display: "Count", name : "c", width : 52, sortable : true, align: 'right'},
            {display: "Docs", name : "docs", width : 52, sortable : true, align: 'right'},
    ];      
    
    var ctag = $("select[name=ctag] option:selected").val();
    var subcorpus_id = $("select[name=subcorpus_id] option:selected").val();
    phrase = $("input[name=phrase]").val();

    var row_height = $("#words_frequency tr:last").outerHeight(true) + 8;
    $("#words_frequency").hide();
    $("#words_per_subcorpus").hide();
    row_height = row_height && row_height > 0 ? row_height : 28;
    var viewportHeight = $(window).height();
    var occupiedHeight = $(".corpus-word-frequency-toolbar").outerHeight(true) +
        $(".corpus-word-frequency-subheading").first().outerHeight(true) +
        $(".corpus-word-frequency-footer").outerHeight(true) + 140;
    var flexi_height = Math.max(MIN_WORD_FREQUENCY_HEIGHT, Math.min(MAX_WORD_FREQUENCY_HEIGHT, viewportHeight - occupiedHeight));
    var flexi_width = Math.max($("#words_frequency").closest(".corpus-word-frequency-column").width() - 12, 286);
    
    setWordsFrequencyLoading(true);
    setWordsFrequencyEmpty(false);
    setWordsDistributionLoading(true);

    flex = $("#words_frequences").flexigrid({
        url: 'index.php',
        params: [
            { "name":"corpus", "value": corpus_id },
            { "name":"ajax", "value": "words_frequency" },
            { "name":"ctag", "value": ctag},        
            { "name":"phrase", "value": phrase},
            { "name":"subcorpus_id", "value": subcorpus_id}        
        ],
        dataType: 'json',
        colModel : colModel,
        colResize: false,
        sortname: "c",
        sortorder: "desc",
        usepager: true,
        title: false,
        useRp: false,
        rp: WORD_FREQUENCY_ROWS_PER_PAGE,
        showTableToggleBtn: false,
        showToggleBtn: false,
        width: flexi_width,
        height: flexi_height,
        resizable: false,
        preProcess: preprocessWordsFrequencyResponse,
        onSubmit: function(){
            setWordsFrequencyLoading(true);
            setWordsFrequencyEmpty(false);
            return true;
        },
        onSuccess: function(){
            setWordsFrequencyLoading(false);
            setWordsFrequencyEmpty(false);
        	$("#words_per_subcorpus").text("Loading ...");
            window.setTimeout(loadWordFrequencyPerCorpus, 0);
            return true;
        }
    });
    
    /* Pozostałe */
    $("#words_frequency").show();
    $("#words_frequency .corpus-word-frequency-flexigrid").hide();
    $("#words_per_subcorpus").show();    
    	
    $("#export_selected").click(function(){
        triggerWordFrequencyDownload(buildWordFrequencyExportUrl("word_frequency_export"));
    });	

    $("#export_by_subcorpora").click(function(){
        triggerWordFrequencyDownload(buildWordFrequencyExportUrl("word_frequency_export_by_subcorpora"));
    });	

});


/**
 * Wczytuje frekwencję słów widocznych w tabeli z podziałem na podkorpusy.
 */
function loadWordFrequencyPerCorpus(){
	var base_ids = [];
	var base_ids_text = {};
	var subcorpus_ids_text = {};
	var count = $("#countby a.active").attr("type");

    setWordsDistributionLoading(true);
    $("#chart_link").hide().attr("href", "#");

	getWordsFrequencyRows().each(function(){
		var base_id = ($(this).attr("id") || "").replace("row", "");
		var base_text = $(this).find("td:nth-child(2)").text();
		base_ids.push(base_id);
		base_ids_text[base_id] = base_text;
	});

    if (!base_ids.length) {
        setWordsDistributionLoading(false);
        $("#words_per_subcorpus").html(
            '<div class="corpus-word-frequency-empty-copy">' +
                '<i class="fa fa-bar-chart" aria-hidden="true"></i>' +
                '<div class="corpus-word-frequency-empty-text">' +
                    '<strong>No distribution to display</strong>' +
                    '<span>There are no matching results for the current filters, so the chart is empty.</span>' +
                '</div>' +
            '</div>'
        );
        return;
    }

	$("select[name=subcorpus_id] option").each(function(){
		var value = $(this).attr("value");
		if ( value != "" ){
			subcorpus_ids_text[value] = $(this).text().trim();
		}
	});
	
	doAjax("words_frequency_subcorpora",
		{corpus_id: corpus_id, base_ids: base_ids},
		function(data){
			var words_freq = {};
			var freq = [];
			$(data).each(function(index,value){
				var subcorpus_id = value['subcorpus_id'];
				var word_id = value['base_id'];
				if ( !(word_id in words_freq) ){
					words_freq[word_id] = {"text": value['text']};
				}
				words_freq[word_id][subcorpus_id] = value[ count == undefined || count == "words" ? 'c' : 'docs' ];
			});
			var row = ['Base'];
			var total = {}
            $.each(subcorpus_ids_text, function(subcorpus_id,value){
                 row.push(value);
                 total[subcorpus_id] = 0;
            });
			freq.push(row);

			$.each(base_ids, function(index,base_id){
				var row = [base_ids_text[base_id]];
				$.each(subcorpus_ids_text, function(subcorpus_id,value){
					var f = (subcorpus_id in words_freq[base_id]) ? parseFloat(words_freq[base_id][subcorpus_id]) : 0;
					row.push(f);
					total[subcorpus_id] += f;
				});
				freq.push(row);
			});
			if ( phrase != '' ){
				var row = ['Total count'];
				$.each(subcorpus_ids_text, function(subcorpus_id,value){
					row.push(total[subcorpus_id]);
				});
				freq.push(row);
			}

            while ((freq.length - 1) < MIN_CHART_BAR_SLOTS) {
                var emptyRow = [' '];
                $.each(subcorpus_ids_text, function(){
                    emptyRow.push(0);
                });
                freq.push(emptyRow);
            }
			
			var data = google.visualization.arrayToDataTable(freq);
                var chartContainer = $("#words_per_subcorpus");
                var visibleBarCount = base_ids.length + (phrase != '' ? 1 : 0);
                var chartHeight = Math.max(210, Math.min(320, 88 + (Math.max(visibleBarCount, MIN_CHART_BAR_SLOTS) * 30)));
                var chartAreaTop = 28;
                var chartAreaBottom = 52;
                var chartAreaLeft = 104;
                var chartAreaWidth = Math.max(chartContainer.width() - chartAreaLeft - 24, 180);
                var chartAreaHeight = Math.max(chartHeight - chartAreaTop - chartAreaBottom, 140);
      			var options = {
        			title: "inforex.clarin-pl.eu",
			        height: chartHeight,
			        legend: { position: 'bottom', aligment: 'start' },
			        bar: { groupWidth: '89%' },
			        isStacked: "relative",
			        fontSize: 12,
                    hAxis: {
                        minValue: 0
                    },
                    vAxis: {
                        textStyle: {
                            fontSize: 11
                        }
                    },
			        chartArea:{
                        left: chartAreaLeft,
                        top: chartAreaTop,
                        width: chartAreaWidth,
                        height: chartAreaHeight
                    }
				
		      };
		      var chart = new google.visualization.BarChart(document.getElementById('words_per_subcorpus'));
		      google.visualization.events.addListener(chart, 'ready', function () {
                    var exportName = "word-distribution-corpus-" + corpus_id + ".png";
                    $("#chart_link")
                        .attr("href", chart.getImageURI())
                        .attr("download", exportName)
                        .show();
                    setWordsDistributionLoading(false);
		      });
		      chart.draw(data, options);
		},
        function(){
            setWordsDistributionLoading(false);
            $("#words_per_subcorpus").text("Failed to load subcorpus distribution");
        },
        null,
        null);
}	

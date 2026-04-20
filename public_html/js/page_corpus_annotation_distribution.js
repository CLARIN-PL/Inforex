/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

//google.charts.load('current', {packages: ['corechart', 'bar']});
google.load("visualization", "1", {packages:["corechart", "bar"]});

var COOKIE_COUNTBY = "countby";

var url = $.url(window.location.href);
var corpus_id = url.param('corpus');
var phrase = "";
var annotation_stage = "";
var annotation_set_id = "";

function setAnnotationDistributionLoading(isLoading) {
    $("#annotation_distribution_loading").toggle(!!isLoading);
}

function getAnnotationFrequencyGridWidth() {
    return Math.max($("#annotation_frequency .annotation-distribution-flexigrid").innerWidth() || 0, 280);
}

function buildAnnotationFrequencyColModel(gridWidth) {
    var noWidth = 26;
    var countWidth = 48;
    var docsWidth = 48;
    var typeWidth = Math.max(76, Math.floor(gridWidth * 0.24));
    var baseWidth = Math.max(104, gridWidth - noWidth - countWidth - docsWidth - typeWidth - 18);

    return [
        {display: "No.", name : "no", width : noWidth, sortable : false, align: 'right'},
        {display: "Base", name : "text", width : baseWidth, sortable : false, align: 'left'},
        {display: "Type", name : "type_name", width : typeWidth, sortable : false, align: 'left'},
        {display: "Count", name : "c", width : countWidth, sortable : false, align: 'right'},
        {display: "Docs", name : "docs", width : docsWidth, sortable : false, align: 'right'}
    ];
}

$(document).ready(function() {

    $("#chart_link").on("click", function(event){
        var href = $(this).attr("href");
        if (!href || href === "#") {
            event.preventDefault();
        }
    });

    function adjustAnnotationFrequencyHeight() {
        var $grid = $("#annotation_frequency .annotation-distribution-flexigrid .flexigrid");
        var $body = $grid.find("div.bDiv");
        var $rows = $body.find("tbody tr:visible");
        var visibleRows = $rows.length;
        var rowHeight = Math.max(18, Math.ceil($rows.first().outerHeight() || row_height || 22));
        var bodyHeight = (Math.max(1, Math.min(rows_per_page, visibleRows || rows_per_page)) * rowHeight) + 2;

        $body.css("height", bodyHeight + "px");
    }
	
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
    		loadAnnotationFrequencyPerCorpus();
    		$.cookie(COOKIE_COUNTBY, $("#countby a.active").attr("type"));
    	}
    })
	
    /* Tabelka z frekwencją */
    var gridWidth = getAnnotationFrequencyGridWidth();
    var colModel = buildAnnotationFrequencyColModel(gridWidth);
    
    var ctag = $("select[name=ctag] option:selected").val();
    var subcorpus_id = $("select[name=subcorpus_id] option:selected").val();
    var annotation_type_id = $("select[name=annotation_type_id] option:selected").val();
    annotation_stage = $("select[name=annotation_stage] option:selected").val();
    phrase = $("input[name=phrase]").val();
    annotation_set_id = $("select[name=annotation_set_id] option:selected").val();

    var row_height = Math.max(18, ($("#annotation_frequency_table tr:last").outerHeight(true) || 22));
    $("#annotation_frequency").hide();
    $("#annotations_per_subcorpus").hide();
    setAnnotationDistributionLoading(true);
    var rows_per_page = 15;
    var flexi_height = (row_height * rows_per_page) + 12;
    
    flex = $("#annotation_frequency_table").flexigrid({
        url: 'index.php',
        params: [
            { "name":"corpus", "value": corpus_id },
            { "name":"ajax", "value": "annotation_frequency" },
            { "name":"subcorpus_id", "value": subcorpus_id},
            { "name":"phrase", "value": phrase},
            { "name":"annotation_type_id", "value": annotation_type_id},
            { "name":"annotation_stage", "value": annotation_stage},
            { "name":"annotation_set_id", "value": annotation_set_id}
        ],
        dataType: 'json',
        colModel : colModel,
        colResize: false,
        sortname: "c",
        sortorder: "desc",
        usepager: true,
        title: false,
        useRp: false,
        rp: rows_per_page,
        showTableToggleBtn: false,
        showToggleBtn: false,
        width: gridWidth + "px",
        height: flexi_height,
        resizable: false,
        onSuccess: function(){
            adjustAnnotationFrequencyHeight();
            setAnnotationDistributionLoading(true);
        	loadAnnotationFrequencyPerCorpus();
        }
    });

    setTimeout(adjustAnnotationFrequencyHeight, 0);
    
    /* Pozostałe */
    $("#annotation_frequency").show();
    $("#annotations_per_subcorpus").show();    
    	
//    $("#export_selected").click(function(){
//    	window.location.href=window.location.href.replace("page=word_frequency", "page=word_frequency_export");
//    });	
//
//    $("#export_by_subcorpora").click(function(){
//    	window.location.href=window.location.href.replace("page=word_frequency", "page=word_frequency_export_by_subcorpora");
//    });	

});


/**
 * Wczytuje frekwencję słów widocznych w tabeli z podziałem na podkorpusy.
 */
function loadAnnotationFrequencyPerCorpus(){
	var texts = [];
	var texts_text = {};
	var subcorpus_ids_text = {};
	var count = $("#countby a.active").attr("type");

    setAnnotationDistributionLoading(true);
    $("#chart_link").hide().attr("href", "#");

	$("table#annotation_frequency_table tbody tr").each(function(){
		var text = $(this).find("td:nth-child(2)").text().trim();
		var type = $(this).find("td:nth-child(3)").text().trim();
		var key = text + ":" + type;
		texts.push(key);
		texts_text[key] = text;
	});

	$("select[name=subcorpus_id] option").each(function(){
		var value = $(this).attr("value");
		if ( value != "" ){
			subcorpus_ids_text[value] = $(this).text().trim();
		}
	});
	if ( texts.length == 0 ){
		$("#annotations_per_subcorpus").text("No annotation found");
        setAnnotationDistributionLoading(false);
		return;
	}
	
	doAjax("annotation_frequency_subcorpora",
		{corpus_id: corpus_id, texts: texts, annotation_stage: annotation_stage},
		function(data){
			var words_freq = {};
			var freq = [];
			$(data).each(function(index,value){
				var subcorpus_id = value['subcorpus_id'];
				var word_id = value['text'];
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
			
			$.each(texts, function(index,base_id){
				var row = [texts_text[base_id]];
				$.each(subcorpus_ids_text, function(subcorpus_id,value){
					var f = (subcorpus_id in words_freq[base_id]) ? parseFloat(words_freq[base_id][subcorpus_id]) : 0;
					total[subcorpus_id] += f;
					row.push(f);
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

			var data = google.visualization.arrayToDataTable(freq);
                var chartContainer = $("#annotations_per_subcorpus");
                var chartContainerWidth = Math.max(chartContainer.width(), 320);
                var chartLeft = Math.max(120, Math.min(168, Math.round(chartContainerWidth * 0.28)));
                var chartWidth = Math.max(chartContainerWidth - chartLeft - 28, 160);
      			var options = {
      				title: "inforex.clarin-pl.eu",
      				titlePosition: 'bottom',
			        height: $("#annotation_frequency_table").height() + 70 ,
			        legend: { position: 'bottom', aligment: 'start' },
			        bar: { groupWidth: '75%' },
			        isStacked: "relative",
			        fontSize: 12,
			        chartArea:{left:chartLeft,top:20,width:chartWidth,height:$("#annotation_frequency_table").height()}
				
		      };
		      var chart = new google.visualization.BarChart(document.getElementById('annotations_per_subcorpus'));
		      google.visualization.events.addListener(chart, 'ready', function () {
				var exportName = "annotation-distribution-corpus-" + corpus_id + ".png";
				$("#chart_link")
                    .attr("href", chart.getImageURI())
                    .attr("download", exportName)
                    .show();
                setAnnotationDistributionLoading(false);
		      });
		      chart.draw(data, options);
		},
		null,null,null);
}	

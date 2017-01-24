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
    		loadAnnotationFrequencyPerCorpus();
    		$.cookie(COOKIE_COUNTBY, $("#countby a.active").attr("type"));
    	}
    })
	
    /* Tabelka z frekwencją */
    var colModel = [
            {display: "No.", name : "no", width : 30, sortable : false, align: 'right'},
            {display: "Base", name : "text", width : 160, sortable : false, align: 'left'},
            {display: "Type", name : "type_name", width : 120, sortable : false, align: 'left'},
            {display: "Count", name : "c", width : 30, sortable : false, align: 'right'},
            {display: "Docs", name : "docs", width : 30, sortable : false, align: 'right'},
    ];      
    
    var ctag = $("select[name=ctag] option:selected").val();
    var subcorpus_id = $("select[name=subcorpus_id] option:selected").val();
    var annotation_type_id = $("select[name=annotation_type_id] option:selected").val();
    phrase = $("input[name=phrase]").val();

    var row_height = $("#annotation_frequency tr:last").outerHeight(true) + 8;
    $("#annotation_frequency").hide();
    $("#annotations_per_subcorpus").hide();
    var flexi_height = $(window).height() - $("body").outerHeight(true) - 50;
    var rows_per_page = Math.floor(flexi_height / row_height); 
    
    flex = $("#annotation_frequency_table").flexigrid({
        url: 'index.php',
        params: [
            { "name":"corpus", "value": corpus_id },
            { "name":"ajax", "value": "annotation_frequency" },
            { "name":"subcorpus_id", "value": subcorpus_id},
            { "name":"phrase", "value": phrase},
            { "name":"annotation_type_id", "value": annotation_type_id}
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
        width: 400,
        height: flexi_height,
        resizable: false,
        onSuccess: function(){
        	$("#annotations_per_subcorpus").text("Loading ...");
        	loadAnnotationFrequencyPerCorpus();
        }
    });
    
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
	
	doAjax("annotation_frequency_subcorpora",
		{corpus_id: corpus_id, texts: texts},
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
      			var options = {
			        height: $("#annotation_frequency_table").height() + 40,
			        legend: { position: 'bottom', aligment: 'start' },
			        bar: { groupWidth: '75%' },
			        isStacked: "relative",
			        fontSize: 12,
			        chartArea:{left:200,top:0,width:$("#annotations_per_subcorpus").width()-220,height:$("#annotation_frequency_table").height()}
				
		      };
		      var chart = new google.visualization.BarChart(document.getElementById('annotations_per_subcorpus'));
		      chart.draw(data, options);
		},
		null,null,null);
}	

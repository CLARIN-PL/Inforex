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

// Wysokość nagłówka
var headerH = 170;
// Wysokość stopki
var footerH = 40;
// Wysokość paginacji
var paginateH = 30;

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
    		loadWordFrequencyPerCorpus();
    		$.cookie(COOKIE_COUNTBY, $("#countby a.active").attr("type"));
    	}
    })
	
    /* Tabelka z frekwencją */
    var colModel = [
            {display: "No.", name : "no", width : 40, sortable : true, align: 'right'},
            {display: "Base", name : "base", width : 200, sortable : true, align: 'left'},
            {display: "Count", name : "c", width : 60, sortable : true, align: 'right'},
            {display: "Docs", name : "docs", width : 60, sortable : true, align: 'right'},
    ];      
    
    var ctag = $("select[name=ctag] option:selected").val();
    var subcorpus_id = $("select[name=subcorpus_id] option:selected").val();
    phrase = $("input[name=phrase]").val();

    var row_height = $("#words_frequency tr:last").outerHeight(true) + 8;
    $("#words_frequency").hide();
    $("#words_per_subcorpus").hide();
    var flexi_height = $(window).height() - $("body").outerHeight(true) - 70;
    var rows_per_page = Math.floor(flexi_height / row_height); 
    
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
        rp: rows_per_page,
        showTableToggleBtn: false,
        showToggleBtn: false,
        width: 400,
        height: flexi_height,
        resizable: false,
        onSuccess: function(){
        	$("#words_per_subcorpus").text("Loading ...");
        	loadWordFrequencyPerCorpus();
        }
    });
    
    /* Pozostałe */
    $("#words_frequency").show();
    $("#words_per_subcorpus").show();    
    	
    $("#export_selected").click(function(){
    	window.location.href=window.location.href.replace("page=word_frequency", "page=word_frequency_export");
    });	

    $("#export_by_subcorpora").click(function(){
    	window.location.href=window.location.href.replace("page=word_frequency", "page=word_frequency_export_by_subcorpora");
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

	$("table#words_frequences tbody tr").each(function(){
		var base_id = $(this).attr("id").replace("row", "");
		var base_text = $(this).find("td:nth-child(2)").text();
		base_ids.push(base_id);
		base_ids_text[base_id] = base_text;
	});

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
			
			var data = google.visualization.arrayToDataTable(freq);
      			var options = {
        			title: "inforex.clarin-pl.eu",
			        height: $("#words_frequences").height(),
			        legend: { position: 'bottom', aligment: 'start' },
			        bar: { groupWidth: '75%' },
			        isStacked: "relative",
			        fontSize: 12,
			        chartArea:{left:100,top:20,width:$("#words_per_subcorpus").width()-120,height:$("#words_frequences").height()}
				
		      };
		      var chart = new google.visualization.BarChart(document.getElementById('words_per_subcorpus'));
		      google.visualization.events.addListener(chart, 'ready', function () {
		    	    var link = '<a href="' + chart.getImageURI() + '" target="_blank">Open chart as a PNG file</a>';
		    	    $("#chart_link").html(link);
		      });
		      chart.draw(data, options);
		},
		null,null,null);
}	

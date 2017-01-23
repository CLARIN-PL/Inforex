/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

//google.charts.load('current', {packages: ['corechart', 'bar']});
google.load("visualization", "1", {packages:["corechart", "bar"]});

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
var subcorpus = url.param('subcorpus');
var ctag = url.param('ctag');

function getWFRow(wf_entry, index){
	var row = "<tr base_id='" + wf_entry['base_id']  + "'>";
    row += '<td style="text-align: right">' + ++index + '</td>';
	row += '<td><b>' + wf_entry['base'] + '</b></td>';
	row += '<td style="text-align: right"><a href="index.php?page=browse&amp;corpus='+corpus_id+'&amp;reset=1&amp;base=' + wf_entry['base'] + '&amp;subcorpus=' + subcorpus + '" title="show list of documents in new window">' + wf_entry['c'] + '</td>';
	row += '<td style="text-align: right">' + wf_entry['docs'] + '</td>';
	row += '<td style="text-align: right">' + wf_entry['docs_per'].toFixed(2) + '</td>';
	row += '<td style="text-align: right">' + wf_entry['docs_c'].toFixed(2) + '</td>';
    row += '</tr>';    

	return row;
}


function displayTable(){
	$(tablesorterTable).trigger("update"); 
	var sorting = [[2,1],[0,0]]; 
    $(tablesorterTable).trigger("sorton",[sorting]);
    var paggingContainer = '.pagging';
    // Bieżąca wysokość okna
    var windowH = window.innerHeight;
    // Przyjęta do obliczeń wysokość wiersza
    var rowH = 23;
    // Liczba wyświetlanych wierszy
    var elems = Math.floor((windowH - headerH - 2*paginateH - footerH) / rowH);
    // Wyświetl obliczoną liczbę wierszy, ale nie mniej niż 10
    pageElements = Math.max(pageElements, elems); 
    
	$(paggingContainer + ' .pagesize').val(pageElements);	
    $(tablesorterTable).tablesorterPager({
    	container: $(paggingContainer),
        positionFixed: false,
        size: pageElements,
        view: 'punbb',
        viewPunbbVisiblePageNumberMargin: 4,
        viewPunbbVisiblePageNumberMarginAtCorners: 2,
        currentPageNumber: 'active',
        currentPageUrlId: 'page',
    }).bind('pagerChange pagerComplete pagerInitialized pageMoved', function(e,c){
    	console.log("x");
    });

    // $(tablesorterTable + ' .header').click(function() {
    //     $(paggingContainer + ' .first').click();
    // });
}

$(document).ready(function() {
	
    var colModel = [
            {display: "No.", name : "no", width : 40, sortable : true, align: 'right'},
            {display: "Base", name : "base", width : 200, sortable : true, align: 'left'},
            {display: "Count", name : "c", width : 60, sortable : true, align: 'right'},
            {display: "Docs", name : "docs", width : 60, sortable : true, align: 'right'},
    ];      
    
    var ctag = $("select[name=ctag] option:selected").val();
    var subcorpus_id = $("select[name=subcorpus_id] option:selected").val();

    var row_height = $("#words_frequency tr:last").outerHeight(true) + 8;
    $("#words_frequency").hide();
    $("#words_per_subcorpus").hide();
    var flexi_height = $(window).height() - $("body").outerHeight(true) - 50;
    var rows_per_page = Math.floor(flexi_height / row_height); 
    
    flex = $("#words_frequences").flexigrid({
        url: 'index.php',
        params: [
            { "name":"corpus", "value": corpus_id },
            { "name":"ajax", "value": "words_frequency" },
            { "name":"ctag", "value": ctag},        
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
    
    $("#words_frequency").show();
    $("#words_per_subcorpus").show();    
    	
    $("#export_selected").click(function(){
    	window.location.href=window.location.href.replace("page=word_frequency", "page=word_frequency_export");
    });	
});

/**
 * Wczytuje frekwencję słów widocznych w tabeli z podziałem na podkorpusy.
 */
function loadWordFrequencyPerCorpus(){
	var base_ids = [];
	var base_ids_text = {};
	var subcorpus_ids_text = {};

	$("table#words_frequences tbody tr").each(function(){
		var base_id = $(this).attr("id").replace("row", "");
		var base_text = $(this).find("td:nth-child(2)").text();
		base_ids.push(base_id);
		base_ids_text[base_id] = base_text;
	});

	$("select[name=subcorpus_id] option").each(function(){
		subcorpus_ids_text[$(this).attr("value")] = $(this).text();
	});
	
	doAjax("words_frequency_subcorpora",
		{corpus_id: corpus_id, base_ids: base_ids},
		function(data){
			var last_word_id = 0;
			var words_freq = {};
			var freq = [];
			$(data).each(function(index,value){
				var subcorpus_id = value['subcorpus_id'];
				var word_id = value['base_id'];
				if ( word_id != last_word_id ){
					last_word_id = word_id;
					words_freq[word_id] = {"text": value['text']};
				}
				words_freq[word_id][subcorpus_id] = value['c'];
			});
			var row = ['Base'];
            $.each(subcorpus_ids_text, function(subcorpus_id,value){
                 row.push(value);
            });
			freq.push(row);

			$.each(base_ids, function(index,base_id){
				var row = [base_ids_text[base_id]];
				$.each(subcorpus_ids_text, function(subcorpus_id,value){
					row.push((subcorpus_id in words_freq[base_id]) ? parseFloat(words_freq[base_id][subcorpus_id]) : 0);
				});
				freq.push(row);
			});
			var data = google.visualization.arrayToDataTable(freq);
      			var options = {
			        height: $("#words_frequences").height() + 40,
			        legend: { position: 'bottom', maxLines: 3 },
			        //bar: { groupWidth: '75%' },
			        isStacked: "relative",
			        fontSize: 12,
			        chartArea:{left:100,top:0,width:$("#words_per_subcorpus").width()-120,height:$("#words_frequences").height()}
				
		      };
		      var chart = new google.visualization.BarChart(document.getElementById('words_per_subcorpus'));
		      chart.draw(data, options);
		},
		null,null,null);
}	

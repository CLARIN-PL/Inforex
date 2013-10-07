/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

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
	var row = "<tr>";
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
        currentPageUrlId: 'page'
    });

    // $(tablesorterTable + ' .header').click(function() {
    //     $(paggingContainer + ' .first').click();
    // });
}

$(document).ready(function() { 
	$("table#words_frequences").tablesorter();

	var action = "words_frequency";
	var params = {
		corpus: corpus_id,
		subcorpus: subcorpus,
		ctag: ctag
	};

	var loaderElement = $("#wf_loader");

	var success = function(data){
		var rows = "";
		if(data){
			$.each(data, function(index, element){
				rows += getWFRow(element,index);
			});
			$("table#words_frequences tbody").append(rows);
			displayTable();
		}else{
			$("#nowords").show();
		}
	};

	doAjax(action, params, success, null, null, loaderElement);
});

	
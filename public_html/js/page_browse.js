/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

// Wysokość nagłówka
var headerH = 100;
// Wysokość stopki
var footerH = 40;
// Wysokość paginacji
var paginateH = 30;
// Szerokość paska przewijania
var scrollWidth = 20;
// Minimalna wysokość wiersza (flaga + 8px paddingu (4px-góra + 4px-dół))
var minRowH = 20;
// Obiekt flexgid reprezentujący tabelę z dokumentami
var flex = null;

// Parametry GET adresu url
var url = $.url(window.location.href);
var corpus_id = url.param('corpus');
var prev_report = url.param('r');

function resizeFilterPanel(desiredHeight){
    // Wysokość zawartości
    var contentHeight = $("#filter_menu").get(0).scrollHeight;
    // Wysokość panelu
    var currentHeight = $("#filter_menu").outerHeight();
    // Szerokość panelu
    var currentWidth = $("#filter_menu").outerWidth();
    // Czy jest potrzebny pasek przewijania
    var needsScroll = (contentHeight - desiredHeight) > 0;
    // Czy jest wyświetlony pasek przewijania
    var hasScroll =  contentHeight > currentHeight;
    // Szerokość do ustawienia
    var desiredWidth = currentWidth;
    
    if(needsScroll && !hasScroll){
        desiredWidth += scrollWidth;
        var tableDiv = $("#filter_menu").next().next();
        var tablePadding = parseInt($(tableDiv).css("padding-right"));
        $(tableDiv).css("padding-right", tablePadding + scrollWidth +"px");
    }

    if(!needsScroll && hasScroll){
        desiredWidth -= scrollWidth;
        var tableDiv = $("#filter_menu").next().next();
        var tablePadding = parseInt($(tableDiv).css("padding-right"));
        $(tableDiv).css("padding-right", tablePadding - scrollWidth +"px");   
    }


    $("#filter_menu").css("height", desiredHeight + "px");
    $("#filter_menu").css("width", desiredWidth + "px");
}

$(window).resize(function(){
    var windowH = window.innerHeight;
    resizeFilterPanel(windowH - headerH - footerH);
});

function animateOverflow(paragraph){
    var $paragraph = $(paragraph);
    var element = $paragraph.find('span.fs_span');
    element.css("position", "relative");    
    element.animate({left: '-'+ (element.width() - $paragraph.width())}, 3000);    
}

function animateOverflowFinito(paragraph){
    var $paragraph = $(paragraph);
    $paragraph.find('span.fs_span').animate({left: 0}, 3000);    
}

function hasScroll(div){
    return $(div).get(0).scrollHeight > $(div).outerHeight();
}

function getFreeSpace(){
    return $("table#table-documents").parent().innerWidth() - $("table#table-documents").innerWidth() - 15*hasScroll($("table#table-documents").parent());
}

/**
 * Resizes i-th column and returns final size
 * @param  {int} colNo        column number
 * @param  {int} desiredWidth desired width of the column
 * @return {int}              final column width
 */
function resizeColumn(colNo, desiredWidth){
    var freeSpace = getFreeSpace();
    console.log("freeSpace: "+freeSpace);
    var colWidth = $($("td:nth-child("+colNo+")").get(0)).outerWidth();
    
    // Jeśli nie ma miejsca to nie zwiększaj kolumny
    if(freeSpace <= 0) return colWidth;

    var newWidth = Math.min(colWidth+freeSpace, desiredWidth+5);
    console.log(newWidth+" =min("+(colWidth +freeSpace)+" "+(desiredWidth+5)+")");

    // Jeśli nowa szerokość jest mniejsza od bieżącej to nic nie rób
    if(colWidth >= newWidth) return colWidth;
    
    var hDiv = $("#table-documents").hDiv;
    var bDiv = $("#table-documents").bDiv;
    
    $('th:visible div:eq('+colNo+')',hDiv).css('width',newWidth);
	$('tr',bDiv).each (
		function ()
		{
			$('td:visible div:eq('+colNo+')',this).css('width',newWidth);
		}
	);    
    flex.grid.rePosDrag();
    flex.grid.fixHeight();
    return newWidth;
}

function resizeBaseColumn(){
    var maxNeededWidth = 0;
    $("td:nth-child(7) p").each(function(i,e){var w = $(e).outerWidth(); if(maxNeededWidth < w) maxNeededWidth = w;});
    var setWidth = resizeColumn(7, maxNeededWidth) - 15;
    console.log(setWidth);
    $("td:nth-child(7) p").each(function(i,e){
        $(e).css("width", setWidth+"px");
    });
}

function resizeTitleColumn(){
    var maxNeededWidth = 0;
    $("td:nth-child(3) a").each(function(i,e){var w = $(e).outerWidth(); if(maxNeededWidth < w) maxNeededWidth = w;});
    resizeColumn(2, maxNeededWidth);
	//var colModel = [{name:'title', width: 300}];
	//$("#table-documents").flexReload({colModel : colModel});
}

$(function() {
    // Bieżąca wysokość okna
    var windowH = window.innerHeight;
    // Ustaw wysokość panelu filtrów
    resizeFilterPanel(windowH - headerH - footerH);
    // Przyjęta do obliczeń wysokość wiersza
    var rowH = $("#table-documents tr:last").outerHeight() + 6;
    console.log(rowH);
    rowH = Math.max(rowH, minRowH);
    // Wysokość FlexiGrida
    var flexiHeight = windowH - headerH - 2*paginateH - footerH - 30;
    // Liczba wyświetlanych wierszy
    var elems = Math.floor((flexiHeight - 30) / rowH);
    // Wyświetl obliczoną liczbę wierszy, ale nie mniej niż 10
    var tableElementsPerPage = Math.max(10, elems); 
    var paggingContainer = '.pagging';
    var tablesorterTable = '#table-documents';

    var initPage = Math.ceil(init_from / tableElementsPerPage);

    flex = $("#table-documents").flexigrid({
        url: 'index.php',
        params: [
            { "name":"corpus","value": corpus_id },
            { "name":"ajax","value": "page_browse_get" },
            { "name":"r","value": prev_report }
        ],
        dataType: 'json',
        colModel : colModel,
        colResize: false,
        sortname: "id",
        sortorder: "asc",
        usepager: true,
        title: false,
        useRp: false,
        rp: tableElementsPerPage,
        showTableToggleBtn: false,
        showToggleBtn: false,
        //width: $("div#page_content").innerWidth() - $("div#filter_menu").innerWidth() - 20,
        height: flexiHeight,
        newp: initPage,
        resizable: false
    });


    $(document).ajaxSuccess(function( event, xhr, settings){
        var url = $.url('?'+settings['data']);
        var ajax = url.param('ajax');

        if(ajax == 'page_browse_get'){
            // Tytuły
            $("a.tip").tooltip({
                showURL: false
            });

            resizeTitleColumn();

        }else{
            // Zdania
            $("p.tip").tooltip({
                bodyHandler: function() { 
                    return $($(this).next()).html(); 
                },
                showURL: false 
            });
            resizeBaseColumn();
        }
    });

    // Rozwijane filtry
    $("a.toggle_simple").live("click",function(){
        var filterDiv = $(this).parent();
        var filterWidth = $(filterDiv).outerWidth();
        var currentWidth = $("#filter_menu").outerWidth();
        var contentHeight = $("#filter_menu").get(0).scrollHeight;
        var currentHeight = $("#filter_menu").outerHeight();
        var hasScroll =  contentHeight > currentHeight;

        if(hasScroll && currentWidth == filterWidth){
            $("#filter_menu").css("width", (currentWidth + scrollWidth) + "px");
        }
        else if(!hasScroll && currentWidth > filterWidth){
            $("#filter_menu").css("width", (currentWidth - scrollWidth) + "px");   
        }
    });


    var html_ajax_loader = '<img src="gfx/ajax.gif" class="ajax_loader" />';
    
    var add_sentence_to_report = function(report_id, sentence_data, cell) {
    	var html = '<p class="found_sentence tip" data-word="'+sentence_data.word+'"><span class="fs_span">';
        html += sentence_data.sentence_with_highlighted;
        html += '<span></p>';
        html += '<p style="display:none">'+sentence_data.sentence_with_highlighted+'</p>';
        cell.append(html);
    };
    
    var add_sentences_to_report = function(report_id, sentences_data, cell) {
    	sentences_data.forEach(function(sentence_data) {
    		add_sentence_to_report(report_id, sentence_data, cell);
        });
    };

    $('#table-documents').delegate('.ajax_link_get_sentences', 'click', function() {
        var report_id = $(this).attr('data-report_id');
        var base = $(this).attr('data-search_base');
        var current_cell = $(this).parents('td');
        current_cell.html(html_ajax_loader);
        
        var ajax_action = "browse_get_sentences_with_base_in_report";
        var send_data = {};
        send_data.report_id = report_id;
        send_data.base = base;

        var success = function(data){
        	current_cell.empty();
        	if (data.length === 0) {
                current_cell.html('Not found');
            } else {
                add_sentences_to_report(report_id, data, current_cell);
            }
        };
        
        var error = function(){
        	current_cell.html('Error: Problem encountered during retrieving data.');
        };
        
        
        doAjax("browse_get_sentences_with_base_in_report",send_data, success, error);
        
        return false;
    });
    
    $('input[name="random_order"]').change(function() {
        $('input[name="random_order"]').attr('checked', $(this).is(':checked'));
    });
    
    $('select[name="results_limit"]').change(function() {
        $('select[name="results_limit"]').val($(this).val());
    });
    
});

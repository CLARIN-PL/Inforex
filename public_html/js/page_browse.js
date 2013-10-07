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
    //$("#filter_menu").css("overflow-y", 'auto');
}

$(window).resize(function(){
    var windowH = window.innerHeight;
    resizeFilterPanel(windowH - headerH - footerH);
});

function animateOverflow(cell){
    var $cell = $(cell);
    var element = $cell.find('div a');
    element.css("position", "relative");    
    element.animate({left: '-'+ (element.width() - $cell.find('div').width())}, 3000);    
}

function animateOverflowFinito(cell){
    var $cell = $(cell);
    $cell.find('div a').animate({left: 0}, 3000);    
}


$(function() {
    // Bieżąca wysokość okna
    var windowH = window.innerHeight;
    // Ustaw wysokość panelu filtrów
    resizeFilterPanel(windowH - headerH - footerH);
    // Przyjęta do obliczeń wysokość wiersza
    var rowH = $("#table-documents tr:last").outerHeight() + 2;
    // Liczba wyświetlanych wierszy
    var elems = Math.floor((windowH - headerH - 2*paginateH - footerH) / rowH);
    // Wyświetl obliczoną liczbę wierszy, ale nie mniej niż 10
    var tableElementsPerPage = Math.max(10, elems); 
    var paggingContainer = '.pagging';
    var tablesorterTable = '#table-documents';
    $(paggingContainer + ' .pagesize').val(tableElementsPerPage);
    jQuery(tablesorterTable).tablesorter()
            .tablesorterPager({
        container: $(paggingContainer),
        positionFixed: false,
        size: tableElementsPerPage,
        view: 'punbb',
        viewPunbbVisiblePageNumberMargin: 4,
        viewPunbbVisiblePageNumberMarginAtCorners: 2,
        currentPageNumber: 'active',
        currentPageUrlId: 'page'
    });
    $(tablesorterTable + ' .header').click(function() {
        $(paggingContainer + ' .first').click();
    });

    // Przewijane tytuły
    $("td div").live("mouseenter",function(){
        animateOverflow($(this).parent());
    });
    $("td div").live("mouseleave",function(){
        animateOverflowFinito($(this).parent());
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
    
    var add_sentence_to_report = function(report_id, sentence_data) {
    	var html = '<p class="found_sentence" data-word="'+sentence_data.word+'">';
        html += sentence_data.sentence_with_highlighted;
        html += '</p>';
        $('tr#report_'+report_id+' td.found_base_form').append(html);
    }
    
    var add_sentences_to_report = function(report_id, sentences_data) {
    	sentences_data.forEach(function(sentence_data) {
    		add_sentence_to_report(report_id, sentence_data);
        })
    }

    $('#table-documents').delegate('.ajax_link_get_sentences', 'click', function() {
        var report_id = parseInt($(this).parents('tr').attr('data-report_id'));
        var base = $(this).parents('table').attr('data-search_base');
        
        $(this).parents('td').html(html_ajax_loader);
        
        var ajax_action = "browse_get_sentences_with_base_in_report";
        var send_data = {};
        send_data.report_id = report_id;
        send_data.base = base;

        var success = function(data){
        	$('tr#report_'+report_id+' td.found_base_form').empty();
        	if (data.length === 0) {
                $('tr#report_'+report_id+' td.found_base_form').html('Not found');
            } else {
                add_sentences_to_report(report_id, data);
            }
        };
        
        var error = function(){
        	$('tr#report_'+report_id+' td.found_base_form').html('Error: Problem encountered during retrieving data.');
        };
        
        
        doAjax("browse_get_sentences_with_base_in_report",send_data, success, error);
        
        return false;
    });
    
    $('input[name="random_order"]').change(function() {
        $('input[name="random_order"]').attr('checked', $(this).is(':checked'));
    })
    
    $('select[name="results_limit"]').change(function() {
        $('select[name="results_limit"]').val($(this).val());
    })
    
});

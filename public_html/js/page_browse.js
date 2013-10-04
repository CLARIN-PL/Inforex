/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */


$(function() {
    // Bieżąca wysokość okna
    var windowH = window.innerHeight;
    // Przyjęta do obliczeń wysokość wiersza
    var rowH = 35;
    // Liczba wyświetlanych wierszy (130px to wysokość pozostałych elementów)
    var elems = Math.floor((windowH - 130) / rowH);
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

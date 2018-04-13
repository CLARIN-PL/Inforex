/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var test_limit = 100; //liczba testowanych jednocześnie dokumentów
var corpus_id = 0;
var documents_in_corpus = 0;
var count_active_tests = 0;
var active_timer = false;
var stop_test = true;
var annotations_active = [];

/*
obsługa - proces testowania
*/
function testProcess(from,error_num,test_name){
	if(from < documents_in_corpus){
		$('#' + test_name).find('td.test_process').html(from + '/' + documents_in_corpus + '<br><img class="ajax_indicator" src="gfx/ajax.gif"/>');
		if(error_num > 0)
			$('#' + test_name).find('td.test_result').html(error_num);
		if(stop_test){
			endSingleTest(test_name,error_num)
		}
		else{
			testAjax(from,error_num,test_name);
		}
	}
	else{
		endSingleTest(test_name,error_num)
	}
}

function startTest(){
	if($("input.activeTest:checked").length){
		stop_test = false;
		$("input.activeTests").attr("disabled", "disabled");
		$("input.activeTest").attr("disabled", "disabled");
		$("input.activeAnnotation").attr("disabled", "disabled");
		annotations_active = [];
		$(".activeAnnotation:checked").each(function(item,value){
			annotations_active.push($(value).val());		
		});
		$(".test_process").text("stop");
		count_active_tests = 0;
		$(".buttonTest").removeClass("stop").addClass("run");
		$(".buttonTest").text("Test stop");
		$(".activeTest:checked").each(function(){
			if($(this).hasClass("lin") || $(this).hasClass("tech")){
				$("#tests_document_list").find("."+$(this).attr('id')).remove();
				$(this).parent().parent().removeClass("wrong").removeClass("corect");
				$(this).parent().parent().find(".test_time").text(0);
				$(this).parent().parent().find(".test_result").html("<i>brak</i>");
				count_active_tests++;
				testProcess(0,0,$(this).attr('id'));
				$(this).parent().parent().find('td.test_time').addClass('running');
			}
		});
		timerStart();
	}
}
function stopTest(){
	timerStop();
	$(".buttonTest").removeClass("run").addClass("stop");
	$(".buttonTest").text("Test start");
	$("input.activeTests").removeAttr("disabled");
	$("input.activeTest").removeAttr("disabled");
	$(".buttonTest").removeAttr("disabled");
	$("input.activeAnnotation").removeAttr("disabled");
	$(".buttonTestAjaxIndicator").remove();
}

function forceStopTest(){
	stop_test = true;
	$(".buttonTest").text("Stop tests");
	$(".buttonTest").attr("disabled", "disabled");
	$(".buttonTest").after("<img class='buttonTestAjaxIndicator' src='gfx/ajax.gif'/>");		
}

function endSingleTest(test_name,error_num){
	--count_active_tests;
	$('#' + test_name).find('td.test_time').removeClass('running');
	$('#' + test_name).find('td.test_process').html(documents_in_corpus + '/' + documents_in_corpus);
	if(error_num > 0){
		$('#' + test_name).find('td.test_result').html(error_num);
		$('#' + test_name).addClass(' wrong');
	}
	else{
		$('#' + test_name).addClass(' corect');
	}
	if(!count_active_tests){
		stopTest();			
	}
}

/*
obsługa testów - ajax
*/
function testAjax(from,error_num,test_name){
	
	var params = {
		name: test_name,
		from: from,
		to: test_limit,
		error_num: error_num,
		corpus_id: corpus_id,
		annotations_active: annotations_active
	};
	
	var success = function(data){
		var html = '';
		var fn = window["html_" + test_name];
		for (a in data['data']){
			html += '<tr class="tests_items ' + test_name + '" style="display:none">';
			html += '	<td style="vertical-align: middle">' + data['data'][a]['error_num'] + '</td>';
			html += '	<td style="vertical-align: middle"><a target="_blank" href="index.php?page=report&amp;corpus=' + corpus_id + '&amp;subpage=annotator&amp;id=' + data['data'][a]['report_id'] + '">' + data['data'][a]['report_id'] + '</a></td>';
			html += '	<td style="vertical-align: middle">' + data['data'][a]['wrong_count'] + '</td>';
			html += '	<td style="vertical-align: middle"><a href="#" class="errors">wyświetl szczegóły</a></td>';							
			html += '</tr>';							
			for (element in data['data'][a]['test_result']){
				html += fn(data['data'][a]['test_result'][element],test_name);
			}
		}
		$('#tests_document_list').find('tbody').append(html);
		testProcess(from + test_limit,data['error_num'],test_name);
	};
	
	doAjax("tests_integrity", params, success);
}

// Html
function html_start(test_name){
	return '<tr class="tests_errors ' + test_name + '" style="display:none"><td colspan="3" class="empty"></td><td style="vertical-align: middle">';
}

function html_end(){
	return '</td></tr>';
}

function html_annotations(element){
	return '<span class="' + element['type1'] + '" title="an#' + element['id1'] + ':' + element['type1'] + '">' + element['text1'] + '</span> <span class="' + element['type2'] + '" title="an#' + element['id2'] + ':' + element['type2'] + '">' + element['text2'] + '</span>';	 
}

// Html functions
function html_empty_chunk(element,test_name){
	html = html_start(test_name);
	html += 'Pusty chunk: znajduje się w linii ' + element['line'];
	html += html_end();
	return html;	
}

function html_wrong_chunk(element,test_name){
	html = html_start(test_name);
	html += 'Line: ' + element['line'] + ' Column: ' + element['col'] + ' Description: ' + element['description'];
	html += html_end();
	return html;	
}

function html_wrong_tokens(element,test_name){
	html = html_start(test_name);
	html += 'Dla tokenu o indeksie ' + element['id'] + ' i zakesie [' + element['from'] + ', ' + element['to'] + '] nie istnieje token będący jego następnikiem';
	html += html_end();
	return html;	
}

function html_tokens_out_of_scale(element,test_name){
	html = html_start(test_name);
	html += 'Token o indeksie ' + element['id'] + ' i zakesie [' + element['from'] + ', ' + element['to'] + '] wykracza poza ramy dokumentu o długości [' + element['content_length'] + ']';
	html += html_end();
	return html;	
}

function html_wrong_annotations(element,test_name){
	html = html_start(test_name);
	html += 'Anotacja: <span class="' + element['annotation_type'] + '" title="an#' + element['annotation_id'] + ':' + element['annotation_type'] + '">' + element['annotation_text'] + '</span> o zakresie [' + element['annotation_from'] + ',' + element['annotation_to'] + '] ';
	html += (element['err'] == 1 ? 'przecina się z tokenem' : 'znajduje się w tokenie' ); 
	html += ' o indeksie ' + element['token_id'] + ' i zakesie [' + element['token_from'] + ', ' + element['token_to'] + ']';
	html += html_end();
	return html;	
}

function html_wrong_annotation_in_annotation(element,test_name){
	html = html_start(test_name);
	html += html_annotations(element);
	html += html_end();
	return html;	
}

function html_wrong_annotations_duplicate(element,test_name){
	html = html_start(test_name); 
	html += html_annotations(element);
	html += html_end();
	return html;
}

function html_wrong_annotations_by_annotation(element,test_name){
	html = html_start(test_name); 
	html += html_annotations(element);
	html += html_end();
	return html;
}

function html_wrong_annotations_by_sentence(element,test_name){
	html = html_start(test_name); 
	html += 'Anotacja: <span class="' + element['annotation_type'] + '" title="an#' + element['annotation_id'] + ':' + element['annotation_type'] + '">' + element['annotation_text'] + '</span> o zakresie [' + element['annotation_from'] + ',' + element['annotation_to'] + '] ';
	html += ' wykracza poza granice zdania w linii ' + element['line'];
	html += html_end();
	return html;
}

function html_wrong_annotation_chunks_type(element,test_name){
	html = html_start(test_name); 
	html += (element['err'] == 1 ? ' Frazy „duże” nie są rozłączne ' : (element['err'] == 2 ? ' Fraza „chunk_agp” przekracza granie fraz „dużych” ' : ' Fraza „chunk_qp” przekracza granie fraz „dużych” lub frazy „chunk_agp” ' ) );
	html += html_annotations(element);
	html += html_end();
	return html;
}

// Timer
function timer(count){
	setTimeout( function(){
		if(active_timer)
			timer(++count);
		$(".test_time.running").html(count);         
    }, 1000);
}

function timerStart(){
	active_timer = true;
	timer(0);
}

function timerStop(){
	active_timer = false;
}

$(function(){

	$(".tests_items").hide();
	$("#tests_document_list").hide();
	
	corpus_id = $('.corpus_id').attr('id');
	documents_in_corpus = $('.documents_in_corpus').attr('id');
	
	/*
	Obsługa tabeli z typami testów (po kliknięciu typ testu wyświetlana jest lista dokumentów dla danego testu)
	*/
	$("tr.group").click(function(){
		if($(this).hasClass('wrong') || $(this).hasClass('corect')){
			$("#tests_document_list").show();
			var test_id = $(this).attr('id');
			var test_name = $(this).children(".test_name").text();
			$(".tests_items").removeClass("showItem").hide();
			$(".tests_items").find("a.errors").html("wyświetl szczegóły");
			$("." + test_id).show();
			$(".tests_errors").hide();		
			$(".result_test_name").html(test_name + ":");
			if (! $(this).hasClass("selected")){
				$("tr.group").removeClass("selected");
				$(this).addClass("selected");	
			}
		}		
	});
	
	/*
	Obsługa tabeli z wynikami testów (po kliknięciu w pole testu wyświetlana jest lista naruszeń dla danego testu)
	*/
	$("tr.tests_items").live({
		click: function(){
			if ($(this).hasClass("showItem")){
				$(this).removeClass("showItem").nextUntil(".tests_items").hide();
				$(this).find("a.errors").html("wyświetl szczegóły");
			}
			else{  
				$(this).addClass("showItem").nextUntil(".tests_items").filter(".tests_errors").show();
				$(this).find("a.errors").html("ukryj szczegóły");
			}
		}				
	});
	
	$('a[href=#]').live('click', function(){
		var tr = $(this).parent().parent();
		if ($(tr).hasClass("showItem")){
				$(tr).removeClass("showItem").nextUntil(".tests_items").hide();
				$(this).html("wyświetl szczegóły");
		}
		else{  
				$(tr).addClass("showItem").nextUntil(".tests_items").filter(".tests_errors").show();
				$(this).html("ukryj szczegóły");
		}
		return false;
	});
	
	$("input.activeTests").live("click",function(){
		$(".activeTest").attr("checked",$(this).attr("checked"));
	});
	
	$("input.activeTest.allTech").live("click",function(){
		$(".activeTest.tech").attr("checked",$(this).attr("checked"));
	});
	
	$("input.activeTest.allLin").live("click",function(){
		$(".activeTest.lin").attr("checked",$(this).attr("checked"));
	});
	
	$(".buttonTest").live("click",function(){
		if($(this).hasClass("stop")){
			startTest();
		}
		else if($(this).hasClass("run")){
			forceStopTest();
		}
	});	
});
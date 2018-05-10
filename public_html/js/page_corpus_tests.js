/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var test_limit = 10; //liczba testowanych jednocześnie dokumentów
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
		if(error_num > 0) {
            $('#' + test_name).find('td.test_result').html(error_num);
        }
		if(stop_test){
			endSingleTest(test_name,error_num)
		} else{
			testAjax(from,error_num,test_name);
		}
	} else{
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
	$('#' + test_name).find('td.test_time').removeClass('running');
	$('#' + test_name).find('td.test_process').html(documents_in_corpus + '/' + documents_in_corpus);
	if(error_num > 0){
		$('#' + test_name).find('td.test_result').html(error_num);
		$('#' + test_name).addClass(' wrong');
	}else{
		$('#' + test_name).addClass(' corect');
	}
	if(!count_active_tests){
		stopTest();			
	}
}

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
		for (var a in data['data']){
            var details = "<ul>";
            for (var element in data['data'][a]['test_result']){
                details += "<li>" + fn(data['data'][a]['test_result'][element],test_name) + "</li>"
            }
            details += "</ul>";
			html += '<tr class="tests-items ' + test_name + '" style="display:none">';
			html += '	<td class="col-no">' + (parseInt(data['data'][a]['error_num'])+1) + '</td>';
			html += '	<td class="col-document-id"><a target="_blank" href="index.php?page=report&amp;corpus=' + corpus_id + '&amp;subpage=annotator&amp;id=' + data['data'][a]['report_id'] + '">' + data['data'][a]['report_id'] + '</a></td>';
			html += '	<td class="col-count">' + data['data'][a]['wrong_count'] + '</td>';
			html += '	<td>'+details+'</td>';
			html += '</tr>';
		}
		$('#tests_document_list').find('tbody').append(html);
		testProcess(from + test_limit,data['error_num'],test_name);
	};
	
	doAjax("tests_integrity", params, success);
}

function html_empty_chunk(element,test_name){
	return 'Pusty chunk: znajduje się w linii ' + element['line'];
}

function html_wrong_chunk(element,test_name){
	return 'Line: ' + element['line'] + ' Column: ' + element['col'] + ' Description: ' + element['description'];
}

function html_wrong_tokens(element,test_name){
	return 'Dla tokenu o indeksie ' + element['id'] + ' i zakesie [' + element['from'] + ', ' + element['to'] + '] nie istnieje token będący jego następnikiem';
}

function html_tokens_out_of_scale(element,test_name){
	return 'Token o indeksie ' + element['id'] + ' i zakesie [' + element['from'] + ', ' + element['to'] + '] wykracza poza ramy dokumentu o długości [' + element['content_length'] + ']';
}

function html_wrong_annotations(element,test_name){
	html = 'Anotacja: <span class="' + element['annotation_type'] + '" title="an#' + element['annotation_id'] + ':' + element['annotation_type'] + '">' + element['annotation_text'] + '</span> o zakresie [' + element['annotation_from'] + ',' + element['annotation_to'] + '] ';
	html += (element['err'] == 1 ? 'przecina się z tokenem' : 'znajduje się w tokenie' ); 
	html += ' o indeksie ' + element['token_id'] + ' i zakesie [' + element['token_from'] + ', ' + element['token_to'] + ']';
	return html;
}

function html_wrong_annotation_in_annotation(element,test_name){
	return html_annotations(element);
}

function html_wrong_annotations_duplicate(element,test_name){
	return html_annotations(element);
}

function html_wrong_annotations_by_annotation(element,test_name){
	return html_annotations(element);
}

function html_wrong_annotations_by_sentence(element,test_name){
	html = 'Anotacja: <span class="' + element['annotation_type'] + '" title="an#' + element['annotation_id'] + ':' + element['annotation_type'] + '">' + element['annotation_text'] + '</span> o zakresie [' + element['annotation_from'] + ',' + element['annotation_to'] + '] ';
	html += ' wykracza poza granice zdania w linii ' + element['line'];
	return html;
}

function html_wrong_annotation_chunks_type(element,test_name){
	html =  (element['err'] == 1 ? ' Frazy „duże” nie są rozłączne ' : (element['err'] == 2 ? ' Fraza „chunk_agp” przekracza granie fraz „dużych” ' : ' Fraza „chunk_qp” przekracza granie fraz „dużych” lub frazy „chunk_agp” ' ) );
	html += html_annotations(element);
	return html;
}

function timer(count){
	setTimeout( function(){
		if(active_timer) {
            timer(++count);
        }
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
	corpus_id = $.url(window.location.href).param('corpus');
	documents_in_corpus = $('.documents_in_corpus').attr('id');

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
            $('a[href=#]').click(function(){
                var tr = $(this).parent().parent();
                if ($(tr).hasClass("showItem")){
                    $(tr).removeClass("showItem").find("ul").hide();
                    $(this).html("wyświetl szczegóły");
                }else{
                    $(tr).addClass("showItem").find("ul").show();
                    $(this).html("ukryj szczegóły");
                }
                return false;
            });
		}		
	});

	$("input.activeTests").on("click",function(){
		$(".activeTest").attr("checked",$(this).attr("checked"));
	});
	
	$("input.activeTest.allTech").on("click",function(){
		$(".activeTest.tech").attr("checked",$(this).attr("checked"));
	});
	
	$("input.activeTest.allLin").on("click",function(){
		$(".activeTest.lin").attr("checked",$(this).attr("checked"));
	});
	
	$(".buttonTest").on("click",function(){
		if($(this).hasClass("stop")){
			startTest();
		}else if($(this).hasClass("run")){
			forceStopTest();
		}
	});	
});
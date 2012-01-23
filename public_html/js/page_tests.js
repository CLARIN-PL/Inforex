var test_limit = 100; //liczba testowanych jednocześnie dokumentów
var corpus_id = 0;
var documents_in_corpus = 0;

/*
obsługa - proces testowania
*/
function testProcess(from,error_num,test_name){
	if(from < documents_in_corpus){
		$('#' + test_name).find('td.test_process').html(from + '/' + documents_in_corpus + '<br><img class="ajax_indicator" src="gfx/ajax.gif"/>');
		if(error_num > 0)
			$('#' + test_name).find('td.test_result').html(error_num);
		testAjax(from,error_num,test_name);
	}
	else{
		$('#' + test_name).find('td.test_time').removeClass('running');
		$('#' + test_name).find('td.test_process').html(documents_in_corpus + '/' + documents_in_corpus);
		if(error_num > 0){
			$('#' + test_name).find('td.test_result').html(error_num);
			$('#' + test_name).addClass(' wrong');
		}
		else{
			$('#' + test_name).addClass(' corect');
		}
	}
}

/*
obsługa testów - ajax
*/
function testAjax(from,error_num,test_name){
	$.ajax({
			type: 	'POST',
			url: 	"index.php",
			data:	{ 	
						ajax: "tests_integrity",
						name: test_name,
						from: from,
						to: test_limit,
						error_num: error_num,
						corpus_id: corpus_id  
					},						
			success: function(data){
						var html = '';
						for (a in data['data']){
							html += '<tr class="tests_items ' + test_name + '">';
							html += '	<td style="vertical-align: middle">' + data['data'][a]['error_num'] + '</td>';
							html += '	<td style="vertical-align: middle"><a target="_blank" href="index.php?page=report&amp;corpus=' + corpus_id + '&amp;subpage=annotator&amp;id=' + data['data'][a]['report_id'] + '">' + data['data'][a]['report_id'] + '</a></td>';
							html += '	<td style="vertical-align: middle">' + data['data'][a]['wrong_count'] + '</td>';
							html += '	<td style="vertical-align: middle"><a href="#" class="errors">wyświetl szczegóły</a></td>';							
							html += '</tr>';
							if(test_name == 'empty_chunk')
							for (element in data['data'][a]['test_result']){
								if(element < data['data'][a]['test_result'].length-1){
									html += '<tr class="tests_errors ' + test_name + '">';
									html += '	<td colspan="3" class="empty"></td>';
									html += '	<td style="vertical-align: middle">Pusty chunk: znajduje się w linii ' + data['data'][a]['test_result'][element] + '</td>';
									html += '</tr>';
								}
							}
							if(test_name == 'wrong_chunk')
							for (element in data['data'][a]['test_result']){
									html += '<tr class="tests_errors ' + test_name + '">';
									html += '	<td colspan="3" class="empty"></td>';
									html += '	<td style="vertical-align: middle">Line: ' + data['data'][a]['test_result'][element]['line'] + ' Column: ' + data['data'][a]['test_result'][element]['col'] + ' Description: ' + data['data'][a]['test_result'][element]['description'] + '</td>';
									html += '</tr>';
							}
							if(test_name == 'wrong_tokens'){
								for (element in data['data'][a]['test_result']){
									html += '<tr class="tests_errors ' + test_name + '">';
									html += '	<td colspan="3" class="empty"></td>';
									html += '	<td style="vertical-align: middle">Dla tokenu o indeksie ' + data['data'][a]['test_result'][element]['id'] + ' i zakesie [' + data['data'][a]['test_result'][element]['from'] + ', ' + data['data'][a]['test_result'][element]['to'] + '] nie istnieje token będący jego następnikiem</td>';
									html += '</tr>';
								}
							}
							if(test_name == 'tokens_out_of_scale'){
								for (element in data['data'][a]['test_result']){
									html += '<tr class="tests_errors ' + test_name + '">';
									html += '	<td colspan="3" class="empty"></td>';
									html += '	<td style="vertical-align: middle">Token o indeksie ' + data['data'][a]['test_result'][element]['id'] + ' i zakesie [' + data['data'][a]['test_result'][element]['from'] + ', ' + data['data'][a]['test_result'][element]['to'] + '] wykracza poza ramy dokumentu o długości [' + data['data'][a]['test_result'][element]['content_length'] + ']</td>';
									html += '</tr>';
								}
							}
							if(test_name == 'wrong_annotations'){
								for (element in data['data'][a]['test_result']){
									html += '<tr class="tests_errors ' + test_name + '">';
									html += '	<td colspan="3" class="empty"></td>';
									html += '	<td style="vertical-align: middle">Anotacja: <span class="' + data['data'][a]['test_result'][element]['annotation_type'] + '" title="an#' + data['data'][a]['test_result'][element]['annotation_id'] + ':' + data['data'][a]['test_result'][element]['annotation_type'] + '">' + data['data'][a]['test_result'][element]['annotation_text'] + '</span> o zakresie [' + data['data'][a]['test_result'][element]['annotation_from'] + ',' + data['data'][a]['test_result'][element]['annotation_to'] + '] przecina się z tokenem o indeksie ' + data['data'][a]['test_result'][element]['token_id'] + ' i zakesie [' + data['data'][a]['test_result'][element]['token_from'] + ', ' + data['data'][a]['test_result'][element]['token_to'] + ']</td>';
									html += '</tr>';
								}
							}
							if(test_name == 'wrong_annotations_by_annotation'){
								for (element in data['data'][a]['test_result']){
									html += '<tr class="tests_errors ' + test_name + '">';
									html += '	<td colspan="3" class="empty"></td>';
									html += '	<td style="vertical-align: middle"><span class="' + data['data'][a]['test_result'][element]['type1'] + '" title="an#' + data['data'][a]['test_result'][element]['id1'] + ':' + data['data'][a]['test_result'][element]['type1'] + '">' + data['data'][a]['test_result'][element]['text1'] + '</span> <span class="' + data['data'][a]['test_result'][element]['type2'] + '" title="an#' + data['data'][a]['test_result'][element]['id2'] + ':' + data['data'][a]['test_result'][element]['type2'] + '">' + data['data'][a]['test_result'][element]['text2'] + '</span></td>';
									html += '</tr>';
								}
							}
						}
						$('#tests_document_list').find('tbody').append(html);//data['data']['html']);
						testProcess(from + test_limit,data['error_num'],test_name);
					},
			error: function(request, textStatus, errorThrown){	
						dialog_error("<b>HTML result:</b><br/>" + request.responseText);		
					},
			dataType:"json"						
	});
}

// Timer
function timer(count){
	setTimeout( function(){
		timer(++count);
		$(".test_time.running").html(count);
         
    }, 1000);
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
			$(".tests_items").hide();
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
		
	testProcess(0,0,'empty_chunk');
	testProcess(0,0,'wrong_chunk');
	testProcess(0,0,'wrong_tokens');
	testProcess(0,0,'tokens_out_of_scale');
	testProcess(0,0,'wrong_annotations');
	testProcess(0,0,'wrong_annotations_by_annotation');
	timer(0);	
});
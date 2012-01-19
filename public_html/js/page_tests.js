var test_limit = 50; //liczba testowanych jednocześnie dokumentów
var corpus_id = 0;
var documents_in_corpus = 0;



function testProcess(from,error_num,test_name){
	if(from < documents_in_corpus){
		$('#' + test_name).find('td.test_process').html(from + '/' + documents_in_corpus);
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
						$('#tests_document_list').find('tbody').append(data['data']['html']);
						testProcess(from + test_limit,data['error_num'],test_name);
					},
			error: function(request, textStatus, errorThrown){	
						dialog_error("<b>HTML result:</b><br/>" + request.responseText);		
					},
			dataType:"json"						
	});
}

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
		}
	});
	
	/*
	Obsługa tabeli z wynikami testów (po kliknięciu w pole testu wyświetlana jest lista naruszeń dla danego testu)
	*/
	$("tr.tests_items").live({
		click: function(){
			if ($(this).hasClass("showItem"))
				$(this).removeClass("showItem").nextUntil(".tests_items").hide();
			else  
				$(this).addClass("showItem").nextUntil(".tests_items").filter(".tests_errors").show();
		}				
	});
	
	testProcess(0,0,'empty_chunk');
	testProcess(0,0,'wrong_tokens');
	testProcess(0,0,'tokens_out_of_scale');
	testProcess(0,0,'wrong_annotations');
	testProcess(0,0,'wrong_annotations_by_annotation');
	timer(0);	
});
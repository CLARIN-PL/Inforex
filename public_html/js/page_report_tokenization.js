/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	$.each($("#content *"), function(index, value){
		$(value).after('<span style="display:none">&nbsp;</span>');
	});
	
	$("#tokenizeText").click(function(){
		runTokenization();
	});

	$("#documentTokens tr").hover(function(){
		var tokenId = $(this).attr("tokenId");
		$("#col-content span.selected").removeClass("selected");
		$("#col-content #an" + tokenId).addClass("selected");
		console.log(tokenId);
		$("#documentTokens tr span.hoverIcons").hide();
		$(this).find(".hoverIcons").show();

		$("a.tokenDelete").confirmation('hide');
	});

	$("a.tokenDelete").confirmation(
		{   title: 'Delete token?',
			placement: "left",
			popout: true,
			onConfirm: function(){
				var row = $(this).parents("tr");
				var tokenId = row.attr("tokenId");
				console.log(tokenId);
				$("#documentTokens").parents(".panel-body").LoadingOverlay("show");

				var tokenDeleteSuccess = function(data){
					row.addClass("deleted");
					row.find(".icons").html('<a href="">refresh view</a>');
				};

				var tokenDeleteComplete = function(){
					$("#documentTokens").parents(".panel-body").LoadingOverlay("hide");
				};

				doAjax("token_delete", {"token_id": tokenId}, tokenDeleteSuccess, null, tokenDeleteComplete);
			}
		});

	$("a.tokenMergeDown").confirmation(
		{   title: 'Merge with token below?',
			placement: "left",
			popout: true,
			onConfirm: function(){
				var row = $(this).parents("tr");
				var row2 = $(this).parents("tr").next("tr");
				var token1Id = row.attr("tokenId");
				var token2Id = row2.attr("tokenId");

				$("#documentTokens").parents(".panel-body").LoadingOverlay("show");

				var tokenMargeSuccess = function(data){
					let t1text = row.find(".tokenText");
					let t1from = row.find(".tokenFrom");
					let t1to = row.find(".tokenTo");
					let t2text = row2.find(".tokenText");
					t1text.html(t1text.text() + t2text.text());
					t1from.html(data["token"]["from"]);
					t1to.html(data["token"]["to"]);
					row2.remove();
					updateTableNo();
					updateReportContent(data["token"]["report_id"])
				};

				var tokenMargeComplete = function(){
					$("#documentTokens").parents(".panel-body").LoadingOverlay("hide");
				};

				doAjax("token_merge",
					{
							"token_1_id": token1Id,
							"token_2_id": token2Id
				}, tokenMargeSuccess, null, tokenMargeComplete);
			}
		});


	var dialog, form, tokenId, token_text, changedRow;

	token_1_txt = $( "#token_1_txt" );
	token_2_txt = $( "#token_2_txt" );
	allFields = $( [] ).add( token_1_txt ).add( token_2_txt);

	tips = $( ".validateTips" );

	function updateTips( t ) {
		tips
			.text( t )
			.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tips.removeClass( "ui-state-highlight", 1500 );
		}, 500 );
	}

	function checkLength( o, n, min, max ) {
		if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			updateTips( "Length of " + n + " must be between " +
				min + " and " + max + "." );
			return false;
		} else {
			return true;
		}
	}

	function checkCombinedText(t1, t2, org ) {
		if(org.localeCompare(t1.val() + t2.val()) === 0){
			return true
		} else {
			updateTips( "Combined values from input fields must be the same as original " +
				org +" is not equal to "+ t1.val() + t2.val());
			return false
		}
	}

	function SplitTokens() {
		var valid = true;
		allFields.removeClass( "ui-state-error" );

		valid = valid && checkLength( token_1_txt, "Token text", 1, token_text.length );
		valid = valid && checkLength( token_2_txt, "New token text", 1, token_text.length );
		valid = valid && checkCombinedText(token_1_txt, token_2_txt, token_text)
		$("#documentTokens").parents(".panel-body").LoadingOverlay("show");

		if ( valid ) {
			var tokenSplitSuccess = function(data){
				let t1text = changedRow.find(".tokenText");
				let t2row = changedRow.clone(true);

				let t1from = changedRow.find(".tokenFrom");
				let t1to = changedRow.find(".tokenTo");

				let t2id = t2row.find(".tokenId");
				let t2text = t2row.find(".tokenText");
				let t2from = t2row.find(".tokenFrom");
				let t2to = t2row.find(".tokenTo");

				t1text.html(token_1_txt.val());
				t1from.html(data["token1"]["from"]);
				t1to.html(data["token1"]["to"]);


				t2id.html('<small>'+data["token2"]["token_id"]+'</small>')
				t2text.html(token_2_txt.val())
				t2from.html(data["token2"]["from"])
				t2to.html(data["token2"]["to"])
				t2row.attr("tokenId", data["token2"]["token_id"])
				changedRow.after(t2row)
				updateTableNo();
				updateReportContent(data["token1"]["report_id"])
				dialog.dialog( "close" );
			};

			var tokenSplitComplete = function(){
				$("#documentTokens").parents(".panel-body").LoadingOverlay("hide");
			};

			doAjax("token_split", {
				"token_id": tokenId,
				"token_length": token_1_txt.val().length}, tokenSplitSuccess, null, tokenSplitComplete);
		} else {
			$("#documentTokens").parents(".panel-body").LoadingOverlay("hide");
		}
		return valid;
	}

	dialog = $( "#dialog-split-token" ).dialog({
		autoOpen: false,
		width: 400,
		modal: true,
		buttons: {
			"Split token": SplitTokens,
			Cancel: function() {
				dialog.dialog( "close" );
			}
		},
		close: function() {
			form[ 0 ].reset();
			allFields.removeClass( "ui-state-error" );
		}
	});

	form = dialog.find( "form" ).on( "submit", function( event ) {
		event.preventDefault();
		SplitTokens();
	});

	$( "a.tokenSplit" ).on( "click", function() {
		let row = $(this).parents("tr");
		changedRow = row
		tokenId = row.attr("tokenId");
		token_text = row.find(".tokenText").text();
		$( "#lbl_token_txt" ).text('Token('+tokenId+'):');
		$( "#token_1_txt" ).val(token_text);
		dialog.dialog( "open" );
	});

});

function updateReportContent(reportId){

	var reportContentSuccess = function(data){
		console.log(data["content_inline"]);
		$("#rp-content").fadeOut(800, function(){
			$("#rp-content").html(data["content_inline"]).fadeIn().delay(2000);
		});
	};

	var reportContentComplete = function(){
	};

	doAjax("report_content",
		{
			"report_id": reportId
		},
		reportContentSuccess, null, reportContentComplete);
}

function updateTableNo(){
	let i = 1;
	$("#documentTokens tbody tr").each(function() {
		$this = $(this);
		$this.find(".tokenNo").html(i);
		i++;
	});
}

function runTokenization(){
	var button = $("#tokenizeText");

	$(button).after("<img class='ajax_indicator' src='gfx/ajax.gif'/>");
	$(button).attr("disabled", "disabled");
	$("#process_status").show();

	var task = $("#taggers").find("input[name=task]:checked").attr('id');

	var corpus_id = $.url(window.location.href).param("corpus");
	var document_id = $.url(window.location.href).param("id");

	var params = {
		'error': false,
		'task': task,
		'document_id': document_id,
		'url': 'corpus=' + corpus_id
	};

	var success = function(data){
		var interval_id = window.setInterval(function() { fetchTokenizationStatus(data['task_id'], interval_id); }, 1000);
	};

	var complete = function(){
		$(button).removeAttr("disabled");
		$(".ajax_indicator").remove();
	};

	doAjaxSync("task_new", params, success, null, complete);
}

function fetchTokenizationStatus(task_id, interval_id){
	var params = {
		'task_id': task_id
	};

    var success = function(data){
		var processing = data.processed;
		var percent = data.percent;

		var status;
		if(processing === 1 && percent === 0){
			status = "Processing...";
		} else if (processing === 1 && percent === 100){
			status = "<a href=''><i class=\"fa fa-refresh\" aria-hidden=\"true\"></i> Refresh</a> the page.";
            clearInterval(interval_id);
		} else{
			status = "Queued";
		}

		$("#status").html(status);
    };

	doAjax('task_check_status', params, success);
}
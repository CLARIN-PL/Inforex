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
					//updateTokensTable(data.tokens)
					row.addClass("deleted");
					row.find(".icons").html('<a href="">refresh view</a>');
				};

				var tokenDeleteComplete = function(){
					$("#documentTokens").parents(".panel-body").LoadingOverlay("hide");
				};

				doAjax("token_delete", {"token_id": tokenId}, tokenDeleteSuccess, null, tokenDeleteComplete);
			}
		});

	var dialog, form, tokenId;

	token_1_txt = $( "#token_1_txt" );
	token_1_orth = $( "#token_1_orth" );
	token_2_txt = $( "#token_2_txt" );
	token_2_orth = $( "#token_2_orth" );
	allFields = $( [] ).add( token_1_txt ).add( token_1_orth ).add( token_2_txt).add(token_2_orth);
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

	function SplitTokens() {
		var valid = true;
		allFields.removeClass( "ui-state-error" );

		valid = valid && checkLength( token_1_txt, "Token text", 1, 80 );
		valid = valid && checkLength( token_2_txt, "New token text", 1, 80 );

		if ( valid ) {
			var tokenSplitSuccess = function(data){
				dialog.dialog( "close" );
			};

			var tokenSplitComplete = function(){
				$("#documentTokens").parents(".panel-body").LoadingOverlay("hide");
			};

			doAjax("token_split", {
				"token_id": tokenId,
				"token_text": token_1_txt.val(),
				"token_orth": token_1_orth.val(),
				"new_token_text": token_2_txt.val(),
				"new_token_orth": token_2_orth.val()}, tokenSplitSuccess, null, tokenSplitComplete);

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
		tokenId = row.attr("tokenId");
		$( "#lbl_token_txt" ).text('Token('+tokenId+'):');
		$( "#token_1_txt" ).val(row.find(".tokenText").text());
		$( "#token_1_orth" ).val(row.find(".tokenOrth").text());
		dialog.dialog( "open" );
	});

});

function updateTokensTable(tokens){
	var rows = $("#documentTokens tbody tr").toArray();
	for (var i = 0; i < rows.length && tokens.length; i++) {
		var row = rows[i];
		console.log(tokens[i]);
	}
	console.log(rows);
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
		if(processing == 1 && percent == 0){
			status = "Processing...";
		} else if (processing == 1 && percent == 100){
			status = "<a href=''><i class=\"fa fa-refresh\" aria-hidden=\"true\"></i> Refresh</a> the page.";
            clearInterval(interval_id);
		} else{
			status = "Queued";
		}

		$("#status").html(status);
    };

	doAjax('task_check_status', params, success);
}
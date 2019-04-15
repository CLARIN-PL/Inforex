/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	assignButtonAutoAnnotateClick();

	assignRadioClick();
	assignSelectChange();
	assignAnnotationHighlight();
});

function assignButtonAutoAnnotateClick(){
	var buttonAutoannotate = $("#buttonAutoannotate");
	buttonAutoannotate.click(function(){
		buttonAutoannotate.startAjax();
		$(".info-refresh").hide();
		$(".info-notfound").hide();

		var params = {
			documentId : getUrlParameter("id"),
			annotationSetId : $("input[name=annotation_set_id]").val()
		};

		var success = function(data){
			if (data.length > 0 ) {
				$(".info-refresh").show();
			} else {
				$(".info-notfound").show();
			}
		};

		var complete = function(){
			buttonAutoannotate.stopAjax();
			autoreizeFitToScreen();
		}

		doAjax("report_autoannotate", params, success, null, complete);

		return false;
	});
};

function assignRadioClick(){
	$("#annotationList td.decision").click(function(){
		$(this).find("input").prop('checked', true);
		updateSaveButtonStatus();
	});
	/** Resetuje listę wyboru relacji, na którą ma być zmieniona anotacja */
	$("input[type=radio]").click(function(){
		$(this).closest("tr").find("select").val("-");
		updateSaveButtonStatus();
	});
};

function assignSelectChange(){
	/** Resetuje radio butony wyboru przy ustawieniu typu relacji */
	$("select").change(function(){
		if ( $(this).closest("tr").find("input:checked").val() == "change" ) {
			$(this).closest("tr").find("input[value=later]").attr("checked", "checked");
		} else {
			$(this).closest("tr").find("input[value=change]").attr("checked", "checked");
		}
		updateSaveButtonStatus();
	});
};

function assignAnnotationHighlight(){
	$("tr.annotation").hover(function(){
		var annotationId = $(this).attr("annotation_id");
		$(".contentBox span.hightlighted").removeClass("hightlighted");
		$(".contentBox span#an" + annotationId).addClass("hightlighted");

		$("tr.annotation.selected").removeClass("selected");
		$(this).addClass("selected");
	});
};

function updateSaveButtonStatus(){
	var decisions = $("input[value=later]").length;
	var later = $("input[value=later]:checked").length;
	var btn = $("#buttonSave");
	if ( later == decisions ){
		btn.removeClass("btn-danger");
		btn.addClass("btn-default");
	} else {
		btn.removeClass("btn-default");
		btn.addClass("btn-danger");

	}
};
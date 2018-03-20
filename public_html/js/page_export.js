/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var url = $.url(window.location.href);
var corpus_id = url.param("corpus");
var ongoing_exports;

$(document).ready(function(){
    handleExportProgress();
    setupRelationTree();

    $("#export_message_body").on('click', '.error_details_btn', function(){
        var parent_tr = $(this).closest('tr');

        //Toggle the rows with error details
        $(parent_tr).nextAll().each(function(){
            if($(this).attr('class') !== 'error_desc_row'){
                if($(parent_tr).hasClass('toggled')){
                    $(this).hide();
                } else{
                    $(this).show();
                }
            } else{
                return false;
            }
        });

        if($(parent_tr).hasClass('toggled')){
            $(parent_tr).removeClass('toggled');
        } else{
            $(parent_tr).addClass('toggled');
        }
    });

    $("#history").on('click', '.export_stats_button', function(){
        $("#export_stats_body").html('<div class="loader"></div>');
        $("#export_stats_modal").modal('show');


        var export_id = $(this).attr('id');
        var success = function (data) {
            var table_html = generateStatsTable(data);
            $("#export_stats_body").html(table_html);
        };
        var data = {
            'export_id': export_id
        };

        doAjaxSync("export_get_stats", data, success);
    });

    $("#history").on('click', '.export_message_button', function(){
        $("#export_message_body").html('<div class="loader"></div>');
        $("#export_message_modal").modal('show');

        var export_id = $(this).attr('id');
        var success = function (data) {
            var table_html = generateErrorTable(data);
            $("#export_message_body").html(table_html);
        };
        var data = {
            'export_id': export_id
        };

        doAjaxSync("export_get_errors", data, success);
    });

    $(".table").on('change', '.select_mode', function(){
        if($(this).val() === "standard"){
            $(this).parent().find('.element_user').hide();
            $(this).parent().find('.elements').show();
        } else{
            $(this).parent().find('.elements').hide();
            $(this).parent().find('.element_user').show();
        }
    });

	
	$(".table").on("click", "img",function(){
		$(this).toggleClass("selected");
	});
	
	$(".table").on("click", ".close", function(){
		$(this).parent().remove();
	});
	
	$(".new_selector").click(function(){
		var form = $(".flag_template").html();		
		$("td.flags").append(form);		
	});

    $(".new_index").click(function(){
        var form = $(".index_template").html();
        $("td.indices").append(form);
    });

	$(".new_extractor").click(function(){
		var form = $(".extractor_template").html();
        annotationTypeTreeInitTriggers($(form).appendTo("td.extractors"));
	});
	
	$("#newExportButton").click(function(){
		$("#newExportForm").modal('show');
	});

	$("#cancel").click(function(){
		$("#newExportButton").show();
		$("#newExportForm").hide();
		$("#history").show();
	});

	$("#export").click(function(){
		$(".instant_error").remove();
		
		var description = $("textarea[name=description]").val().trim();
		if ( description.length == 0 ){
			$("textarea[name=description]").after(get_instante_error_box("Enter description of the export"));
		}
		
		var selectors = collect_selectors();		
		var extractors = collect_extractors();
		var indices = collect_indices();
		var taggingMethod = get_tagging_method();

		if ( $(".instant_error").size() > 0 ){
			$(".buttons").append(get_instante_error_box("There were some errors. Please correct them first before submitting the form."))
		}
		else{
			submit_new_export(description, selectors, extractors, indices, taggingMethod);
		}
	});

	var $morphoUserSelect = $('select[name="morpho-user"]');
    $('select[name="select-tagging"]').change(function(){
        $morphoUserSelect.toggle($(this).val() === 'user');
    })
});


function generateErrorTable(data){
    var table_html = '<table class="table table-striped">'+
        '<thead>'+
            '<tr>' +
                '<th>Error</th>' +
                '<th>Count</th>' +
                '<th>Details</th>' +
            '</tr>' +
        '</thead>' +
        '<tbody>';
    var i = 0;
    for(i; i < data.length; i++){
        var row = data[i];
        table_html += '<tr class = "error_desc_row">' +
                        '<td class = "col-md-8">'+row.message+'</td>' +
                        '<td class = "col-md-2">'+row.count+'</td>' +
                        '<td class = "col-md-2">' +
                            '<button class = "btn btn-primary error_details_btn">Details</button>' +
                        '</td>';
        for(var index in row.error_details){
            var stat = row.error_details[index];
            table_html += '<tr style = "background: #ff000024; display: none;">' +
                '<td colspan = "1"><strong>' +
                    index + '</strong>: ' +
                '<td colspan = "2">';
                for(var key in stat){
                    table_html += key + ', ';
                }
                table_html += '</td>';

            table_html +='</td>';
            table_html += '</tr>';

        }


    }
    table_html += '</tbody></table>';

    return table_html;
}

function handleExportProgress(){
    getCurrentExports();
    updateQueue();
    var intervalID = window.setInterval(fetchExportStatus, 1000);

}

function updateQueue(){
    var queued_exports = ongoing_exports.scheduled_exports;
    var pos = 0;
    for (var key in queued_exports) {
        $("#export_status_"+key).html("queued - " + (pos+1) + " pos");
        pos++;
    }
}

function generateStatsTable(data){
    var table_html = '<table class="table table-striped">'+
        '<thead>'+
        '<tr><th></th>';
    var first_key = Object.keys(data)[0];
    for(var key in data[first_key]){
        table_html += '<th class = "text-center">'+key+'</th>';
    }
    table_html += '</tr></thead>';
    table_html += '<tbody>';

    for(var index in data){
        var stat = data[index];
        table_html += '<tr><td>'+index+'</td>';
        for(var ind in stat){
            table_html += '<td class = "text-center">'+stat[ind]+'</td>';
        }
        table_html += '</tr>';

    }
    table_html += '</tbody></table>';

    return table_html;
}

function fetchExportStatus(){
    var success = function (data) {
        data.forEach(function(value){
            var export_id = value.export_id;
            var progress = value.progress;
            var progress_bar = '<div class="progress">'+
                '<div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="'+progress+'"'+
                'aria-valuemin="0" aria-valuemax="100" style="text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black; width:'+progress+'%">'+progress+'%'+
                '</div>'+
                '</div>';

            $("#export_status_"+export_id).html(progress_bar);
            if(progress === "100"){
                $("#export_status_"+export_id).html("Preparing...");
            }
            if(value.status === "done"){
                $("#export_status_"+export_id).html("done");
                var download_button_html = '<a href="index.php?page=export_download&amp;export_id='+export_id+'">'+
                                     '<button class="btn btn-primary">Download</button>'
                                  '</a>';
                $("#export_download_"+export_id).html(download_button_html);

                if(value.statistics !==  null){
                    var stats_button_html = '<button class="btn btn-primary export_stats_button" id = "'+export_id+'" >Statistics</button>';
                } else{
                    var stats_button_html = '<i>not available</i>';
                }

                if(value.error_count > 0){
                    var error_button_html = '<button class="btn btn-warning export_message_button" id = "'+export_id+'" >Contains errors</button>';
                } else{
                    var error_button_html = '-';
                }

                $("#export_message_"+export_id).html(error_button_html);
                $("#export_stats_"+export_id).html(stats_button_html);


                handleExportProgress();
            } else{
                //Add export_id to the set of current exports if it is not there.
                //Remove from scheduled exports.
                if(ongoing_exports.current_exports[export_id] !== 1){
                    ongoing_exports.current_exports[export_id] = 1;
                    delete ongoing_exports.scheduled_exports[export_id];
                    updateQueue();
                }
            }
        });
    };
    var data = {
        'corpus_id':corpus_id,
        'current_exports': ongoing_exports.current_exports
    };
    doAjax("export_get_export_status", data, success);
}

function getCurrentExports(){
    var success = function (data) {
        ongoing_exports = data;
    };
    var data = {
        'corpus_id': corpus_id
    };

    doAjaxSync("export_get_active_exports", data, success);
}

/**
 * 
 * @param description
 * @param selectors
 * @param extractors
 * @param indices
 * @param taggingMethod
 * @returns
 */
function submit_new_export(description, selectors, extractors, indices, taggingMethod){
	var params = {};	
	params['url'] = $.url(window.location.href).attr("query");
	params['description'] = description;
	params['selectors'] = selectors;
	params['extractors'] = extractors;
	params['indices'] = indices;
	params['tagging'] = taggingMethod;

	// console.log(params);
	// return;

	doAjaxWithLogin("export_new", params, function(){
		window.location.reload(true);
	}, function(){
		submit_new_export(description, selectors, extractors, indices, taggingMethod);
	});
	
}

/**
 * Zbiera opis zdefiniowanych selectorów.
 * @returns
 */
function collect_selectors(){
	var selectors = "";
	$("td.flags div.flags").each(function(){		
		selectors += (selectors.length > 0 ? "\n" : "") + "corpus_id=" + corpus_id + "&flag:" + parse_flag(this);
	});
	if ( selectors.length == 0){
		selectors = "corpus_id=" + corpus_id;
	}
	return selectors;
}

function getStandardExtractors(element){
    var elements = "";
    $(element).find("div.elements .annotation_layers_and_subsets input.group_cb:checked").each(function(){
        elements += (elements.length>0?"&":"") + "annotation_set_id=" + $(this).val();
    });
    $(element).find("div.elements .annotation_layers_and_subsets input.lemma_group_cb:checked").each(function(){
        elements += (elements.length>0?"&":"") + "lemma_annotation_set_id=" + $(this).val();
    });
    $(element).find("div.elements .annotation_layers_and_subsets input.attribute_group_cb:checked").each(function(){
        elements += (elements.length>0?"&":"") + "attributes_annotation_set_id=" + $(this).val();
    });
    $(element).find("div.elements .relation_tree input.relation_group_cb:checked").each(function(){
        elements += (elements.length>0?"&":"") + "relation_set_id=" + $(this).val();
    });
    $(element).find("div.elements .annotation_layers_and_subsets input.subset_cb:checked").each(function(){
        elements += (elements.length>0?"&":"") + "annotation_subset_id=" + $(this).val();
    });
    $(element).find("div.elements .annotation_layers_and_subsets input.lemma_subset_cb:checked").each(function(){
        elements += (elements.length>0?"&":"") + "lemma_annotation_subset_id=" + $(this).val();
    });
    $(element).find("div.elements .annotation_layers_and_subsets input.attribute_subset_cb:checked").each(function(){
        elements += (elements.length>0?"&":"") + "attributes_annotation_subset_id=" + $(this).val();
    });

    return elements;
}

//mencat_d=3:annotations=annotation_set_ids#1,9;user_ids#65
function getCustomExtractors(element){
    var annotation_sets = "";
    var annotation_subsets = "";
    var user_ids = "";
    var stage = $(".annotation_stage_select").val();
    $(element).find("div.element_user .annotation_layers_and_subsets input.user_group_cb:checked").each(function(){
        annotation_sets += (annotation_sets.length>0?",":"") + "" + $(this).val();
    });
    $(element).find("div.element_user .annotation_layers_and_subsets input.user_subset_cb:checked").each(function(){
        annotation_subsets += (annotation_subsets.length>0?",":"") + "" + $(this).val();
    });
    $(element).find("div.element_user .export_users input.user_checkbox:checked").each(function(){
        user_ids += (user_ids.length>0?",":"") + "" + $(this).val();
    });

    var elements = (annotation_sets.length > 0 ? ("annotation_set_ids#"+annotation_sets) : "");
    if(elements.substr(elements.length - 1) !== ";") elements += ";";
    elements += (annotation_subsets.length > 0 ? (elements.length > 0)("annotation_subset_ids#"+annotation_subsets) : "");
    if(elements.substr(elements.length - 1) !== ";") elements += ";";
    elements += (user_ids.length > 0 ? ("user_ids#"+user_ids) : "");
    if(elements.substr(elements.length - 1) !== ";") elements += ";";
    elements += "stages#" + stage;

    if(elements.length > 0 && user_ids.length > 0 && (annotation_sets.length > 0 || annotation_subsets.length >0)){
        return "annotations=" + elements;

    } else{
        return "";
    }
}

/**
 * Zbiera opisy zdefiniowanych ekstraktorów treści.
 * @returns
 */
function collect_extractors(){
    var extractors = "";
    $("td.extractors div.extractor").each(function(){
        var flag = parse_flag($(this).find("div.flags"));
        var elements;
        if($(".select_mode").val() === "standard"){
            elements = getStandardExtractors(this);
        } else{
            elements = getCustomExtractors(this);
        }

        if ( elements.length === 0 ){
            $(this).append(get_instante_error_box("No elements to expert were defined"));
        }
        else{
            extractors += (extractors.length > 0 ? "\n" : "") + flag + ":" + elements;
        }
    });
    return extractors;
}


function get_tagging_method(){
    var taggingMethod = $('select[name="select-tagging"]').val();

    if(taggingMethod === 'user'){
        taggingMethod += ':' + $('select[name="morpho-user"]').val();
    }
    return taggingMethod;
}

/**
 * Zbiera opisy zdefiniowanych indeksów.
 * @returns
 */
function collect_indices(){
    var indices = "";
    $("td.indices div.index").each(function(){
        var flag = parse_flag($(this).find("div.flags"));
        var index = $(this).find(".index_file").val();

        if ( index === "" ){
            $(this).append(get_instante_error_box("No index defined."));
        }
        else{
            indices += (indices.length > 0 ? "\n" : "") + "index_" + index + ".list:" + flag;
        }
    });
    return indices;
}

/**
 * Parsuje obiekt reprezentujący formularz opisujący wartości wybranej flagi
 * @param flag -- obiekt dom 
 */
function parse_flag(flag){
	if ($(flag).find("select[name=corpus_flag_id] option:selected").val() == ""){
		$(flag).append(get_instante_error_box("Flag name not set"));
	}
	var name = $(flag).find("select[name=corpus_flag_id] option:selected").text();		
	var values = "";
	$(flag).find("img.selected").each(function(){
		values += (values.length > 0 ? "," : "") + $(this).attr("value");
	});
	if ( values == "" ){
		$(flag).append(get_instante_error_box("Flag value(s) not set"));		
	}
	return name + "=" + values;
}

/**
 * 
 * @param msg
 * @returns
 */
function get_instante_error_box(msg){
	return "<div class='error instant_error ui-corner-all ui-state-error'>" + msg +"</div>"
}
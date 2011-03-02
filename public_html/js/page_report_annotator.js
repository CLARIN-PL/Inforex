var isCtrl = false; 
var _wAnnotation = null;
var _oNavigator = null;
//czy tryb dodawania relacji?

var AnnotationRelation = Object();
AnnotationRelation.relationMode = false;
AnnotationRelation.types = []; //array of available types
AnnotationRelation.target_type = {}; //target_type.relation_type=[X,Y,...] existing relations relation_type between source_id and target_id=X,Y,..

//ta funkcja moze byc uzyta dla wszystkich ajaxow, potem najwyzej sie dorobi obsluge faliureHandler'a (obecnie cancel_relation)
function ajaxErrorHandler(data, successHandler, errorHandler){
	if (data['error']){
		if (data['error_code']=="ERROR_AUTHORIZATION"){
				loginForm(false, function(success){ 
					if (success){						
						if (errorHandler && $.isFunction(errorHandler)){
							errorHandler();
						}
					}else{
						//alert('Wystąpił problem z autoryzacją. Zmiany nie zostały zapisane.');
						cancel_relation(); 
					}
				});				
		}
		else {
			alert('nieznany blad!');
		}
	} 
	else {
		if (successHandler && $.isFunction(successHandler)){
			successHandler();
		}		
	}
} 

//annotation_clicked_by_label -> source  

/**
 * Przypisanie akcji po wczytaniu się strony.
 */
$(document).ready(function(){
	$("a.an").click(function(){
		selection = new Selection();
		if ( !selection.isValid )
		{
			alert("Zaznacz tekst");
			return false;
		}
		add_annotation(selection, $(this).attr("value"));		
		return false;
	});
	
	
	//---------------------------------------------------------
	//Obsługa relacji
	//---------------------------------------------------------
	$("#relation_add").click(function(){
		add_relation_init();
	});

	$("#relation_cancel").click(function(){	
		cancel_relation();
		get_relations();
	});
	
	$("#relation_type").change(function(){
		block_existing_relations();
	});
	
	$("#relation_table span").live('mouseover',function(){
		$("#"+$(this).attr('title').split(":")[0].replace("#","")).addClass("hightlighted");
	}).live('mouseout',function(){
		$("#"+$(this).attr('title').split(":")[0].replace("#","")).removeClass("hightlighted");
	});
	
	$("div.deleteRelation").live('click',function(){
		delete_relation(this);
	});
	
	get_all_relations();
});

function block_existing_relations(){
	$annotations = $("#content span");
	$annotations.addClass("relationGrey");//.css("cursor","default");
	$.each(AnnotationRelation.types,function(index, value){
		$annotations.filter("."+value).removeClass("relationGrey").addClass("relationAvailable");//.css("cursor","crosshair");
	});	
	selectedType = $("#relation_type").children(":selected:first").val();
	if (AnnotationRelation.target_type[selectedType]){
		$.each(AnnotationRelation.target_type[selectedType], function(index, value){
			$annotations.filter("#an"+value).addClass("relationGrey").removeClass("relationAvailable");//.css("cursor","default");
		});
	}
}

function get_relations(){
	if (_wAnnotation && _wAnnotation._annotation){
		sourceObj = _wAnnotation._annotation;
		AnnotationRelation.target_type = {};
		jQuery.ajax({
			async : false,
			url : "index.php",
			dataType : "json",
			type : "post",
			data : { 
				ajax : "report_get_annotation_relations", 
				annotation_id : sourceObj.id
			},				
			success : function(data){
				ajaxErrorHandler(data,
					function(){ 
						$("#relation_table > tbody tr").remove();
						$table = $("#relation_table");
						
						$("#content span").addClass("relationGrey");
						$.each(data, function(index, value){
							$('<tr>'+
									'<td>'+value.name+'</td>'+
									'<td><span class="'+value.type+'" title="an#'+value.target_id+':'+value.type+'">'+value.text+'</span></td>'+
									'<td><div id="relation'+value.id+'"  class="deleteRelation"><b>X</b></div></td>'+
							  '</tr>').appendTo($table);
							if (AnnotationRelation.target_type[value.name]){
								AnnotationRelation.target_type[value.name].push(value.target_id);
							}
							else {
								AnnotationRelation.target_type[value.name] = [];
								AnnotationRelation.target_type[value.name].push(value.target_id);
							}
							$("#an"+value.target_id).removeClass("relationGrey");
						});
						get_all_relations();
					}, 
					function(){
						get_relations();
					}
				);
			}
		});		
	}
}

function get_all_relations(){
	jQuery.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : { 
			ajax : "report_get_relations", 
			report_id : $("#report_id").val()
		},				
		success : function(data){
			//ajaxErrorHandler(data,
				//function(){ 
					$("#content span").removeClass("unit_source unit_target");
					$.each(data, function(index, value){
						$("#an"+value.source_id).addClass("unit_source");
						$("#an"+value.target_id).addClass("unit_target");
					});
				//}, 
				//function(){
					//get_all_relations();
				//}
			//);
		}
	});		
	

}


function add_relation_init(){
	AnnotationRelation.types = [];
	jQuery.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : { ajax : "report_get_annotation_relation_types", annotation_id : _wAnnotation._annotation.id },				
		success : function(data){
			ajaxErrorHandler(data,
				function(){
					AnnotationRelation.relationMode = true; //global variable in page_report_annotator.js
					$("#relation_add").hide();
					$("#relation_select").show();
					$listContainer = $("#relation_type").empty();//.append('<option style="display:none"></option>');
					$.each(data, function(index, value){
						$('<option value="'+value.name+'">'+value.name+'</option>').data(value).appendTo($listContainer);
					});
					jQuery.ajax({
						async : false,
						url : "index.php",
						dataType : "json",
						type : "post",
						data : { ajax : "report_get_annotation_types", annotation_id : _wAnnotation._annotation.id },				
						success : function(data2){
							$.each(data2,function(index, value){
								AnnotationRelation.types.push(value[0]);
							});
							block_existing_relations();
						}
					});	
				},
				function(){
					add_relation_init();
				}
			);
		}
	});
}

function add_relation(spanObj){
	sourceObj = _wAnnotation._annotation;
	targetObj = new Annotation(spanObj);
	relationTypeId = $("#relation_type").children(":selected:first").data('id');
	jQuery.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : { 
			ajax : "report_add_annotation_relation", 
			source_id : sourceObj.id,
			target_id : targetObj.id,
			relation_type_id : relationTypeId
		},				
		success : function(data){
			ajaxErrorHandler(data,
				function(){
					cancel_relation();
					get_relations();
				},
				function(){
					add_relation(spanObj);
				}
			);
		}
	});			
}
/*
 * 			$(this).attr("id").replace("relation",""), 
			$(this).offset().left-$(window).scrollLeft(),
			$(this).offset().top - $(window).scrollTop());

 */
function delete_relation(deleteHandler){
	relationId = $(deleteHandler).attr("id").replace("relation","");
	xPosition = $(deleteHandler).offset().left-$(window).scrollLeft();
	yPosition = $(deleteHandler).offset().top - $(window).scrollTop();
	
	
	$relation = $("#relation"+relationId);
	relationName = $relation.parent().prev().prev().text();
	$relationSrc = $("#content span.selected:first");
	$relationSrcTxt = $('<span class="'+$relationSrc.attr('title').split(":")[1]+'">'+$relationSrc.text()+'</span>');
	$relationDstTxt = $($relation.parent().prev().html()).removeAttr('title');
	//"Czy na pewno usunąć relację 'xxxx' pomiędzy 'aaaa' i 'bbb'?"
	//log(relationDstTxt);
	//log(relationName);
	$dialogBox = 
		$('<div class="deleteDialog annotations">Czy usunąć relację "'+relationName+'" pomiędzy <br/></div>')
		.append($relationSrcTxt)
		.append("<br/>oraz<br/>")
		.append($relationDstTxt)
		.dialog({
			modal : true,
			title : 'Potwierdzenie usunięcia',
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					jQuery.ajax({
						async : false,
						url : "index.php",
						dataType : "json",
						type : "post",
						data : { 
							ajax : "report_delete_annotation_relation", 
							relation_id : relationId
						},				
						success : function(data){
							ajaxErrorHandler(data,
								function(){							
									cancel_relation();
									get_relations();
									$dialogBox.dialog("close");
								},
								function(){
									$dialogBox.dialog("close");
									delete_relation(deleteHandler);
								}
							);								
						}
					});	
				
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}

		});
		$dialogBox.dialog("option", "position",[xPosition- $dialogBox.width(), yPosition]);
	
	/*jQuery.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : { 
			ajax : "report_delete_annotation_relation", 
			relation_id : relationId
		},				
		success : function(data){
			cancel_relation();
			get_relations();
		}
	});	*/
}

function cancel_relation(){
	$("#relation_table > tbody tr").remove();	
	$("#relation_add").show();
	$("#relation_select").hide();
	AnnotationRelation.relationMode = false;
	$("#content span").removeClass("relationGrey relationAvailable");
	$dialogObj = $(".deleteDialog");
	if ($dialogObj.length>0){
		$dialogObj.dialog("destroy").remove();
	}
	get_all_relations();
	
}



/*
 * Zmiana aktualnie zaznaczonej adnotacji po kliknięciu na dowolną adnotację (element span).
 */
function blockInsertion(info){
	$(".an").attr("disabled", "true");
	$("#block_reason").text(info);
	$("#block_message").show();
	$("#block_message_info").hide();
}
function unblockInsertion(){
	$(".an").removeAttr("disabled");
	$("#block_message").hide();
	$("#block_message_info").show();
}

/**
 * Obsługa kliknięcia w anotację.
 */
var annotation_clicked_by_label = null;


$("#content span").live("click", function(){
	if (annotation_clicked_by_label != null)
	{
		//alert("00");
		//czy to sie nigdy nie wykona?
		if (_wAnnotation.get() == annotation_clicked_by_label)		
			set_current_annotation(null);
		
		else 
			set_current_annotation(annotation_clicked_by_label);
		
		annotation_clicked_by_label = null;
	}
	else if ( getSelText() == "")
	{
		if (!AnnotationRelation.relationMode){
			if (_wAnnotation.get() == this){
				set_current_annotation(null);
				cancel_relation();
			}
			else {
				set_current_annotation(this);
				get_relations();
			}
		}
		else if (_wAnnotation.get() != this && !$(this).hasClass("relationGrey")) {
			add_relation(this);
		}
	}
	return false;
});

$("#content .annotation_label").live("click", function(){
	annotation_clicked_by_label = $("span[title='"+$(this).attr("title")+"']");
});




//--------------------
//Ustaw aktywną anotację
//---------------------------------------------------------
/**
 * Ustaw anotację do edycji.
 * @param annotation referencja na znacznik SPAN reprezentujący anotację.
 */
function set_current_annotation(annotation){
	$("#content span.selected").removeClass("selected");
	var context = $("#content .context");
	context.removeClass("context");
	if ( context.attr("class") == "" ) context.removeAttr("class");
	
	_wAnnotation.set(annotation);	
	if ( annotation == null ){
		$("#cell_annotation_edit").hide();
		$("#cell_annotation_add").show();
	}else{
		$("#cell_annotation_add").hide();		
		$("#cell_annotation_edit").show();
	}
}

/**
 * Zdarzenia tabeli z adnotacjami.
 */
$(".an_row").live("click", function(){
	var id = $(this).attr("label");
	$("#"+id).click();
});

/**
 * Ustawienie funkcji szybkiego dodawania anotacji.
 * @return
 */
function setup_quick_annotation_add(){
	var default_annotation = $.cookie("default_annotation");

	if (default_annotation != null){
		$(".annotation_list input[value='"+default_annotation+"']").attr('checked', true);
		$(".annotation_list input[value='"+default_annotation+"']").next().addClass("hightlighted");
		$("#quick_add_cancel").show();
	}
	
	$("#quick_add_cancel").click(function(){
		$("#default_annotation_zero").attr('checked', true);
		$("input:default_annotation ~ span").removeClass("hightlighted");
		$.cookie("default_annotation", "");
		$(this).hide();
		return false;
	});
//	$("input:default_annotation").click(function(){
//		$("input:default_annotation ~ span").removeClass("hightlighted");
//		$(this).next().addClass("hightlighted");
//		$("#quick_add_cancel").show();
//		$.cookie("default_annotation", $("input[name='default_annotation']:checked").val(), {});
//	});	
	$("#content").mouseup(function(){
		if ( _wAnnotation.get() == null ){
			var quick_annotation = $("input[name='default_annotation']:checked").val();
			if (quick_annotation){
				selection = new Selection();
				if ( selection.isValid )
					add_annotation(selection, quick_annotation);
			}
		}
	});
}


//---------------------------------------------------------
// Po załadowaniu strony
//---------------------------------------------------------
$(document).ready(function(){
	$("#annotations").tablesorter(); 

	$(".autogrow").autogrow();
	
	_wAnnotation = new WidgetAnnotation();
	
	//_oNavigator = new Navigator($("#content"));
	setup_quick_annotation_add();
});

//---------------------------------------------------------
$(function(){
	isCtrl = false;
	isShift = false;
	
	$(document)
		.keyup(function (e) { 
			if(e.which == 17) 
				isCtrl=false; 
			if(e.which == 16) 
				isShift=false; 
		})
		.keydown(function (e) { 
			if(e.which == 17) 
				isCtrl=true; 
			if(e.which == 16){ 
				isShift=true; 
			}
			if(e.which == 83 && isCtrl == true) { 
				//run code for CTRL+S -- ie, save! return false; 
			}
			if(e.which == 37 && isCtrl == true && $("#article_prev")){
				//window.location = $("#article_prev").attr("href");			
			} 
			if(e.which == 39 && isCtrl == true && $("#article_next")){
				//window.location = $("#article_next").attr("href");
			}
			if (e.which == 39){
				//_oNavigator.moveRight();
			}
			if ( _wAnnotation != null ){
				_wAnnotation.keyDown(e, isCtrl, isShift)
			}
			if(isCtrl && isShift){ 
				return false;
			}
		});
});

$(document).ready(function(){
	var input = $("#report_type");
	if (input){
		input.change(function(){
			var report_id = $("#report_id").val();
			var report_type = $("#report_type").val();
			$("#report_type").after("<img src='gfx/ajax.gif'/>");
			$.post("index.php", { ajax: "report_set_type", id: report_id, type: report_type },
			  function(data){
				$("#report_type + img").remove();
				$("#report_type").after("<span class='ajax_success'>zapisano</span>");
				$("#report_type + span").fadeOut("1000", function(){$("#report_type + span").remove();});
				console_add("zmieniono typ raportu na <b>"+data['type_name']+"</b>");
			  }, "json");			
		});
	}
});

// Dodaj anotację wskazanego typu
function add_annotation(selection, type){
	selection.trim();
	selection.fit();

	if (!selection.isSimple){
		alert("Błąd ciągłości adnotacji.\n\nMożliwe przyczyny:\n 1) Zaznaczona adnotacja nie tworzy ciągłego tekstu w ramach jednego elementu.\n 2) Adnotacja jest zagnieżdżona w innej adnotacji.\n 3)Adnotacja zawiera wewnętrzne adnotacje.");
		return false;
	}

	sel = selection.sel;

	var report_id = $("#report_id").val();
	
	var newNode = document.createElement("xyz");
	sel.surroundContents(newNode);
			
	var content_no_html = content_no_html = $.trim($("#content").html());

	content_no_html = content_no_html.replace(/<xyz>(.*?)<\/xyz>/, fromDelimiter+"$1"+toDelimiter);
	content_no_html = html2txt(content_no_html);

	// Pobierz treść anotacji przed usunięciem białych znaków
	var from = content_no_html.indexOf(fromDelimiter) + fromDelimiter.length;
	var to = content_no_html.indexOf(toDelimiter);
	var text = content_no_html.substring(from, to);

	// Oblicz właściwe indeksy
	content_no_html = content_no_html.replace(/\s/g, '');
	from = content_no_html.indexOf(fromDelimiter);
	to = content_no_html.indexOf(toDelimiter) - fromDelimiter.length - 1;
	
	status_processing("dodawanie anotacji ...");
	
	if (from < 0 || to < 0 ){
		status_fade();
		dialog_error("Wystąpił błąd z odczytem granic anotacji. Odczytano ["+from+","+to+"]. <br/><br/>Zgłoś błąd administratorowi.");
		return;
	}
	
	$.ajax({
		type: 	'POST',
		url: 	"index.php",
		data:	{ 	
					ajax: "report_add_annotation", 
					report_id: report_id, 
					from: from,
					to: to,
					text: text,
					type: type
				},
		success:function(data){
					$("#content xyz").wrapInner("<span id='new'/>");
					$("#content xyz").replaceWith( $("#content xyz").contents() );
				
					if (data['success']){
						var annotation_id = data['annotation_id'];
						var node = $("#content span#new");
						var title = "an#"+annotation_id+":"+type;
						node.attr('title', title);
						node.attr('id', "an"+annotation_id);
						node.attr('class', type);
						console_add("anotacja <b> "+title+" </b> została dodana do tekstu <i>"+text+"</i>");
						recreate_labels(node);
					}else{
					    dialog_error(data['error']);
					    $("span#new").after($("span#new").html());
					    $("span#new").remove();
					}			
					//$("input.an").removeAttr("disabled"); // Odblokuj przyciski
					status_fade();
				},
		error: function(request, textStatus, errorThrown){
				  dialog_error(request['responseText']);
				  status_fade();
				},
		dataType:"json"
	});	
}


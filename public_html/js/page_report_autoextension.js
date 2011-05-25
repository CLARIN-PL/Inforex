var isCtrl = false; 
var _wAnnotation = null;
var _oNavigator = null;
var hiddenAnnotations = 0;

/**
 * Przypisanie akcji po wczytaniu się strony.
 */
$(function(){
	$.each($("#content *"), function(index, value){
		$(value).after('<span style="display:none">&nbsp;</span>');
	});	
	
	$(".hideLayer").click(function(){
		if (!$(this).attr("disabled")){
			layerArray = $.parseJSON($.cookie('hiddenLayer'));
			layerId = $(this).attr("name").replace("layerId","id");
			if ($(this).hasClass("hiddenLayer")) {
				$(this).attr("title","show");
				delete layerArray[layerId];
			}
			else{
				layerArray[layerId]=1;
				$(this).attr("title","hide");
			}
			newCookie="{ ";
			$.each(layerArray,function(index,value){
				newCookie+='"'+index+'":'+value+',';
			});
			$.cookie('hiddenLayer',newCookie.slice(0,-1)+"}");
			set_visible_layers();
		}
	});

	$(".clearLayer").click(function(){
		layerArray = $.parseJSON($.cookie('clearedLayer'));
		layerArray2 = $.parseJSON($.cookie('hiddenLayer'));
		layerId = $(this).attr("name").replace("layerId","id");
		if ($(this).hasClass("clearedLayer")) {
			delete layerArray[layerId];
			delete layerArray2[layerId];
		}
		else {
			layerArray[layerId]=1;
			layerArray2[layerId]=1;
		}
		var newCookie="{ ";
		$.each(layerArray,function(index,value){
			newCookie+='"'+index+'":'+value+',';
		});
		$.cookie('clearedLayer',newCookie.slice(0,-1)+"}");
		newCookie="{ ";
		$.each(layerArray2,function(index,value){
			newCookie+='"'+index+'":'+value+',';
		});
		$.cookie('hiddenLayer',newCookie.slice(0,-1)+"}");

		if (document.location.href[document.location.href.length-1]=="#") document.location.href=document.location.href.slice(0,-1);
		document.location = document.location;
		
	});
	
	$("#runNerModule").click(function(){

		var text = $.trim($("#content").text());
		
		$("#runNerModule").attr("disabled", "disabled");
		$("#runNerModule").after("<img class='ajax_indicator' src='gfx/ajax.gif'/>");
		
		var model = $("#ner-model option:selected").val();
		
		$.ajax({
			type: 	'POST',
			url: 	"index.php",
			data:	{ 	
						ajax: "report_autoextension_ner_process", 
						text: text,
						model: model,
						report_id : $("#report_id").text()
					},
			success:function(data){
						if ( data.success ){
							//log(data);
							$(".ajax_indicator").remove();
							//$("#content").append(data.html);
						}
						else
							dialog_error(data.errors);
						$("#runNerModule").removeAttr("disabled");
					},
			error: function(request, textStatus, errorThrown){
						$("#runNerModule").removeAttr("disabled");
					},
			dataType:"json"
		});		
		
		
	});	
	
	
	set_visible_layers();
	
});

function set_visible_layers(){
	if (!$.cookie('hiddenLayer')) $.cookie('hiddenLayer','{}');
	if (!$.cookie('clearedLayer')) $.cookie('clearedLayer','{}');
	var layerArray = $.parseJSON($.cookie('hiddenLayer'));
	$(".hideLayer").removeClass('hiddenLayer').attr("title","hide").attr("checked","checked");//.css("background-color","");
	$("#content span:not(.token)").removeClass('hiddenAnnotation');
	$("#widget_annotation div[groupid]").children().show().filter(".hiddenAnnotationPadLayer").remove();
	$(".layerName").css("color","").css("text-decoration","");
	$("#annotationList ul").show();
	
	$.each(layerArray,function(index,value){
		layerId = index.replace("id","");
		$('.hideLayer[name="layerId'+layerId+'"]').addClass('hiddenLayer').attr("checked","").attr("title","show").parent().prev().children("span").css("color","#AAA");
		$("#content span[groupid="+layerId+"]").addClass('hiddenAnnotation');
		$('#widget_annotation div[groupid="'+layerId+'"]').append('<div class="hiddenAnnotationPadLayer">This annotation layer was hidden (see Annotation layers)</div>').children("ul").hide();
		$('#annotationList ul[groupid="'+layerId+'"]').hide();
		
		
	});
	
	layerArray = $.parseJSON($.cookie('clearedLayer'));
	$(".clearLayer").removeClass('clearedLayer').attr("title","hide").attr("checked","checked");
	$.each(layerArray,function(index,value){
		layerId = index.replace("id","");
		$('.clearLayer[name="layerId'+layerId+'"]').addClass('clearedLayer').attr("checked","").attr("title","show").parent().prev().children().attr("disabled","disabled").parent().prev().children("span").css("text-decoration","line-through");
		var $container = $('#widget_annotation div[groupid="'+layerId+'"]')
		if ($container.children(".hiddenAnnotationPadLayer").length==0)
			$container.append('<div class="hiddenAnnotationPadLayer">This annotation layer was disabled (see Annotation layers)</div>').children("ul").hide();
		else $container.children(".hiddenAnnotationPadLayer").text("This annotation layer was disabled (see Annotation layers)");
	});
	$("#annotationsCount").text(parseInt($.cookie("allcount"))-$("#content span:not(.hiddenAnnotation)").length);
	


}



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
	if ($(newNode).parent().is(".token")){
		status_fade();
		dialog_error("You cannot create new annotation inside a token");
		return;
	}
			
	var content_html = $.trim($("#content").html());

	//console.log(content_no_html);
	content_html = content_html.replace(/<xyz>(.*?)<\/xyz>/, fromDelimiter+"$1"+toDelimiter);
	//content_no_html = html2txt(content_no_html);
	content_no_html = content_html.replace(/<\/?[^>]+>/gi, '');

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
					status_fade();
				},
		error: function(request, textStatus, errorThrown){
				  dialog_error(request['responseText']);
				  status_fade();
				},
		dataType:"json"
	});	
}


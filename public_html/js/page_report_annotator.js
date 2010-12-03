var isCtrl = false; 
var _wAnnotation = null;
var _oNavigator = null;

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
});

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
		if (_wAnnotation.get() == annotation_clicked_by_label)
			set_current_annotation(null);
		else
			set_current_annotation(annotation_clicked_by_label);
		annotation_clicked_by_label = null
	}
	else if ( getSelText() == "" )
	{
		if (_wAnnotation.get() == this)
			set_current_annotation(null);
		else
			set_current_annotation(this);
	}
	console.log("span");
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
	if (default_annotation){
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
	$(".annotation_list input:default_annotation").click(function(){
		$("input:default_annotation ~ span").removeClass("hightlighted");
		$(this).next().addClass("hightlighted");
		$("#quick_add_cancel").show();
		$.cookie("default_annotation", $("input[name='default_annotation']:checked").val(), {});
	});	
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

$(document)
	.keyup(function (e) { 
		if(e.which == 17) 
			isCtrl=false; 
	})
	.keydown(function (e) { 
		if(e.which == 17) 
			isCtrl=true; 
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
			//_wAnnotation.keyDown(e, isCtrl)
		}
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
				$("#report_type + span").fadeOut("1000", function(){$("#report_type + span").remove()});
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
		alert("Błąd ciągłości adnotacji.\n\nMożliwe przyczyny:\n 1) Zaznaczona adnotacja nie tworzy ciągłego tekstu w ramach jednego elementu.\n 2) Adnotacja jest zagnieżdżona w innej adnotacji.\n 3)Adnotacja zawiera wewnętrzne adnotacje.")
		return false;
	}

	sel = selection.sel;

	var report_id = $("#report_id").val();
	
	var newNode = document.createElement("xyz");
	sel.surroundContents(newNode);
			
	var content_no_html = content_no_html = $.trim($("#content").html());

	content_no_html = content_no_html.replace(/<xyz>(.*?)<\/xyz>/, fromDelimiter+"$1"+toDelimiter);

	// Remove containers with labels
	jqhtml = $("<div>"+content_no_html+"</div>");
	$(".label_container", jqhtml).remove();
	content_no_html = jqhtml.html();
	
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


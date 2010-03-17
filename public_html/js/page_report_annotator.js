var isCtrl = false; 
var _wAnnotation = null;
var _oNavigator = null;

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

$("#content span").live("click", function(){
	if (_wAnnotation.isChanged()){
		// Wystąpił problem podczas zapisu.			
		$("#dialog .message").html("Zapisz lub cofnij dotychczas wprowadzone zmiany.");
		$("#dialog").dialog('destroy');
		$("#dialog").dialog( {
			bgiframe: true, 
			modal: true,
			buttons: {
				Ok: function() {
					$(this).dialog('close');
				}
			}
		} );		
	}else{
		_wAnnotation.set(this);		
	}
});

/*
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
		$("input[value='"+default_annotation+"']").attr('checked', true);
		$("input[value='"+default_annotation+"']").next().addClass("hightlighted");
		$("#quick_add_cancel").show();
	}
	
	$("#quick_add_cancel").click(function(){
		//$("input:default_annotation").blur();
		$("#default_annotation_zero").attr('checked', true);
		$("input:default_annotation ~ span").removeClass("hightlighted");
		$(this).hide();
		return false;
	});
	$("input:default_annotation").click(function(){
		//alert($(this).val());
		$("input:default_annotation ~ span").removeClass("hightlighted");
		$(this).next().addClass("hightlighted");
		$("#quick_add_cancel").show();
		$.cookie("default_annotation", $("input[name='default_annotation']:checked").val(), {});
	});	
	$("#content").mouseup(function(){
		var quick_annotation = $("input[name='default_annotation']:checked").val();
		if (quick_annotation){
			selection = new Selection();
			if ( selection.isValid )
				add_annotation(selection, quick_annotation);
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
			_oNavigator.moveRight();
		}
		if ( _wAnnotation != null ){
			_wAnnotation.keyDown(e, isCtrl)
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

function add_annotation(selection, type){
	selection.trim();
	selection.fit();

	if (!selection.isSimple){
		alert("Błąd ciągłości adnotacji.\n\nMożliwe przyczyny:\n 1) Zaznaczona adnotacja nie tworzy ciągłego tekstu w ramach jednego elementu.\n 2) Adnotacja jest zagnieżdżona w innej adnotacji.\n 3)Adnotacja zawiera wewnętrzne adnotacje.")
		return false;
	}

	sel = selection.sel;

	var report_id = $("#report_id").val();
	
	var newNode = document.createElement("span");
	//newNode.title = "an#0:"+type;
	//newNode.className = type;
	sel.surroundContents(newNode);
	
	var content = $("#content").html();		
	// Wytnij nawigatora
	content = content.replace(/<em[^<]*<\/em>/gi, "");
	content = content.replace(/<small[^<]*<\/small>/gi, "");
	content = content.replace(/<\/span>/gi, "</an>");
	content = content.replace(/<span id="an[0-9]+" class="[^>]*" title="an#([0-9]+):([a-z_]+)">/gi, "<an#$1:$2>");
	content = content.replace(/<span title="an#([0-9]+):([a-z_]+)" class="[^>]*" id="an[0-9]+">/gi, "<an#$1:$2>");
	//content = content.replace(/<([a-z]*).*?>(.*?)<\/$1>/gi, "$2");

	var content_no_html = $("#content").html();		
	// Wytnij nawigatora
	//content_no_hyml = content_no_hyml.replace(/<em[^<]*<\/em>/gi, "");
	content_no_html = content_no_html.replace(/<small[^<]*<\/small>/gi, "");
	content_no_html = content_no_html.replace(/<span id="an[0-9]+" class="[^>]*" title="an#[0-9]+:[a-z_]+">([^]*?)<\/span>/gi, "$1");
	content_no_html = content_no_html.replace(/<span id="an[0-9]+" title="an#[0-9]+:[a-z_]+" class="[^>]*">([^]*?)<\/span>/gi, "$1");
	content_no_html = content_no_html.replace(/<span title="an#[0-9]+:[a-z_]+" class="[^>]*" id="an[0-9]+">([^]*?)<\/span>/gi, "$1");
	content_no_html = content_no_html.replace(/<span title="an#[0-9]+:[a-z_]+" id="an[0-9]+" class="[^>]*">([^]*?)<\/span>/gi, "$1");
	content_no_html = content_no_html.replace(/<span class="[^>]*" id="an[0-9]+" title="an#[0-9]+:[a-z_]+">([^]*?)<\/span>/gi, "$1");
	content_no_html = content_no_html.replace(/<span class="[^>]*" title="an#[0-9]+:[a-z_]+" id="an[0-9]+">([^]*?)<\/span>/gi, "$1");
	content_no_html = content_no_html.replace(/<br(\/)?>/gi, "");
	content_no_html = content_no_html.replace(/<(\/)?p>/gi, "");
	content_no_html = $.trim(content_no_html);
	var from = content_no_html.indexOf("<span>");
	var to = content_no_html.indexOf("</span>") - 7;
	content_no_html = content_no_html.replace(/<span>([^]*?)<\/span>/gi, "$1");
	var text = content_no_html.substring(from, to+1);

	status_processing("dodawanie anotacji ...");
	
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
					if (data['success']){
						var annotation_id = data['annotation_id'];
						newNode.title = "an#"+annotation_id+":"+type;
						newNode.id = "an"+annotation_id;
						newNode.className = type;
						console_add("anotacja <b> "+newNode.title+" </b> została dodana do tekstu <i>"+text+"</i>");
					}else{
					    dialog_error(data['error']);
					    newNode.id = "new";
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

$(document).ready(function(){
	$("a.an").click(function(){
		// Przy wykonaniem jakiejkolwiek akcji zablokuj przyciski
		//$("input.an").attr("disabled", "true");

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

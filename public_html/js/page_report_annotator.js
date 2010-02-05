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

//---------------------------------------------------------
// Po załadowaniu strony
//---------------------------------------------------------
$(document).ready(function(){
	$("#annotations").tablesorter(); 

	$(".autogrow").autogrow();
	
	_wAnnotation = new WidgetAnnotation();
	
	$("#tag_buttons").fixOnScroll();
	_oNavigator = new Navigator($("#content"));
	
	$(document).ajaxError(function(e, xhr, settings, exception){
		alert("Wystąpił błąd ajax: " + exception);
	});
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
			window.location = $("#article_next").attr("href");
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


$(document).ready(function(){
	$("input.an").click(function(){
		// Przy wykonaniem jakiejkolwiek akcji zablokuj przyciski
		//$("input.an").attr("disabled", "true");

		selection = new Selection();
		if ( !selection.isValid )
		{
			alert("Zaznacz tekst");
			return null;
		}
				
		selection.trim();
		selection.fit();

		if (!selection.isSimple){
			alert("Błąd ciągłości adnotacji.\n\nMożliwe przyczyny:\n 1) Zaznaczona adnotacja nie tworzy ciągłego tekstu w ramach jednego elementu.\n 2) Adnotacja jest zagnieżdżona w innej adnotacji.\n 3)Adnotacja zawiera wewnętrzne adnotacje.")
			return;
		}

		sel = selection.sel;

		var type = $(this).val();
		var report_id = $("#report_id").val();
		
		var newNode = document.createElement("span");
		newNode.title = "an#0:"+type;
		newNode.className = type;
		newNode.id = "an0";
		sel.surroundContents(newNode);
		
		var content = $("#content").html();		
		// Wytnij nawigatora
		content = content.replace(/<em[^<]*<\/em>/gi, "");
		content = content.replace(/<small[^<]*<\/small>/gi, "");
		content = content.replace(/<\/span>/gi, "</an>");
		content = content.replace(/<span id="an[0-9]+" class="[^>]*" title="an#([0-9]+):([a-z_]+)">/gi, "<an#$1:$2>");
		content = content.replace(/<span title="an#([0-9]+):([a-z_]+)" class="[^>]*" id="an[0-9]+">/gi, "<an#$1:$2>");
		
		status_processing("dodawanie anotacji ...");
		
		$.post("index.php", { ajax: "report_add_annotation", report_id: report_id, content: content },
		  function(data){
			
			if (data['success']){
				var anid = data['anid'];
				var new_an_node = $("#an0");
				new_an_node.attr("id", "an"+anid);
				new_an_node.attr("title", new_an_node.attr("title").replace("#0:", "#"+anid+":"));
				new_an_node.before("<small>[#"+anid+"]</small>")
				console_add("anotacja <b> "+new_an_node.attr("title")+" </b> została dodana");
				status_fade();
			}else{
				var msg = "Wystąpił problem z dopasowanie tekstu do oryginału!!\n\n";
				msg += "W bazie:\n" + data['content_old']+"\n\n";
				msg += "Przesłano:\n" + data['content_new'];						
				alert( msg );
			}
			
			// Odblokuj przyciski
			$("input.an").removeAttr("disabled");
		  }, "json");			
	});
});

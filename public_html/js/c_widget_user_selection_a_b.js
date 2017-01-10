/**
* Zestaw metod do obsługi drzewa z typami anotacji.
* Obsługuje zwijanie, rozwijanie i zapisywanie wybranych typów w ciasteczkach. 
*/

var url = $.url(window.location.href);
var corpus_id = url.param("corpus");

var cookieAnnotatorA = corpus_id + "_annotator_a_id";
var cookieAnnotatorB = corpus_id + "_annotator_b_id";

/**
 * Ustawia zdarzenia zwijania, rozwijania i klikania w checkboxy.
 */
function setupUserSelectionAB(){	
	var annotator_a_id = $.cookie(cookieAnnotatorA); 
	var annotator_b_id = $.cookie(cookieAnnotatorB);
	$('input:radio[name="annotator_a_id"][value="'+annotator_a_id+'"]').attr('checked', true);
	$('input:radio[name="annotator_b_id"][value="'+annotator_b_id+'"]').attr('checked', true);
	
    $('#user_selection_a_b input[type=radio][name=annotator_a_id]').change(function() {
    	$.cookie(cookieAnnotatorA, this.value);
    });

    $('#user_selection_a_b input[type=radio][name=annotator_b_id]').change(function() {
    	$.cookie(cookieAnnotatorB, this.value);
    });
}
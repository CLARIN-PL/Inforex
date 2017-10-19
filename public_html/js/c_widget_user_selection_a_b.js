/**
* Zestaw metod do obsługi drzewa z typami anotacji.
* Obsługuje zwijanie, rozwijanie i zapisywanie wybranych typów w ciasteczkach. 
*/

/**
 * Ustawia zdarzenia zwijania, rozwijania i klikania w checkboxy.
 * @param {String} [cookieName] - opcjonalna nazwa zmiennej do przechowywania identyfikatora użytkowników
 */
function setupUserSelectionAB(cookieName){
    var url = $.url(window.location.href);
    var corpus_id = url.param("corpus");

    if ( typeof cookieName === "undefined" ){
        cookieName ="default";
	}
	var cookieAnnotatorA = "agreement_" + cookieName + "_" + corpus_id + "_annotator_id_a";
	var cookieAnnotatorB = "agreement_" + cookieName + "_" + corpus_id + "_annotator_id_b";

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
/**
 * Funkcja zwraca zaznaczony tekst w postaci niesformatowanego tekstu.
 * @return niesformatowany tekst zaznaczenia
 */
function getSelText()
{
	var txt = '';
    if (window.getSelection)
    {
        txt = window.getSelection();
    }
    else if (document.getSelection)
    {
        txt = document.getSelection();
    }
    else if (document.selection)
    {
        txt = document.selection.createRange().text;
    }
    else return "";
    return txt;
}
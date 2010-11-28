<?
include("../../engine/include/lib_htmlparser.php");

$text = '<p>TM/20/10/04/55<br/><an#571:company_nam>SKARBIEC-LOKACYJNY Fundusz Inwestycyjny Mieszany Papierów Dłużnych</an> ("Fundusz") informuje, że w dniu <an#572:date>19.10.2004 r.</an> została dokonana wycena aktywów Funduszu.<br/>Wartość aktywów netto na certyfikat inwestycyjny Funduszu wyniosła 104,05 złotych.<br/>Wartość aktywów netto Funduszu wyniosła 119.127.955,35 złotych.</p>';

$annotations = HtmlParser::readInlineAnnotations($text);
print_r($annotations);

?>
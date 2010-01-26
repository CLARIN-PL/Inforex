<?php
/* 
 * ---
 * Założenia dotyczące pliku wejściowego:
 * - tekst podzielony jest na paragrafy znacznikami <p>...</p>
 * - tekst wewnątrz znaczników podzielony jest na zdania znacznikiem <br/>
 * - tekst zawiera anotacje <an#id:typ>...</an>
 * ---
 * Created on 2010-01-13
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */

include("anntakipi_functions.php");
include("ixtTextAligner.php");
include("ixtTakipiAligner.php");
include("ixtTakipiReader.php");
include("ixtTakipiDocument.php");

$file = $argv[1];

if ($file == "" ) die("Podaj nazwę pliku do otagowania\n");

if (!file_exists($file)) die ("Plik '$file' nie istnieje\n");
 
$content = file_get_contents($file);
$content_clean = strip_tags($content);

$content_tagged = takipi($content_clean);

$takipiDoc = TakipiDocument::createFromText($content_tagged);

$annDoc = TakipiAligner::align($content, $takipiDoc);
print_r($annDoc);

?>

<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
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

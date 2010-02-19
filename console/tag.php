<?php
/* 
 * ---
 * Urównolegla pliki txt i tag
 * ---
 * Created on 2010-01-14
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */ 
 
$location = "/home/czuk/nlp/corpora/gpw2004";

$what = $argv[1];

//if ($what != "all" && intval($what)==0) die ("Incorrect argument. Expected 'all' or raport id.\n\n");  
if (intval($what)==0) die ("Incorrect argument. Expected report id.\n\n");  

$name = str_pad($what, 7, "0", STR_PAD_LEFT).".txt";

$cmd = "takipi -i {$location}/text/{$name} -o {$location}/tag/{$name}.tag";
shell_exec($cmd);
 
?>

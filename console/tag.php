<?php
/* 
 * ---
 * Urównolegla pliki txt i tag
 * ---
 * Created on 2010-01-14
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */ 
 
include("cliopt.php");

mb_internal_encoding("UTF-8");

/******************** set configuration   *********************************************/

$config = null;

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("tager", "t", "name", "tager name: maca or takipi"));
$opt->addParameter(new ClioptParameter("corpus-location", "c", "path", "path to a folder where the data will be save"));
$opt->addArgument(new ClioptArgument("action", "type of action", array("all", "resume", "DECIMAL")));

try{
	$opt->parseCli($argv);
	$config->action = $opt->getArgument();
	$config->tager = $opt->getRequired("tager");
	$corpus_path = $opt->getRequired("corpus-location");
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/* Sprawdź, czy istnieje katalog z tekstami */
if ( !file_exists("$corpus_path/$text") )
	throw new Exception("'$corpus_path/$text' does not exist");

/* Utwórz podkatalogi */
if ( $config->tager == "takipi" && !file_exists("$corpus_path/takipi")){
	mkdir("$corpus_path/takipi");
	chmod("$corpus_path/takipi", 0777);
}

if ( $config->tager == "maca" && !file_exists("$corpus_path/maca")){
	mkdir("$corpus_path/maca");
	chmod("$corpus_path/maca", 0777);
}

/* Przetwórz pliku z podkatalogu text */
$files_to_process = array();

if (is_numeric($config->action)){
	$name = str_pad($config->action, 7, "0", STR_PAD_LEFT).".txt";
	$file_input = "{$corpus_path}/text/{$name}";
	$files_to_process[] = $file_input;
}elseif ($config->action == "all"){

	if ($handle = opendir($corpus_path."/text/")) {
	    while (false !== ($file = readdir($handle))) {
	        if ($file != "." && $file != ".." && substr($file, strlen($file)-4) == ".txt") {
	            $file_input = "$corpus_path/text/$file";	            

				if ( $config->tager == "takipi" ) {				
					$files_to_process[] = $file_input;
				}
				else if ( $config->tager == "maca" ) {
	            	$file_output = "$corpus_path/maca/$file";
			        system("cat $file_input | maca-analyse -qs morfeusz-nkjp -o xces > $file_output");
					$files_to_process[] = $file_output;
				}				
	        }
	    }
	    closedir($handle);
	}
} 

if ( $config->tager == "takipi" ) {
	$tempfile = "templist.txt";
	file_put_contents($tempfile, implode("\n", $files_to_process));
	system("takipi -is $tempfile");
	unlink($tempfile);
	
	if ($handle = opendir($corpus_path."/text/")) {
	    while (false !== ($file = readdir($handle))) {
	        if ($file != "." && $file != ".." && substr($file, strlen($file)-4) == ".tag") {
	            $file_input = "$corpus_path/text/$file";
	            $file_output = "$corpus_path/takipi/$file";	
	            rename($file_input, $file_output);
	        }
	    }
	    closedir($handle);
	}
	
}
else if ( $config->tager == "maca" ){
	$tempfile = "templist.txt";
	file_put_contents($tempfile, implode("\n", $files_to_process));
	system("/nlp/workdir/wmbt/wmbt/wmbt.py -d /nlp/workdir/wmbt/model_nkjp10 /nlp/workdir/wmbt/config/nkjp-k11.ini --batch $tempfile -o xces");
	unlink($tempfile);	
}


?>

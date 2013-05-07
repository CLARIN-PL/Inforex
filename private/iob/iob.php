<?php
/* 
 * ---
 * Converts documents to IOB represenation in a simple format.
 * Wywołanie:
 *   php iob.php <corpus> all     // test all files in a folder
 *   php iob.php <corpus> resume  // continue testing all files in a folder 
 *   php iob.php <corpus> <id>    // test a files with given id
 * ---
 * Created on 2010-01-14
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */ 
 
include("../../engine/include/anntakipi/ixtTakipiAligner.php"); 
include("../../engine/include/anntakipi/ixtTakipiStruct.php"); 
include("../../engine/include/anntakipi/ixtTakipiDocument.php"); 
include("../../engine/include/anntakipi/ixtTakipiHelper.php"); 

include("../cliopt.php");
mb_internal_encoding("utf-8");
 
/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addArgument(new ClioptArgument("action", "type of action", array("all", "resume", "DECIMAL")));
$opt->addParameter(new ClioptParameter("corpus-location", "c", "path", "path to a folder where the data will be save"));
$opt->addParameter(new ClioptParameter("output", "o", "path", "name of file where to save data in a IOB format"));
$opt->addParameter(new ClioptParameter("ignore", "i", "annotation", "annotation to ignore"));
$opt->addParameter(new ClioptParameter("dont-ignore", "a", "annotation", "annotation not to ignore"));
$opt->addParameter(new ClioptParameter("takipi", null, null, "output format"));
$opt->addParameter(new ClioptParameter("tager", "t", "maca|takipi", "data to use"));

$config = null;

$config->ignore = array();
$config->dontignore = array();
$config->map = array();
$config->features = array("orth", "base", "ctag");

/******************** parse cli *********************************************/

try{
	$opt->parseCli($argv);
	
	$config->action = $opt->getArgument();
	$config->output = $opt->getOptional("output", "data.iob");
	$config->ignore = $opt->getOptionalParameters("ignore");
	$config->takipi = $opt->exists("takipi");
	$config->tager = $opt->getRequired("tager");
	if (!$config->takipi){
		$config->location = $opt->getRequired("corpus-location");
	}
	
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/******************** functions           *********************************************/
// 
function to_oai($textfile, $tagfile, $f=null){
	global $config;
	
	if (!file_exists($tagfile))
		throw new Exception("File '$tagfile' does not exist\n");		
	echo sprintf("%-40s ", $textfile);
	$takipiDoc = TakipiReader::createDocument($tagfile);	
	$text = file_get_contents($textfile);
	if ($config->tager == "takipi")
		$text = TakipiHelper::replace($text);
		
	TakipiAligner::align($text, $takipiDoc);
	
	// Remove ignored annotations
	if ( $config->ignore ){
		for ($s=0; $s<count($takipiDoc->sentences); $s++)
			for ($i=0; $i<count($takipiDoc->sentences[$s]->tokens); $i++){
				//if ( isset($takipiDoc->sentences[$s]->tokens[$i]->channels['person_nam']) )
				//	print_r($takipiDoc->sentences[$s]->tokens[$i]);
				foreach ( $config->ignore as $channal )
					unset($takipiDoc->sentences[$s]->tokens[$i]->channels[$channal]);
			}
	}
			
	if ($f) 
		fwrite($f, "-DOCSTART FILE $textfile\n");
	
	$annotation_number = 0;

	// Remove nested annotations
	for ($s=0; $s<count($takipiDoc->sentences); $s++)
	{
		$current = false;
		for ($i=0; $i<count($takipiDoc->sentences[$s]->tokens); $i++)
		{
			$token = &$takipiDoc->sentences[$s]->tokens[$i];
								
			// Check if current is stil in the channel
			if ($current)
				$current = $token->channels[$current]=="I" ? $current : false;
				
			// If current is not set the find out whitch one is the current
			if (!$current)				
			{
				$begins = array();
				foreach ($token->channels as $name=>$type)
				{
					if ($type == "B")
						$begins[] = $name;
				}
				if (count($begins) == 1){
					$current = $begins[0];//
				}
				elseif ( count($begins) > 1 ){
					// Choose the longest one
					$length = 0;
					$current = null;
					foreach ( $begins as $channel ){
						$j = $i + 1;
						while ( $j < count($takipiDoc->sentences[$s]->tokens)
						 && $takipiDoc->sentences[$s]->tokens[$j]->channels[$channel] == "I"){
							$j++;
						}
						if ( $j - $i > $length){
							$length = $j - $i;
							$current = $channel;
						}						
					}
				}
			}
			
			// Reset other than current
			if ($current){
				foreach ($token->channels as $name=>$type)
					if ( $name != $current )
						$token->channels[$name] = "O";
			}
	
			$count = 0;				
			foreach ($token->channels as $name=>$type)
				$count += $type == "O" ? 0 : 1;

			if ( $count > 1){
				print_r($token);
				die();
			}
		}
	}
	
	foreach ($takipiDoc->sentences as $sentence){
		foreach ( $sentence->tokens as $t ){
			$line = "";
			if (in_array("orth", $config->features)) $line .= trim($t->orth) . " ";
			if (in_array("base", $config->features)) $line .= $t->getDisamb()->base . " ";
			if (in_array("ctag", $config->features)) $line .= $t->getDisamb()->ctag . " ";
			
			// Find first channel with I or B
			$channel_name = null;
			$channel_type = null;
			$channel_count = 0;
			foreach ($t->channels as $name=>$type){
				if ($name=="")
					continue;
				if ( $type=="B" || $type=="I" ){
					$channel_count++;
					$channel_name = $name;
					$channel_type = $type;
				}
			}
			if ($channel_count>1){
				throw new Exception("More then one channel set: " . implode(", ", $t->channels) . " " . implode(", ", array_keys($t->channels)));
			}
			
			// Set token class
			if ($channel_name!=null){
				$line .= "$channel_type-". strtoupper($channel_name). "\n";
				$annotation_number++;
			}
			else
				$line .= "O\n";
			
			// Output
			if ($f==null)
				echo $line;
			else
				fwrite($f, $line);			
		}
		if ($f==null) echo "--EOS--\n"; else fwrite($f, "\n");
	}
	
	// Print summary.
//	$after = $all_annotation_count != $final_annotation_count ? " > " . sprintf("%3d", $final_annotation_count) : "";
	echo sprintf("%3d annotation(s)", $annotation_number);	
}

// Convert a name of a file with annotation to the name of tagged file.
function get_tagged_filename($annotation_file){
	global $config;
	return preg_replace("/(\/annotated\/)(?!.*\1)/", "/{$config->tager}/", $annotation_file).".tag";
}

/**
 * Save file configuration in the header.
 * @param config $config
 * @param file $f
 */
function write_config($config, $f){
	fwrite($f, "-DOCSTART CONFIG FEATURES " . implode(" ", $config->features)."\n");
	if (count($config->ignore)>0)
		fwrite($f, "-DOCSTART CONFIG IGNORE ".implode(", ", $config->ignore)."\n");
	if (count($config->dontignore)>0)
		fwrite($f, "-DOCSTART CONFIG DONTIGNORE ".implode(", ", $config->dontignore)."\n");
}

/******************** main function       *********************************************/
// Process all files in a folder
function main ($config){
	
	$count_files = 0;
	$count_sentences = 0;
	$count_tokens = 0;	
	$count_annotations = 0;
	
	if ($config->action == "all" || $config->action == "resume" ){
		$f = null;		
		if ( file_exists("progress.txt") && $config->option == "resume" ){
			$progress = explode(",", file_get_contents("progress.txt"));
			$f = fopen($config->output, "a");
		}else{
			$progress = array();
			$f = fopen($config->output, "w");
			write_config($config, $f);
		}
		
		$i = count($progress) + 1;
		$exceptions = 0;
		
		$files_to_process = array();
		
		if ($handle = opendir($config->location."/text")){
			
			while ( false !== ($file = readdir($handle))){
				if ($file != "." && $file != ".."){					
					//if (in_array($file, $progress)) continue;					
					$files_to_process[] = $file;
					
				}
			}
			
			// If we reach this point we can remove the progress temp file
			//unlink("progress.txt");
			
			sort($files_to_process);
			
			foreach ($files_to_process as $file){
				$annotation_filename = $config->location . "/annotated/" . $file;
				try{ 
					list($file_sentences, $file_tokens, $file_annotations) 
						= to_oai($annotation_filename, get_tagged_filename($annotation_filename), $f);
				}
				catch(Exception $ex){
					echo "!!!!!!!!!!!!!!!\n";
					print_r($ex->getMessage());
					echo "\n---------------\n";
					$exceptions++;
				}
						
				echo sprintf("%8d\n", $i++);
				$progress[] = $file;
				file_put_contents("progress.txt", implode(",", $progress));									
			}						
		}
		fclose($f);
		
		if ($exceptions>0){
			throw new Exception("There is something wrong, $exceptions exception(s) were reported");
		}
	// Process a single file
	} else {
		$f = fopen($config->output, "w");
		write_config($config, $f);
		$filename = str_pad($config->action, 7, "0", STR_PAD_LEFT) . ".txt";
		$annotation_filename = $config->location . "/annotated/" . $filename; 
		to_oai($annotation_filename, get_tagged_filename($annotation_filename), $f);
		echo "\n";	
	}
} 

/******************** main invoke         *********************************************/
main($config);
?>

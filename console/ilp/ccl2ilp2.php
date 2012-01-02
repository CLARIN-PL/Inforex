<?
/**
 * Skrypt do transformacji korpusu w formacie CCL to bazy wiedzy na potrzeby ILP.
 * Michał Marcińczuk <marcinczuk@gmail.com>
 * październik 2011
 */
mb_internal_encoding("UTF-8");

include("../cliopt.php");
include("../../engine/config.php");
include("../../engine/config.local.php");
include("../../engine/include.php");
ob_end_clean();

/******************** set configuration   *********************************************/

$opt = new Cliopt("Converts CCL corpus into ILP knowledge base.");
$opt->setAuthors("Michał Marcińczuk");

$opt->addParameter(new ClioptParameter("corpus", "c", "path", "path to a corpus for which to construct the knowledge base"));
$opt->addParameter(new ClioptParameter("output", "o", "file", "path to a file where to save knowledge base"));

$config = null;
try{
	$opt->parseCli($argv);	
	$config->corpus = $opt->getRequired("corpus");
	$config->output = $opt->getRequired("output");
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/***************************************************************/

/**
 * Zwraca tablicę obiektów WcclDocument.
 */
function loadWcclDocuments($folder){
	$documents = array();
	//$files = array();
	
	if ($handle = opendir($folder)) {
	    while (false !== ($file = readdir($handle))) {
	        if ($file[0] != "." && $file != "..") {
	            echo "$file\n";
	            $documents[] = WcclReader::readDomFile("$folder/$file");
	        }
	    }
	    closedir($handle);
	}
	return $documents;	
}

/*********************************/
function transform_orth($orth){

	$orth = str_replace("\"", "s_PAR", $orth);	
	$orth = str_replace(".", "s_DOT", $orth);
	$orth = str_replace(",", "s_COMMA", $orth);
	$orth = str_replace("(", "s_BRACKET_L", $orth);
	$orth = str_replace(")", "s_BRACKET_R", $orth);
	$orth = str_replace("[", "s_SQBRACKET_L", $orth);
	$orth = str_replace("]", "s_SQBRACKET_R", $orth);
	$orth = str_replace("–", "s_DASH", $orth);
	$orth = str_replace("-", "s_DASH", $orth);
	$orth = str_replace(":", "s_DOTS", $orth);
	$orth = str_replace("+", "s_PLUS", $orth);
	$orth = str_replace("%", "s_PERCENT", $orth);
	$orth = str_replace("=", "s_EQUAL", $orth);
	$orth = str_replace("°", "s_OOO", $orth);
	$orth = str_replace("®", "s_RESERVED", $orth);
	$orth = str_replace(";", "s_SEMICOLON", $orth);
	$orth = str_replace("/", "s_SLASH", $orth);
	$orth = str_replace("&", "s_AMP", $orth);
	$orth = mb_strtolower($orth, 'UTF-8');
	$orth = utf8_decode($orth);
	$orth = str_replace("'", "_", $orth);
	$orth = str_replace("?", "_", $orth);
	$orth = str_replace("ł", "l", $orth);
	$orth = str_replace("ę", "e", $orth);
	$orth = str_replace("ż", "z", $orth);
	$orth = str_replace("ą", "a", $orth);
	$orth = str_replace("ń", "n", $orth);
	$orth = str_replace("ź", "z", $orth);
	
	return "w_" . $orth;
}
/*********************************/

$words = array();
$annotation_types = array();

$fb = fopen("/nlp/workdir/ilp/yap-6/relations2.b", "w");
$ff = fopen("/nlp/workdir/ilp/yap-6/relations2.f", "w");
$fn = fopen("/nlp/workdir/ilp/yap-6/relations2.n", "w");

fwrite($fb, file_get_contents("ilp_header.txt"));
fwrite($fb, "\n");

$documents = loadWcclDocuments($config->corpus);

$document_id = 1;
foreach ($documents as $d){
	$ad = DocumentConverter::wcclDocument2AnnotatedDocument($d);
	
	foreach ($ad->getChunks() as $c)
	
	foreach ($c->getSentences() as $s){
		$prev = null;
		foreach ($s->getTokens() as $t){
			$token_global_id = sprintf("d%d_%s_t%s", $document_id, $s->id, $t->id);
			fwrite($fb, sprintf("token(%s). ",$token_global_id ));
			if ($prev != null){
				fwrite($fb, sprintf("token_after_token(%s, %s). ", $prev, $token_global_id));
			}
			fwrite($fb, sprintf("token_attributes(%s, '%s').\n", $token_global_id, transform_orth($t->orth)));
			$words[transform_orth($t->orth)] = 1;
			$prev = $token_global_id;
		}
		foreach ($s->getAnnotations() as $a){
			$annotation_id = sprintf("d%s_%s_a%s", $document_id, $s->id, $a->id);
			$token_source_id = sprintf("d%d_%s_t%s", $document_id, $s->id, $a->getFirstToken()->id);
			$token_target_id = sprintf("d%d_%s_t%s", $document_id, $s->id, $a->getLastToken()->id);
			fwrite($fb, sprintf("annotation(%s). ", $annotation_id));
			fwrite($fb, sprintf("annotation_attributes(%s, %s, %s, %s).\n", 
					$annotation_id, $token_source_id, $token_target_id, $a->type));
			$annotation_types[$a->type] = 1;
		}
		fwrite($fb, "\n");
	}	

	/** Wygeneruj pozytywne relacje */
	
	// [typ_relacji][id_anotacji][id_anotacji] = 1
	$relations = array();
	
	foreach ($ad->getRelations() as $r){
		$type = strtolower($r->type);
		$annotation_source_id = sprintf("d%s_%s_a%s", $document_id, $r->source->sentence->id, $r->source->id);		
		$annotation_target_id = sprintf("d%s_%s_a%s", $document_id, $r->target->sentence->id, $r->target->id);		
		fwrite($ff, sprintf("relation_%s(%s, %s).\n", $type, $annotation_source_id, $annotation_target_id));

		$relations[$type][$annotation_source_id][$annotation_target_id] = 1;
		$relations[$type][$annotation_target_id][$annotation_source_id] = 1;

//		$type = "location";
//		if ($r->type == "LOCATION")
//			$type = "nationality";
//			
//		fwrite($fn, sprintf("relation_%s(%s, %s).\n", strtolower($type), $annotation_source_id, $annotation_target_id));		
	}
	
	$document_id++;
}

fwrite($fb, "\n");
foreach (array_keys($words) as $w){
	fwrite($fb, sprintf("orth('%s'). \n", $w));
}

fwrite($fb, "\n");
foreach (array_keys($annotation_types) as $t){
	fwrite($fb, sprintf("annotation_type(%s). \n", $t));
}

fclose($fb);
fclose($ff);

?>

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

$folder = $argv[1];
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


/*********************************/
$header = array();
$pattern_section_separator = "\n\n";

/* Types definition */
$pattern_tag = "tag(%s). ";
$pattern_word = "\nword(%s). ";
$pattern_document = "document(%s). ";
$pattern_sentence = "sentence(%s). ";
$pattern_token = "\ntoken(%s). ";
$pattern_type = "type(%s). ";
//$pattern_annotation = "annotation(%s). ";

/* Predicate definition  */
$pattern_pred_sentence_in_document = "sentence_in_document(%s, %s). ";
$pattern_pred_token_in_sentence = "token_in_sentence(%s, %s). ";
$pattern_pred_token_has_orth = "token_has_word(%s, %s).";
//$pattern_pred_relation = "relation(%s, %s, %s).";
$pattern_pred_relation = "relation(%s, %s).";
//$pattern_token_tag = " token_tag(%s, %s).";
$pattern_pred_annotation = "annotation(%s, %s, %s).";

$header[] = ":- set(i,2).";
//$header[] = ":- set(evalfn,posonly).";
$header[] = ":- set(clauselength,8).";
//$header[] = ":- set(gsamplesize,20).";

//$header[] = ":- modeh(*,relation(+token,+token,#type)).";
$header[] = ":- modeh(*,relation(+sentence,#type)).";

$header[] = ":- modeb(*,token_tag(+token,#tag)).";
$header[] = ":- modeb(1,sentence_in_document(+sentence,-document)).";
$header[] = ":- modeb(*,sentence_in_document(-sentence,+document)).";
$header[] = ":- modeb(1,token_in_sentence(+token,-sentence)).";
$header[] = ":- modeb(*,token_in_sentence(-token,+sentence)).";
$header[] = ":- modeb(1,token_has_word(+token,#word)).";
$header[] = ":- modeb(1,annotation(#annotation_type,+token,+token)).";

$header[] = ":- determination(relation/2,token_in_sentence/2).";
$header[] = ":- determination(relation/2,token_has_word/2).";
$header[] = ":- determination(relation/2,token_tag/2).";


//$relation_types = array()

$word_index = array();
$document_index = array();
$sentence_index = array();
$token_index = array();
$type_index = array();
$tag_index = array( sprintf($pattern_tag, "o") => 1);
$document_sentence_index = array();
$relation_index = array();
$relation_index_f = array();
$relation_index_n = array();

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
$count_doc = 0;
/* Go though all documents */
foreach ($documents as $document){
	
	$annotation_index = array(); // $key->$token_id
	
	$document_id = "doc_" . (++$count_doc);
	$document_index[] = sprintf($pattern_document, $document_id);
	$document_sentence_index = array();
	
	/* Go through all chunks in document */
	$count_sent = 0;
	foreach ($document->chunks as $chunk){

		$sentence_relations  = array();
		
		foreach ($chunk->sentences as $sentence){
			$sentence_id = "sent_d" . $count_doc . "_" . $sentence->id;
			$sentence_index[] = sprintf($pattern_sentence, $sentence_id);
			$document_sentence_index[] = sprintf($pattern_pred_sentence_in_document, $sentence_id, $document_id);
			
			$count_token = 0;
			$prev_token = null;
			/* Go through all tokens */
			foreach ($sentence->tokens as $token){
				$orth = transform_orth($token->orth);
				
				$word_index[sprintf($pattern_word, $orth)] = 1;
				//$word_index[$orth] = 1;
				$token_id = "tok_" . $count_doc . "_" . $sentence->id . "_" . (++$count_token);
				$token_index[] = sprintf($pattern_token, $token_id);
				$token_index[] = sprintf($pattern_pred_token_in_sentence, $token_id, $sentence_id);
				$token_index[] = sprintf($pattern_pred_token_has_orth, $token_id, $orth);
				
				foreach ($token->channels as $chanel=>$value){
					$tag_index[sprintf($pattern_tag, $chanel."_b")] = 1;
					$tag_index[sprintf($pattern_tag, $chanel."_i")] = 1;
					
					if ( $value>0 ){
						$tag = $chanel;
						if ( $prev_token == null || $prev_token->channels[$chanel] != $value ) {
							$tag .= "_b";
							$key = $sentence->id . "_" .$chanel . "_" . $value;
							
							$annotation_index[$key] = $token_id;
						}
						else{
							$tag .= "_i";							
						}
						
						$token_index[] = sprintf($pattern_token_tag, $token_id, $tag);
					}
				}
				
				$prev_token = $token;
			}
			
			// Zakładamy, że nie ma relacji
			$sentence_relations[$sentence_id] = 0;
		}
	}

	foreach($document->relations as $relation){
		
		$type = mb_strtolower($relation->type);
		if ( $type != 'location')
			continue;
				
		$key1 = sprintf("%s_%s_%s", $relation->source_sentence_id, $relation->source_channal_name, $relation->source_id);
		$key2 = sprintf("%s_%s_%s", $relation->target_sentence_id, $relation->target_channal_name, $relation->target_id);
		if ( !isset($annotation_index[$key1]) ){
			print_r($relation);
			die(" Key '$key1' not found");
		}
		if ( !isset($annotation_index[$key2]) ){
			print_r($relation);
			die(" Key '$key2' not found");
		}
		$relation_index[] = sprintf($pattern_pred_relation, $annotation_index[$key1], $annotation_index[$key2], $type);
		$type_index[sprintf($pattern_type, $type)] = 1;
		
		$sentence_id = "sent_d" . $count_doc . "_" . $relation->source_sentence_id;		
		//$relation_index_f[] = sprintf($pattern_pred_relation, $sentence_id , $type);
		$sentence_relations[$sentence_id] = 1;
	} 
	
	foreach ($sentence_relations as $sentence_id=>$present){
		$relation = sprintf($pattern_pred_relation, $sentence_id , "location");
		if ($present)
			$relation_index_f[] = $relation;
		else
			$relation_index_n[] = $relation;
	}
}

/*********************************/
$f = fopen("/nlp/workdir/ilp/yap-6/relations.b", "w");

/* Generuj bazę wiedzy */
fwrite($f, implode("\n", $header));
fwrite($f, $pattern_section_separator);

fwrite($f, implode(array_keys($type_index)));
fwrite($f, $pattern_section_separator);

/* * tags */
fwrite($f, implode(array_keys($tag_index)));
fwrite($f, $pattern_section_separator);

/* * słowa */
fwrite($f, implode(array_keys($word_index)));
fwrite($f, $pattern_section_separator);

/* * dokumenty */
fwrite($f, implode($document_index));
fwrite($f, $pattern_section_separator);

/* * zdania */
fwrite($f, implode($sentence_index));
fwrite($f, $pattern_section_separator);

/* sentence -> document */
fwrite($f, implode($document_sentence_index));
fwrite($f, $pattern_section_separator);

/* * tokens */
fwrite($f, implode($token_index));
	
fclose($f);

/***/
$ff = fopen("/nlp/workdir/ilp/yap-6/relations.f", "w");
fwrite($ff, implode("\n", $relation_index_f));
fclose($ff);

$ff = fopen("/nlp/workdir/ilp/yap-6/relations.n", "w");
fwrite($ff, implode("\n", $relation_index_n));
fclose($ff);


?>

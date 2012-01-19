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
$opt->addParameter(new ClioptParameter("relation", "r", "relation_name", "relatio name to be learned"));

$config = null;
try{
	$opt->parseCli($argv);	
	$config->corpus = $opt->getRequired("corpus");
	$config->output = $opt->getRequired("output");
	$config->relations = $opt->getParameters("relation");
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/***************************************************************/

/**
 * Zwraca tablicę obiektów WcclDocument.
 */
echo "1. Wczytywanie dokumentów ...\n";
$cclDocuments = CclReader::readCclDocumentFromFolder($config->corpus);
echo "2. Zapis do formatu Aleph ...\n";
$aw = new AlephWriter();
$aw->write($config->output, $cclDocuments, $config->relations);
echo "3. Gotowe.";


?>

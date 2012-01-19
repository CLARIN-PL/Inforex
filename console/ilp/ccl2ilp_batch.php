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

/**
 * Zwraca tablicę obiektów WcclDocument.
 */

/******************** set configuration   *********************************************/

$opt = new Cliopt("Creates training data for all types of relations from CCL to Aleph format.");
$opt->setAuthors("Michał Marcińczuk");

$opt->addParameter(new ClioptParameter("folder", "f", "path", "folder with subfolders representing all types of relations"));

$config = null;
try{
	$opt->parseCli($argv);	
	$target = $opt->getRequired("corpus");
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/***************************************************************/
 
$relations = array('origin', 'nationality', 'location', 'affiliation', 'creator', 'composition', 'neighbourhood', 'alias');
$pak = array("train", "test", "tune");
 
$aw = new AlephWriter(); 
 
foreach ($pak as $p){
	echo "1. Wczytywanie dokumentów ... $p \n";
	$cclDocuments = CclReader::readCclDocumentFromFolder("$corpora_base/$p");
	
	foreach ($relations as $r){
		echo "2. Zapis do formatu Aleph ... $r \n";
		$aw->write("$target/{$r}_{$p}", $cclDocuments, array($r));
	}
}
echo "3. Gotowe.";


?>

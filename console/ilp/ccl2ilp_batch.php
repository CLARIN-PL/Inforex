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

$opt->addParameter(new ClioptParameter("corpus", "c", "path", "folder with train, tune and test subfolders"));
$opt->addParameter(new ClioptParameter("output", "o", "path", "output folder"));

$config = null;
try{
	$opt->parseCli($argv);	
	$output = $opt->getRequired("output");
	$corpus = $opt->getRequired("corpus");
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/***************************************************************/
 
$relations = array('origin', 'nationality', 'location', 'affiliation', 'creator', 'composition', 'neighbourhood', 'alias');
$pak = array("train", "test", "tune");
  
foreach ($pak as $p){

	$aw = new AlephWriter(); 
	
	if (!file_exists("$output/$p"))
		mkdir("$output/$p");
	
	echo "Wczytywanie dokumentów '$corpus/$p' ... \n";
	$cclDocuments = CclReader::readCclDocumentFromFolder("$corpus/$p");

	echo "Transformacja dokumentów ... \n";
	$aw->loadCorpus($cclDocuments);

	echo "Zapis nagłówka Aleph $output/$p/aleph_header.txt ... \n";
	$aw->writeAlephConfiguration("$output/$p/aleph_header.txt");	

	echo "Zapis pliku $output/$p/background.txt ... \n";
	$aw->writeBackground("$output/$p/background.txt");	
	
	foreach ($relations as $r){
		echo "Zapis relacji '$r' ... \n";
		$aw->writePositiveRelations("$output/$p/relation_$r.f", array($r));
		$aw->writeNegativeRelations("$output/$p/relation_$r.n", array($r));
	}
}
echo "3. Gotowe.";


?>

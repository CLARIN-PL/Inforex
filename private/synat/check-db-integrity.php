<?

require_once("PEAR.php");
require_once("MDB2.php");
$config = null;
$config->dsn = array(
    			'phptype'  => 'mysql',
    			'username' => 'root',
    			'password' => 'sql',
    			'hostspec' => 'localhost',
    			'database' => 'gpw',
				);
include("../../engine/database.php");


//check annotations/tokens boundary conflict
$sql = "SELECT a.report_id " .
		"FROM reports_annotations a " .
		"JOIN tokens t " .
		"ON (a.report_id = t.report_id " .
		"AND ( " .
			"(a.from>t.from AND a.from<t.to ) " .
			"OR (a.to>t.from AND a.to<t.to) )" .
		") GROUP BY a.report_id"; 
$results = db_fetch_rows($sql); 

if (!empty($results)){
	print "* Annotations/tokens boundary conflicts: ";
	foreach ($results as $result)
		print $result['report_id'] . " ";
	print "\n\n"; 
}	


//new tests:
//- 

?>
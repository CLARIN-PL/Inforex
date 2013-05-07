<?php

/*
 * Created on Jul 27, 2012
 */

global $config;
include ("../cliopt.php");
include ("../../engine/config.local.php");
include ("../../engine/include.php");

mb_internal_encoding("UTF-8");

//--------------------------------------------------------
//configure parameters
$opt = new Cliopt();
$opt->addExecute("php relations-report.php --db-name xxx --db-user xxx --db-pass xxx --db-host xxx --db-port xxx -f yyy", null);
$opt->addParameter(new ClioptParameter("db-uri", "u", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("db-host", null, "host", "database address"));
$opt->addParameter(new ClioptParameter("db-port", null, "port", "database port"));
$opt->addParameter(new ClioptParameter("db-user", null, "user", "database user name"));
$opt->addParameter(new ClioptParameter("db-pass", null, "password", "database user password"));
$opt->addParameter(new ClioptParameter("db-name", null, "name", "database name"));
$opt->addParameter(new ClioptParameter("folder", "f", "folder", "folder with files to upload"));

$config = null;
try {
	$opt->parseCli($argv);

	$dbUser = $opt->getOptional("db-user", "sql");
	$dbPass = $opt->getOptional("db-pass", "sql");
	$dbHost = $opt->getOptional("db-host", "localhost") . ":" . $opt->getOptional("db-port", "3306");
	$dbName = $opt->getOptional("db-name", "sql");

	if ($opt->exists("db-uri")) {
		$uri = $opt->getRequired("db-uri");
		if (preg_match("/(.+):(.+)@(.*)\/(.*)/", $uri, $m)) {
			$dbUser = $m[1];
			$dbPass = $m[2];
			$dbHost = $m[3];
			$dbName = $m[4];
		} else {
			throw new Exception("DB URI is incorrect. Given '$uri', but exptected 'user:pass@host:port/name'");
		}
	}

	$config->dsn = array (
		'phptype' => 'mysql',
		'username' => $dbUser,
		'password' => $dbPass,
		'hostspec' => $dbHost,
		'database' => $dbName
	);
	$config->folder = $opt->getRequired("folder");
} catch (Exception $ex) {
	print "!! " . $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

//--------------------------------------------------------
//main function
function main($config) {
	$db = new Database($config->dsn);
	
	$sql = "INSERT INTO texts(type, content, status) VALUES('text', ?, 'new')";
	
	if ($handle = opendir($config->folder)){
		while ( false !== ($file = readdir($handle))){
			if ($file != "." && $file != ".."){
				$content = file_get_contents($config->folder . "/" . $file);
				$db->execute($sql, array($content));
			}
		}
	}

}

//--------------------------------------------------------
//main invoke
main($config);
?>
 
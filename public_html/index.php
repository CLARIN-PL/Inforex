<?php

ob_start();

/********************************************************************8
 * Dołącz pliki.
 */
/* Wczytaj obiekt konfiguracji */
require_once("../engine/config.php");
$config = new Config();

/* Nadpisz domyślną konfigurację przez lokalną konfigurację. */
if (!file_exists("../engine/config.local.php"))
	die("<center><b><code>config-local.php</code> file not found!</b><br/> Create it and set up the configuration of <i>Inforex</i>.</center>");
else
	require_once("../engine/config.local.php");

/* Dołącz wszystkie biblioteki */
require_once($config->path_engine . '/include.php');


/********************************************************************8
 * Połączenie z bazą danych (stary sposób, tylko na potrzeby web)
 */

ob_start();
$options = array(
    'debug' => 2,
    'result_buffering' => false,
);

$mdb2 =& MDB2::singleton($config->dsn, $options);

if (PEAR::isError($mdb2)) {
    die($mdb2->getMessage());
}
$mdb2->loadModule('Extended');
$mdb2->loadModule('TableBrowser');
db_execute("SET CHARACTER SET 'utf8'");
db_execute("SET NAMES 'utf8'");
ob_clean();
/********************************************************************/


$p = new InforexWeb();
$db = new Database($config->dsn);

$auth = new UserAuthorize($config->dsn);
$auth->authorize($_POST['logout']=="1");
$user = $auth->getUserData();
$corpus = RequestLoader::loadCorpus();

chdir("../engine"); /* Ugly hack for Smarty */
$p->execute();

print trim(ob_get_clean());

?>

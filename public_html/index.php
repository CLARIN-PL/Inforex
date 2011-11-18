<?php

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

$auth = new UserAuthorize($config->dsn);
$auth->authorize($_POST['logout']=="1");
$user = $auth->getUserData();
$corpus = RequestLoader::loadCorpus();

$p = new InforexWeb();

chdir("../engine"); /* Temporal hack for Smarty */
$p->execute();

?>

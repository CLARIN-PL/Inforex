<?php
chdir("pear");

require_once($conf_global_path . '/include/Smarty-2.6.22/libs/Smarty.class.php');
require_once("PEAR.php");
require_once("MDB2.php");
require_once('HTTP/Session2.php');
require_once('HTML/Select.php');
//require_once('FirePHPCore/fb.php');
require_once('Auth/Auth.php');

require_once($conf_global_path . '/include/anntakipi/ixtTakipiReader.php');
require_once($conf_global_path . '/include/anntakipi/ixtTakipiDocument.php');

require_once($conf_global_path . '/include/CPage.php');
require_once($conf_global_path . '/include/CAction.php');
require_once($conf_global_path . '/include/CTextAligner.php');
require_once($conf_global_path . '/include/CTeiFormater.php');

require_once($conf_global_path . '/include/report_reformat.php');
require_once($conf_global_path . '/include/ner_filter.php');

chdir("..");

require_once($conf_global_path . '/include/database/include.list.php');

?>

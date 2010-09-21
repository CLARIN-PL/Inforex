<?php
chdir("pear");

require_once($config->path_engine . '/include/Smarty-2.6.22/libs/Smarty.class.php');
require_once($config->path_engine . '/include/pear/HTML/Select.php'); // PEAR module with local changes
require_once($config->path_engine . '/include/pear/FirePHPCore/fb.php');
require_once("PEAR.php");
require_once("MDB2.php");
require_once('HTTP/Session2.php');
require_once('Auth/Auth.php');

require_once($config->path_engine . '/database.php');

require_once($config->path_engine . '/include/anntakipi/ixtTakipiReader.php');
require_once($config->path_engine . '/include/anntakipi/ixtTakipiDocument.php');
require_once($config->path_engine . '/include/anntakipi/ixtTakipiStruct.php');

require_once($config->path_engine . '/include/CPage.php');
require_once($config->path_engine . '/include/CAction.php');
require_once($config->path_engine . '/include/CTextAligner.php');
require_once($config->path_engine . '/include/CTeiFormater.php');

require_once($config->path_engine . '/include/report_reformat.php');
require_once($config->path_engine . '/include/ner_filter.php');
require_once($config->path_engine . '/include/lib_htmlstr.php');
require_once($config->path_engine . '/include/lib_htmlparser.php');
require_once($config->path_engine . '/include/lib_roles.php');

require_once($config->path_engine . '/include/class/a_table.php');
require_once($config->path_engine . '/include/class/c_report.php');
require_once($config->path_engine . '/include/class/c_corpus.php');

require_once($config->path_engine . '/include/utils/CUserActivity.php');

chdir("..");

require_once($config->path_engine . '/include/database/include.list.php');

?>
<?php
chdir("pear");

require_once($conf_global_path . '/include/Smarty-2.6.22/libs/Smarty.class.php');
require_once($conf_global_path . '/pear/PEAR.php');
require_once($conf_global_path . '/pear/MDB2.php');
require_once($conf_global_path . '/pear/HTTP/Session2.php');
require_once($conf_global_path . '/pear/HTML/Select.php');
require_once($conf_global_path . '/pear/FirePHPCore/fb.php');

require_once($conf_global_path . '/include/CPage.php');
require_once($conf_global_path . '/include/CAction.php');
require_once($conf_global_path . '/include/CTextAligner.php');
require_once($conf_global_path . '/include/CTeiFormater.php');

require_once($conf_global_path . '/include/report_reformat.php');

chdir("..");

require_once($conf_global_path . '/include/database/include.list.php');

?>

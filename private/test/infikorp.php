<?

mb_internal_encoding("UTF-8");

require_once("../../engine/config.php");
require_once("../../engine/config.local.php");
require_once("../../engine/include.php");
ob_end_clean();

$report = db_fetch("SELECT * FROM reports WHERE id=?", array(100524));
$tokens = db_fetch_rows("SELECT * FROM tokens WHERE report_id=?", array(100524));
$content = $report[content];

$htmlStr = new HtmlStr($content);

foreach ($tokens as $ann){
	try{
		print_r($ann);
		$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s:%d>", 0, "token", 0), $ann['to']+1, "</an>");	
	}catch(Exception $ex){
		print_r($htmlStr->getContent());	
	}
}


?>
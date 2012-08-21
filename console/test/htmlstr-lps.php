<?

mb_internal_encoding("UTF-8");

require_once("../../engine/config.php");
require_once("../../engine/config.local.php");
require_once("../../engine/include.php");
ob_end_clean();

$db = new Database($config->dsn);

//$report_id = 99884;
$config = null;
$config->report_id = 99893;
$config->add_annotations = true;
$config->parser = "HtmlStr";

for ( $i = 99885; $i < 99886; $i++){
	echo $i . "..";
	$row = DbReport::getReportById($config->report_id);
	$annotations = DbAnnotation::getAnnotationByReportId($config->report_id);
	$tokens = DbToken::getTokenByReportId($config->report_id);
	$htmlStr = new $config->parser($row['content']);
	
	if ( $config->add_annotations ){
		foreach ($annotations as $ann){
			try{
				$htmlStr->insertTag((int)$ann['from'], sprintf("<an id='%d:%s:%d'>", 0, $ann["type"] . ($ann['eos'] ? " eos" : ""), 0), $ann['to']+1, "</an>");		
			}
			catch (Exception $ex){
				//fb($ex);	
			}
		}
		
		foreach ($tokens as $ann){
			try{
				$htmlStr->insertTag((int)$ann['from'], sprintf("<tok id='%d:%s:%d'>", $ann['token_id'], "token" . ($ann['eos'] ? " eos" : ""), 0), $ann['to']+1, "</T>");		
			}
			catch (Exception $ex){
				//fb($ex);	
			}
		}
	}

	echo $htmlStr->getContent();

}

?>
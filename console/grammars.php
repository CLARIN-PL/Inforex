<?
ini_set("error_reporting", E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set("display_errors", 1);
ini_set("output_buffering", 0);
mb_internal_encoding("UTF-8");

/********************************************************************8
 * Dołącz pliki.
 */
// Wczytanie konfiguracji skryptu
require_once("../engine/config.php");

if (!file_exists("../engine/config.local.php"))
	die("<center><b><code>config-local.php</code> file not found!</b><br/> Create it and set up the configuration of <i>Inforex</i>.</center>");
else
	require_once("../engine/config.local.php");

// Dołączenie podstawowych plików systemu
require_once($config->path_engine . '/include.php');
ob_end_flush();

/********************************************************************/
class GrammarExtractor{

	var $reports = null;

	// Wczytuje dane do przetworzenia
	function load(){
		// Pobierz raporty z korpusu Infinity
		$reports = db_fetch_rows("SELECT * FROM reports WHERE corpora = 7");

		foreach ($reports as $k=>$v){
			echo ".";

			$sql = "SELECT id, type, `from`, `to`, `to`-`from` AS len, text" .
					" FROM reports_annotations an" .
					" LEFT JOIN annotation_types t ON (an.type=t.name)" .
					" WHERE report_id = {$v['id']}" .
					"   AND t.name != 'facility'" .
					"   AND t.name != 'product'" .
					"   AND t.name != 'timex'" .
					"   AND t.name != 'event'" .
					" ORDER BY `from` ASC, `level` DESC";
			$anns = db_fetch_rows($sql);
		
			$htmlStr = new HtmlStr($v['content'], true);
			foreach ($anns as $ann){
				try{
					$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s>", $ann['id'], $ann['type']), $ann['to']+1, "</an>");
				}catch (Exception $ex){
					print_r($ex);
					die();
				}
			}	
			$reports[$k]['content'] = $htmlStr->getContent();		
		}
		
		$this->reports = $reports;
	}	
	
	function serialize($filename){
		file_put_contents($filename, json_encode($this->reports));
	}
		
	function deserialize($filename){
		$this->reports = json_decode(file_get_contents($filename));
	}
	
	function reformat(){		
		foreach ($this->reports as $k=>$v){
			$content = $v->content;
			$content = str_replace("<!DOCTYPE cesAna SYSTEM \"xcesAnaIPI.dtd\">", "", $content);
			$content = str_replace("<?xml version=\"1.0\" encoding=\"UTF-8\"?>", "", $content);			
			$content = str_replace("xmlns:", "", $content);			
			$content = str_replace("xml:", "", $content);			
			$content = preg_replace("/<an#([0-9]+):([a-z_]+)>/", '<an type="$2">', $content);
			$this->reports[$k]->content = $content;
		}
	}
	
	function harvest($annotation_names){
		$items = array();
		
		//foreach( $xml->xpath('//an[@type="person" or @type="location" or @type="geopolitical" or @type="organization"]') as $item ) {
		$conds = array();
		foreach ($annotation_names as $n) $conds[] = '@type="'.$n.'"';
			
		foreach ($this->reports as $k=>$v){		
			$xml = new SimpleXMLElement($v->content);
			
			foreach( $xml->xpath('//an['.implode(" or ", $conds).']') as $item ) {
				$xml = $item->asXML();
				$text = preg_replace('/^<an type=\"[a-z_]+\">(.*)<\/an>$/', '$1', $xml);
				do{
					$before = $text;
					$text = preg_replace_callback('/<an type="([a-z_]+)">([^<]*?)<\/an>/', 
										create_function(
								            // single quotes are essential here,
								            // or alternative escape all $ as \$
								            '$matches',
								            'return strtoupper($matches[1]);'
								        )
										, $text);
				}while ($before != $text);
			    $items[] = $text;
			}
		}
		return $items;
	}
}
/********************************************************************/

$gr = new GrammarExtractor();
//$gr->load();
//$gr->serialize("grammars-data.txt");
$gr->deserialize("grammars-data.txt");
$gr->reformat();

file_put_contents("synat_person.txt", implode("\n", $gr->harvest(array("person"))));
file_put_contents("synat_location.txt", implode("\n", $gr->harvest(array("location", "geopolitical"))));
file_put_contents("synat_organization.txt", implode("\n", $gr->harvest(array("organization"))));

?>
<?
/**
 * 
 */
global $config;
include("../cliopt.php");
//include("../../engine/config.php");
include("../../engine/config.local.php");
include("../../engine/include.php");
ob_end_clean();

mb_internal_encoding("UTF-8");

$sets = array();
$sets['heads'] = "chunk_head_agp;chunk_head_vp;chunk_head_np;chunk_head_adjp";

//--------------------------------------------------------
//configure parameters
$opt = new Cliopt();
$opt->addExecute("php import-chunks.php -u user:pass@host:port/name -f path",null);
$opt->addParameter(new ClioptParameter("annotation", "a", "chunk_name", "annotation name to import"));
$opt->addParameter(new ClioptParameter("annotationSet", "s", "{".implode(array_keys($sets), ",")."}", "predefined groups of annotations to import"));
$opt->addParameter(new ClioptParameter("annotationGroup", "g", "", "set of annotations to import"));
$opt->addParameter(new ClioptParameter("db-uri", "u", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("folder", "f", "path", "path to folder where generated CCL files will be saved"));

//get parameters & set db configuration
$config = null;
try {
	$opt->parseCli($argv);
	
	$dbUser = $opt->getOptional("db-user", "root");
	$dbPass = $opt->getOptional("db-pass", "sql");
	$dbHost = $opt->getOptional("db-host", "localhost") . ":" . $opt->getOptional("db-port", "3306");
	$dbName = $opt->getOptional("db-name", "gpw");
	
	if ( $opt->exists("db-uri")){
		$uri = $opt->getRequired("db-uri");
		if ( preg_match("/(.+):(.+)@(.*)\/(.*)/", $uri, $m)){
			$dbUser = $m[1];
			$dbPass = $m[2];
			$dbHost = $m[3];
			$dbName = $m[4];
		}else{
			throw new Exception("DB URI is incorrect. Given '$uri', but exptected 'user:pass@host:port/name'");
		}
	}
		
	$config->dsn = array(
	    			'phptype'  => 'mysql',
	    			'username' => $dbUser,
	    			'password' => $dbPass,
	    			'hostspec' => $dbHost,
	    			'database' => $dbName);		    			
	    			
	$config->folder = $opt->getRequired("folder");	
	$config->chunks = $opt->getParameters("annotation");
	$config->groups = $opt->getParameters("annotationGroup");
	$config->sets = $opt->getParameters("annotationSet");
	
} 
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

//--------------------------------------------------------
function print_chunks_configuration($chunks){
	echo "=== Chunks to import ===\n";
	foreach ($chunks as $k=>$v)
		echo "- $k => $v\n";
}

function transform_chunks($chunks){
	$ch = array();
	foreach ($chunks as $chunk){
		$parts = explode(":", $chunk);
		$ch[$parts[0]] = count($parts) == 1 ? $parts[0] : $parts[1];
	}
	return $ch;
}

function get_cobe_chunks($ccl){
	$offset = 0;
	$chunks = array();
	foreach ($ccl->chunks as $chunk){
		foreach ($chunk->sentences as $sentence){
			/* Pomiń puste zdania o ile się zdażą */
			if ( count($sentence->tokens) == 0 )
				continue;
				
			$channels = array_keys($sentence->tokens[0]->channels);
			$end = $offset;
			
			foreach ($channels as $ch){
				$current = $offset;
				$last = 0;
				$begin = 0;
				foreach ($sentence->tokens as $token){
					$ann = $token->channels[$ch];
					
					/* Sprawdź, czy utworzyć nową anotację */
					if ($ann <> $last && $last > 0){
						$chunks[$ch][] = array($begin,$current-1);
						$begin = 0;
						$end = 0;
						$last = 0;
					}
					
					/* Sprawdź, czy utworzyć nowe śledzenie */
					if ($ann <> $last && $ann > 0){
						$begin = $current;
						$end = $current;
						$last = $ann;
					}
					
					$current += mb_strlen(htmlspecialchars_decode($token->orth));					
				}
				if ($last>0){
					$chunks[$ch][] = array($begin,$current-1);
				}
			}
			
			/* Zmodyfikuj offset początku następnego zdania */
			foreach ($sentence->tokens as $token)
				$offset += mb_strlen(htmlspecialchars_decode($token->orth));
		}
	}
	return $chunks;
}

//--------------------------------------------------------
$db = new Database($config->dsn);

if ($config->sets){
	foreach ($config->sets as $s)
	   $config->chunks = array_merge($config->chunks, explode(";", $sets[$s]));
}

if ($config->groups){
	foreach ($config->groups as $g){
		foreach ( DbAnnotation::getAnnotationTypesByGroupId($g) as $an )
		$config->chunks[] = $an['name']; 
	}
}

$chunks = transform_chunks($config->chunks);
print_chunks_configuration($chunks);

$files = FolderReader::readFilesFromFolder($config->folder);

foreach ($files as $file){
	echo "Plik $file ... ";
	
	$report_id = intval(basename($file, ".xml"));
	if ( $report_id > 0 )
		echo "id=$report_id";
	else{
		echo " brak id !!\n";
		continue;
	}
	
	$r = DbReport::getReportById($report_id);
	if ( !$r){
		echo " raport nie znaleziony w bazie !!\n";
		continue;
	}
	
	$ccl = WcclReader::readDomFile($file);
	$cobe_chunks = get_cobe_chunks($ccl);
	$htmlStr = new HtmlStr($r['content']);
	$chunksToInset = array();
	foreach ($chunks as $k=>$v){
		if ( isset($cobe_chunks[$k]) ){
			foreach ($cobe_chunks[$k] as $be){
				$text = $htmlStr->getText($be[0], $be[1]);
				$chunksToInset[] = array(
					"type" => $k,
					"from" => $be[0],
					"to" => $be[1],
					"text" => $text
				);
			}
		}
	}
	
	DbAnnotation::deleteReportAnnotationsByType($report_id, array_values($chunks));
	
	foreach ($chunksToInset as $c){
		$a = new CReportAnnotation();
		$a->setType($c['type']);
		$a->setFrom($c['from']);
		$a->setTo($c['to']);
		$a->setText($c['text']);
		$a->setReportId($report_id);
		$a->setCreationTime(date("Y-m-d H:i:s"));
		$a->setStage('final');
		$a->setSource('bootstrapping');
		$a->setUserId(1);
		$a->save();
	}
	
	echo "\n";
}

?>

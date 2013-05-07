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
$opt->addParameter(new ClioptParameter("relation_type", "r", "type", "type of dump relation"));
$opt->addParameter(new ClioptParameter("source_type", "s", "type", "name of annotation source type"));
$opt->addParameter(new ClioptParameter("target_type", "t", "type", "name of target source type"));
$opt->addParameter(new ClioptParameter("file_name", "f", "file", "out file name"));

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
	$config->relations = $opt->getParameters("relation_type");
	$config->source_types = $opt->getParameters("source_type");
	$config->target_types = $opt->getParameters("target_type");
	$config->file_name = $opt->getRequired("file_name");
} catch (Exception $ex) {
	print "!! " . $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

//--------------------------------------------------------
//main function
function main($config) {
	$db = new Database($config->dsn);
	$GLOBALS['db'] = $db;

	$where = array ();

	if (where_or('r_type.type', $config->relations) != "")
		$where[] = where_or('r_type.type', $config->relations);
	if (where_or('ans_type.type', $config->source_types) != "")
		$where[] = where_or('ans_type.type', $config->source_types);
	if (where_or('ant_type.type', $config->target_types) != "")
		$where[] = where_or('ant_type.type', $config->target_types);

	$sql = " SELECT ans.text as source_text, " .
	" ans.base as source_base, " .
	" ans.begin as source_begin, " .
	" ans.end as source_end, " .
	" ans_type.type as source_type, " .
	" ant.text as target_text, " .
	" ant.base as target_base, " .
	" ant.begin as target_begin, " .
	" ant.end as target_end, " .
	" ant_type.type as target_type, " .
	" r_type.type as relation_type, " .
	" r.sentence_begin, " .
	" r.sentence_end, " .
	" t.content, " .
	" t.text_id " .
	" FROM relations r " .
	" JOIN annotations ans ON r.annotation_source_id = ans.annotation_id " .
	" JOIN annotations ant ON r.annotation_target_id = ant.annotation_id " .
	" JOIN annotation_types ans_type ON ans.annotation_type_id = ans_type.annotation_type_id " .
	" JOIN annotation_types ant_type ON ant.annotation_type_id = ant_type.annotation_type_id " .
	" JOIN relation_types r_type ON r.relation_type_id = r_type.relation_type_id " .
	" JOIN texts t ON t.text_id = r.text_id " .
	 (count($where) ? " WHERE (" . implode(" AND ", $where) . ")" : "") .
	" ORDER BY t.text_id";

	$result = $db->fetch_rows($sql);

	$f = fopen($config->file_name, "w");
	fwrite($f, init_html());
	$n = 0;
	$text_str = null;
	$text_str_id = null;
	
	foreach ($result as $relation) {
		echo "\r [ " . (++ $n) . " / " . count($result) . " ]";
		try {
			$html = "<tr>\n";
			$html .= "\t<td>" . $n . "</td>\n";
			$html .= "\t<td>" . $relation['source_text'] . "</td>\n";
			$html .= "\t<td>" . $relation['source_base'] . "</td>\n";
			$html .= "\t<td>" . $relation['source_type'] . "</td>\n";
			$html .= "\t<td>" . $relation['target_text'] . "</td>\n";
			$html .= "\t<td>" . $relation['target_base'] . "</td>\n";
			$html .= "\t<td>" . $relation['target_type'] . "</td>\n";
			$html .= "\t<td>" . $relation['relation_type'] . "</td>\n";

			$text_id = $relation['text_id'];

			if ( $text_str_id == null || $text_id != $text_str_id ){
				$text_str = new HtmlStr2(strip_tags($relation['content']));
				$text_str_id = $text_id;
			} 

			$sentence = $text_str->getText($relation['sentence_begin'], $relation['sentence_end']);
			$htmlStr2 = new HtmlStr2($sentence);
			if ($relation['source_end'] - $relation['source_begin'] > $relation['target_end'] - $relation['target_begin']){
				$htmlStr2->insertTag($relation['source_begin'] - $relation['sentence_begin'], '<span class=\'source\' title=\'' . $relation['source_type'] . '\'>', $relation['source_end'] - $relation['sentence_begin'] + 1, '</span>');
				$htmlStr2->insertTag($relation['target_begin'] - $relation['sentence_begin'], '<span class=\'target\' title=\'' . $relation['target_type'] . '\'>', $relation['target_end'] - $relation['sentence_begin'] + 1, '</span>');
			}
			else{
				$htmlStr2->insertTag($relation['target_begin'] - $relation['sentence_begin'], '<span class=\'target\' title=\'' . $relation['target_type'] . '\'>', $relation['target_end'] - $relation['sentence_begin'] + 1, '</span>');
				$htmlStr2->insertTag($relation['source_begin'] - $relation['sentence_begin'], '<span class=\'source\' title=\'' . $relation['source_type'] . '\'>', $relation['source_end'] - $relation['sentence_begin'] + 1, '</span>');				
			}
			$html .= "\t<td>" . $htmlStr2->getContent() . "</td>\n";

			$html .= "</tr>\n";
			fwrite($f, $html);
		} catch (Exception $ex) {
			echo "\n---------------------------\n";
			echo "!! Exception !! \n";
			echo $ex->getMessage();
			echo "\n";
			var_dump($relation);
			echo "\n---------------------------\n";
		}
	}

	fwrite($f, end_html());
	fclose($f);
}

function where_or($column, $values) {
	$ors = array ();
	foreach ($values as $value)
		$ors[] = "$column = '$value'";
	if (count($ors) > 0)
		return "(" . implode(" OR ", $ors) . ")";
	else
		return "";
}

function init_html(){
	$html = "<html>\n";
	$html .= "<head>\n";
	$html .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' . "\n";
	$html .= '<style type="text/css">' . "\n";
	$html .= 'body { font-size: 12px; }' . "\n";
	$html .= 'sub { color: #555; }' . "\n";
	$html .= 'span.source { border: 1px solid #1FCB4A; background: #BDF4CB } ' . "\n";
	$html .= 'span.target { border: 1px solid #FF4848; background: #FFDFDF } ' . "\n";
	$html .= 'table { background-color: #CDCDCD; }' . "\n";
	$html .= 'td { background-color: white}' . "\n";
	$html .= '</style>' . "\n";
	$html .= "</head>\n";
	$html .= "<body>\n";
	$html .= "<table>\n";
	$html .= "<thead>\n";
	$html .= " <tr>\n";
	$html .= "  <th>Lp.</th>\n";
	$html .= "  <th>tekst jednostki źródłowej</th>\n";
	$html .= "  <th>forma bazowej jednostki źródłowej</th>\n";
	$html .= "  <th>typ jednostki źródłowej</th>\n";
	$html .= "  <th>tekst jednostki docelowej</th>\n";
	$html .= "  <th>forma bazowej docelowej</th>\n";
	$html .= "  <th>typ jednostki docelowej</th>\n";
	$html .= "  <th>typ relacji</th>\n";
	$html .= "  <th>zdanie</th>\n";
	$html .= " </tr>\n";
	$html .= "</thead>\n";
	$html .= "<tbody>\n";
	return $html;
}

function end_html(){
	$html = "</tbody>\n";
	$html .= "</table>\n";
	$html .= "</body>\n";
	$html .= "</html>";
	return $html;
}

//--------------------------------------------------------
//main invoke
main($config);
?>
 
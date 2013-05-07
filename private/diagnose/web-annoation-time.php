<?
/* 
 * ---
 * Script estimates the time of corpus annotation. 
 * ---
 * Created on 2010-02-08
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */

require_once("../cliopt.php");
 
mysql_connect("nlp.pwr.wroc.pl:3308", "gpw", "gpw");
mysql_select_db("gpw");

$opt = new Cliopt();
$opt->addExecute("php web-annotation-time.php --corpus n",null);
$opt->addParameter(new ClioptParameter("corpus", "c", "corpus", "corpus id"));


//get parameters & set db configuration
$config = null;
try {
	$opt->parseCli($argv);
	$config->corpus_id = $opt->getRequired("corpus");
} 
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}


$sql = "SELECT a.creation_time" .
		" FROM reports_annotations a" .
		" JOIN reports r ON (r.id=a.report_id)" .
		" WHERE r.corpora = {$config->corpus_id}" .
//		"   AND (a.type = 'person_last_nam'" .
//		"        OR a.type = 'person_first_nam'" .
//		"        OR a.type = 'country_nam'" .
//		"        OR a.type = 'city_nam'" .
//		"        OR a.type = 'road_nam')" .
		" ORDER by creation_time ASC";

$single_annoation_time = 10;
$span_break_interval = 600;

$result = mysql_query($sql) or die(mysql_error());

$count = 0;
$total_count = 0;
$start_time = 0;
$prev_time = 0;
$total_time = 0;
$min = 0;
$max = 0;
$spans = 0;
$zeros = 0;

while ($row = mysql_fetch_array($result))
{
	$time = strtotime($row['creation_time']);
	if ($time < 0)
	{
		$zeros++;
	}
	elseif ($prev_time == 0)
	{
		$prev_time = $time;
		$start_time = $time;
		$count = 1;
		$min = $time;
		$max = $time;
	}
	else if  ( $time - $prev_time <= $span_break_interval)
	{
		$prev_time = $time;
		$count++;
	}
	else
	{
		$total_time += ( $prev_time - $start_time ) + ( $count == 1 ? $single_annoation_time : 0);
		if ($prev_time - $start_time)
			$spans++;
		$prev_time = $time;
		$start_time = $time;
		$count = 1;
	}
	$total_count++;
	$min = min($min, $time);
	$max = max($max, $time);
}
 
$t = $total_time *1.3;
$days = floor($t / (24*60*60));
$t = $t % (24*60*60);
$hours = floor($t / (60*60));
$t = $t % (60*60);
$minutes = floor($t / 60);
$second = $t % 60;

print sprintf("Period from %s to %s\n", date("Y-m-d H:i:s", $min), date("Y-m-d H:i:s", $max));
print sprintf("Annotations $total_count, zero time $zeros\n");
print sprintf("Total time: %d day(s), %d hour(s), %d minute(s) and %d second(s)\n", $days, $hours, $minutes, $second);
print sprintf("Number of spans: %d\n", $spans);  

?>
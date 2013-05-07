<?

$dryrun = true;

if (isset($argv[1]) && $argv[1]=="make")
	$dryrun = false;
else{
	print "This is only dry run.\nTo process run with 'php load-scans.php make' parameter\n";
}

$folder = "/home/czuk/nlp/workdir/lps/scans/images";
$target = "/home/czuk/nlp/eclipse/workspace/inforex_web/secured_data/images";

$files = array();

// Load scans
if (is_dir($folder)) {
    if ($dh = opendir($folder)) {
        while (($file = readdir($dh)) !== false) {
        	if ($file != ".." && $file!=".")
            	$files[] = $file;
        }
        closedir($dh);
    }
}

sort($files);

$files_by_names = array();
foreach ($files as $file){
	list($name, $tail) = explode("-", $file);
	$files_by_names[$name][] = $file;
}

if (!$dryrun){
	//mysql_connect("localhost", "root", "krasnal");
	//mysql_connect("nlp.pwr.wroc.pl:3308", "gpw", "gpw");
	mysql_select_db("gpw");
	mysql_query("SET CHARACTER SET utf8");
}

$count = 0;
foreach ($files_by_names as $name=>$files){
	$count++;
	$sql_report = sprintf("INSERT INTO reports (date, title, status, user_id, corpora) VALUES('%s', '%s', 2, 1, 3)", date("Y-m-d"), mysql_escape_string($name));
	if (!$dryrun)
		mysql_query($sql_report);
	else
		print $sql_report . "\n";
	$report_id = mysql_insert_id();
	
	$n = 1;
	foreach ($files as $file){
		$sql = sprintf("INSERT INTO images (corpus_id, original_name, hash_name) VALUES(3, '%s', '%s');", mysql_escape_string($file), mysql_escape_string($file));
		if (!$dryrun)
			mysql_query($sql);
		else
			print $sql . "\n";
		$image_id = mysql_insert_id();
		
		$sql = sprintf("INSERT INTO reports_and_images (report_id, image_id, position) VALUES(%d, %d, %d)", $report_id, $image_id, $n++);
		if (!$dryrun)
			mysql_query($sql);
		else
			print $sql . "\n";
			
		if (!$dryrun)
		{
			$image_name = $image_id."_".$file;
			copy("$folder/$file", "$target/$image_name");
		}
		
	}	
}

print "Number of scans: $count\n";
?>
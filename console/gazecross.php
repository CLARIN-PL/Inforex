<?
include("cliopt.php");
 
/******************** set configuration   *********************************************/
$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("gazetteer", "g", "type:file", "type and path to a plain gazetteer, i.e. PERSON:/home/person.txt"));

/******************** parse cli *********************************************/
$config = null;
 
try{
	$opt->parseCli($argv);
	$config->gaze = $opt->getParameters("gazetteer");
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/******************** process *********************************************/
function main($config)
{
	$names = array();
	$unique = array();
	foreach ($config->gaze as $gaze)
	{
		$parts = explode(":",$gaze);
		assert('count($parts)==2 /* Incorrect format, should be TYPE:location, not "'.$gaze.'" */' );
		list($type, $file) =  $parts;
		$lines = file($file);
		$names[$type] = array();
	

		foreach ($lines as $line)
		{
			if (substr($line, 0, 1) != '#'){
				$names[$type][$line] = 1;
				$unique[$line] = 1;
			}
		}
	}
	
	$cross = array();
	foreach ( array_keys($names) as $x)
	{
		foreach ( array_keys($names) as $y)
			$cross[$x][$y] = 0;
		$cross[$x]["UNAMB"] = 0;
	}
			
	// Calculate the croo table
	$number_of_names = 0;
	$unambiguous_names = 0;
	
	foreach(array_keys($unique) as $name){
		$number_of_names++;
		$name_categories = array(); 
		foreach (array_keys($names) as $group)
			if ( isset($names[$group][$name]) ){
				$name_categories[] = $group;
			}
		foreach ($name_categories as $x){
			foreach ($name_categories as $y)
				$cross[$x][$y]++;
		}
		if (count($name_categories)==1){
			$unambiguous_names++;
			$cross[$name_categories[0]]["UNAMB"]++;
		}
	}
	
	$columns = array_keys($names);
	$columns[] = "UNAMB";
	
	// Print cross table
	$cellwidth = 0;
	foreach (array_keys($names) as $name)
		$cellwidth = max($cellwidth, strlen($name));
	$header = array(str_pad("", $cellwidth, " "));
	foreach ($columns as $name)
		$header[] = str_pad($name, $cellwidth, " ", STR_PAD_LEFT);
	print implode(" | ", $header)."\n";
	foreach (array_keys($cross) as $name){
		$row = array(str_pad($name, $cellwidth, " ", STR_PAD_LEFT));
		foreach ($columns as $col){
			$row[] = str_pad($cross[$name][$col], $cellwidth, " ", STR_PAD_LEFT);
		}
		print implode(" | ", $row)."\n";
	}
	echo sprintf("Percent of unambiguous names: %2.2f\n", $unambiguous_names/$number_of_names*100); 
}

/******************** process *********************************************/
main($config);
?>
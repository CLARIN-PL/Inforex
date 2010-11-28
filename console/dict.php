<?
include("/nlp/eclipse/workspace_inforex/inforex_web/console/cliopt.php");

/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addArgument(new ClioptArgument("action", "type of action", array("unambiguous")));
$opt->addParameter(new ClioptParameter("dict", "d", "filename", "path to a dictionary"));


try{
	$opt->parseCli($argv);

	$action = $opt->getArgument(0);
	$dicts = $opt->getParameters("dict");

}catch(Exception $ex){
	die("\n!! ". $ex->getMessage() . " !!\n\n" . $opt->printHelp());
}

/******************** do the stuff   *********************************************/

if ( $action == "unamibuous" )
{
	$unamiguous = array();
	$names = array();
	foreach ($dicts as $dict)
		$names[$dict] = file($dict);
	foreach ($dicts as $name=>$dict){
		$names = $dict;
		foreach ($dicts as $name_remove=>$dict_remove)
			if ( $name != $name_remove )
				$names = array_diff($names, $dict_remove);
		$unamiguous[$name] = $names;
	}	
	foreach ($unamiguous as $name=>$dict)
		file_put_contents(str_replace(".txt", ".unambiguoug", $name), implode("\n", $dict));
		
}

?>
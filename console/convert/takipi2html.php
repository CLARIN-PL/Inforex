<?php
include("../cliopt.php");
include("../../engine/include/anntakipi/ixtTakipiReader.php"); 
include("../../engine/include/anntakipi/ixtTakipiDocument.php"); 
include("../../engine/include/anntakipi/ixtTakipiStruct.php"); 

$opt = new Cliopt();
$opt->addArgument(new ClioptArgument("takipi", 'location of iob file to read'));
$opt->addArgument(new ClioptArgument("html", 'location of file where to save result'));
$opt->addParameter(new ClioptParameter("skip-no-annotation", "s", null, "set to skip sentences without an annotation"));

$config = null;

try{
	$opt->parseCli($argv);
	$config->input = $opt->getArgument(0);
	$config->output = $opt->getArgument(1);
	$config->skip = $opt->exists("skip-no-annotation");
		
}catch(Exception $ex){
	print "\n!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

$doc = TakipiReader::createDocument($config->input);

$f = fopen($config->output, "w");
fwrite($f, "<html>");
fwrite($f, "<head>");
fwrite($f, '<meta content="text/html; charset=utf-8" http-equiv="content-type">');
fwrite($f, "</head>");
fwrite($f, "<body>");

foreach ($doc->sentences as $sentence)
{
	$annotation = false;
	$senstr = "";
	$annotation_count = 0;
	$senstr .= '<div>';
	foreach ($sentence->tokens as $token)
	{
		if (count($token->channels))
		{
			$key = array_pop(array_keys($token->channels));
			if ($token->channels[$key] == "B")
			{
				$annotation_count++;
				if ($annotation)
					$senstr .= "</span> ";
				else
					$senstr .= " ";
				
				if ($key=="PERSON_LAST_NAM")
					$senstr .= "<span style='background: yellow' label='$key'>";
				elseif($key=="PERSON_FIRST_NAM")
					$senstr .= "<span style='background: green' label='$key'>";
				elseif($key=="COUNTRY_NAM")
					$senstr .= "<span style='background: blue' label='$key'>";
				elseif($key=="CITY_NAM")
					$senstr .= "<span style='background: orange' label='$key'>";
				else
					$senstr .= "<span style='background: red' label='$key'>";
					
				$annotation = true;
				echo $key."\n";
			}
			elseif ($token->channels[$key] == "O" && $annotation)
			{
				$senstr .= "</span> ";
				$annotation = false;
				$space = true;
			}
			else
				$senstr .= " ";			

			$senstr .= $token->orth;			
		}
		else
			$senstr .= $token->orth." ";
	}
	$senstr .= '</div>'."\n<hr>\n";
	
	if ($annotation_count > 0 || !$config->skip)
		fwrite($f, $senstr);
	else
		print "skipped\n";
}
fwrite($f, "</body>");
fwrite($f, "</html>");
fclose($f);

?>
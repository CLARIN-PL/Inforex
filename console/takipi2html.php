<?php
include("cliopt.php");
include("../engine/include/anntakipi/ixtTakipiReader.php"); 
include("../engine/include/anntakipi/ixtTakipiDocument.php"); 
include("../engine/include/anntakipi/ixtTakipiStruct.php"); 

$opt = new Cliopt();
$opt->addArgument(new ClioptArgument("takipi", 'location of iob file to read'));
$opt->addArgument(new ClioptArgument("html", 'location of file where to save result'));

$config = null;

try{
	$opt->parseCli($argv);
	$config->input = $opt->getArgument(0);
	$config->output = $opt->getArgument(1);
		
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
	fwrite($f, '<div>');
	foreach ($sentence->tokens as $token)
	{
		if (count($token->channels))
		{
			$key = array_pop(array_keys($token->channels));
			if ($token->channels[$key] == "B")
			{
				if ($annotation)
					fwrite($f, "</span> ");
				else
					fwrite($f, " ");
				
				if ($key=="PERSON_LAST_NAM")
					fwrite($f, "<span style='background: yellow' label='$key'>");
				elseif($key=="PERSON_FIRST_NAM")
					fwrite($f, "<span style='background: green' label='$key'>");
				elseif($key=="COUNTRY_NAM")
					fwrite($f, "<span style='background: blue' label='$key'>");
				elseif($key=="CITY_NAM")
					fwrite($f, "<span style='background: orange' label='$key'>");
				else
					fwrite($f, "<span style='background: red' label='$key'>");
					
				$annotation = true;
				echo $key."\n";
			}
			elseif ($token->channels[$key] == "O" && $annotation)
			{
				fwrite($f, "</span> ");
				$annotation = false;
				$space = true;
			}
			else
				fwrite($f, " ");			

			fwrite($f, $token->orth);			
		}
		else
			fwrite($f, $token->orth." ");
	}
	fwrite($f, '</div>'."\n<hr>\n");
}
fwrite($f, "</body>");
fwrite($f, "</html>");
fclose($f);

?>
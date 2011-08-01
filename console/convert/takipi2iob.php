<?php
include("../cliopt.php");
include("../../engine/include/anntakipi/ixtTakipiReader.php"); 
include("../../engine/include/anntakipi/ixtTakipiDocument.php"); 
include("../../engine/include/anntakipi/ixtTakipiStruct.php"); 

$opt = new Cliopt();
$opt->addArgument(new ClioptArgument("takipi", 'location of iob file to read'));
$opt->addArgument(new ClioptArgument("iob", 'location of file where to save result'));
//$opt->addParameter(new ClioptParameter("skip-no-annotation", "s", null, "set to skip sentences without an annotation"));

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


$skipped = 0;
$multiple = 0;
$f = fopen($config->output, "w");
$reader = new TakipiReader();
$reader->loadFile($config->input);

while ( ( $sentence = $reader->readSentence()) !== false )
{
	$sentenceStr = "";
	$count = 0;
	foreach ($sentence->tokens as $token)
	{
		$senstr = $token->orth . " ";			
		if (count($token->channels))
		{
			$key = array_pop(array_keys($token->channels));
			if ($token->channels[$key] == "B" || $token->channels[$key] == "I" )
				$senstr .= $token->channels[$key]."-".$key;
			else
				$senstr .= "O";
			if ($token->channels[$key] == "B")
				$count++;
		}
		else
			$senstr .= "O";
		$sentenceStr .= $senstr . "\n";
	}
	if ($count)
		fwrite($f, $sentenceStr . "\n");
	else 
		$skipped++;
}

$reader->close();
fclose($f);

print "\nSkipped $skipped sentence(s)\n";
?>
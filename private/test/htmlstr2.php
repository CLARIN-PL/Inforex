<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Adam Kaczmarek, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

/**
 * 
 */

$engine = realpath(dirname(__FILE__) . "/../../engine/");

require($engine . "/include/database/CDbReport.php");

include($engine . "/config.php");
include($engine . "/config.local.php");
include($engine . "/include.php");
include($engine . "/cliopt.php");


function getParameters($argv){
	$params = null;
	$opt = new Cliopt();
	$opt->addParameter(new ClioptParameter("document", "d", "ID", "document id"));

	try{
		$opt->parseCli($argv);		
		$params->document_id = $opt->getRequired("document");
		
		return $params;
		
	}catch(Exception $ex){
		print "!! ". $ex->getMessage() . " !!\n\n";
		$opt->printHelp();
		die("\n");
	}	
}

function runScript($argv){
	global $config;
	$params = getParameters($argv);

	$GLOBALS['db'] = $db = new Database($config->get_dsn());

	$report = new CReport($params->document_id);
		
	$htmlStr = new HtmlStr2($report->content);	

//	$tokens = DbToken::getTokenByReportId($params->document_id);
	
//	print count($tokens) . "\n";

//	foreach ($tokens as $ann){
//		$tag_open = sprintf("<an#%d:%s:%d>", $ann['token_id'], "token" . ($ann['eos'] ? " eos" : ""), 0);
//		$tag_close = '</an>';
//		try{					
//			$htmlStr->insertTag((int)$ann['from'], sprintf("<an#%d:%s:%d>", 0, "token" . ($ann['eos'] ? " eos" : ""), 0), $ann['to']+1, "</an>", true);			
//		}
//		catch (Exception $ex){
//			$token_exceptions[] = sprintf("Token '%s' is crossing an annotation. Verify the annotations.", htmlentities($tag_open));
//
//			for ( $i = $ann['from']; $i<=$ann['to']; $i++){
//				try{
//					$htmlStr->insertTag($i, "<b class='invalid_border_token' title='{$ann['from']}'>", $i+1, "</b>");
//				}catch(Exception $exHtml){
//					$token_exceptions[] = $exHtml->getMessage();
//				}
//			}											
//		}
//	}	
	
	//print $htmlStr->getContent();
}


runScript($argv);

?>

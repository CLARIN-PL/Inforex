<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class TeiFormater{

	static function corpus_header(){
		$xml = '<teiHeader xml:lang="pl" type="corpus">'."\n";
		$xml .='  <profileDesc>'."\n";
		$xml .='    <langUsage>'."\n";
		$xml .='      <language ident="pl">Polish</language>'."\n";
		$xml .='    </langUsage>'."\n";
		$xml .='  </profileDesc>'."\n";
		$xml .='  <fileDesc>'."\n";
		$xml .= '    <titleStmt>'."\n";
		$xml .= '      <title>Korpus raportów giełdowych z 2004 roku</title>'."\n";
 		$xml .= '    </titleStmt>'."\n";
		$xml .= '    <sourceDesc>'."\n";
		$xml .= '      <bibl>'."\n";
		$xml .= '        <name>GPWInfoStrefa</name>'."\n";		
		$xml .= '        <ptr target="http://www.gpwinfostrefa.pl"/>'."\n";
		$xml .= '      </bibl>'."\n";
 		$xml .= '    </sourceDesc>'."\n";
 		$xml .= '    <respStmt>'."\n";
 		$xml .= '      <resp>dokumenty zebrał i przetworzył</resp>'."\n";
 		$xml .= '      <persName xml:id="michalm">Michał Marcińczuk</persName>'."\n";
 		$xml .= '    </respStmt>'."\n"; 		
		$xml .='  </fileDesc>'."\n";
		$xml .='  <revisionDesc>'."\n";
		$xml .='    <change who="#michalm" when="'.date("Y-m-d").'">Utworzenie korpusu.</change>'."\n";
		$xml .='  </revisionDesc>'."\n";
		$xml .='</teiHeader>';		
		return $xml;
	}
	
	static function report_to_header($report_row){
		$xml = '<teiHeader>'."\n";
		$xml .= '  <fileDesc>'."\n";
		$xml .= '    <titleStmt>'."\n";
		$xml .= '      <title>@TITLE@</title>'."\n";
 		$xml .= '    </titleStmt>'."\n";
		$xml .= '    <sourceDesc>'."\n";
		$xml .= '      <bibl>'."\n";
		$xml .= '        <ptr target="@LINK@"/>'."\n";
		$xml .= '      </bibl>'."\n";
 		$xml .= '    </sourceDesc>'."\n";
		$xml .= '  </fileDesc>'."\n";
//		$xml .= '  <profileDesc>'."\n";
//		$xml .= '    todo'."\n";
//		$xml .= '  </profileDesc>'."\n";
		$xml .= '  <publicationStmt>'."\n";
		$xml .= '    <distributor>@COMPANY@</distributor>'."\n";
		$xml .= '    <date when="@DATE@">@DATE@</date>'."\n";		
		$xml .= '  </publicationStmt>'."\n";
		$xml .= '</teiHeader>';
		
		$replace = array();
		$replace["@NUMBER@"] = $report_row['number'];
		$replace["@TITLE@"] = $report_row['title'];
		$replace["@LINK@"] = $report_row['link'];
		$replace["@DATE@"] = $report_row['date'];
		$replace["@COMPANY@"] = $report_row['company'];
		
		$xml = str_replace(array_keys($replace), array_values($replace), $xml);
		
		return $xml;
	}
	
	static function report_to_text($report_row, $corpus_header_name="no_header.xml"){				
		$xml = '<teiCorpus'."\n";
		$xml .= ' xmlns:xi="http://www.w3.org/2001/XInclude"'."\n";
		$xml .= ' xmlns="http://www.tei-c.org/ns/1.0">'."\n";
		$xml .= '  <xi:include href="'.$corpus_header_name.'"/>'."\n";
		$xml .= '  <TEI>'."\n";
		$xml .= '    <xi:include href="header.xml"/>'."\n";
		$xml .= '    <text xml:id="struct_text">'."\n";
		$xml .= '      <body>'."\n";
		$xml .= TeiFormater::report_body($report_row['content']) . "\n";
		$xml .= '      </body>'."\n";
		$xml .= '    </text>'."\n";
		$xml .= '  </TEI>'."\n";
		$xml .= '</teiCorpus>'."\n";
		
		return $xml;
	}

	static function report_body($content){
		$content = normalize_content($content);
		$content = preg_replace("/<an#.*?:.*?>(.*?)<\/an>/", "$1", $content);
		$count = preg_match_all("/<p>(.*?)<\/p>/s", $content, $matches, PREG_SET_ORDER);
		$paragraphs_no = 1;
		$sentence_no = 1;
		
		if ( $count == 0 )
			throw new Exception("Report structure corrupted: no paragraphs");
		
		$paragraphs = array();
		
		foreach ($matches as $match){
			$content_br = explode("<br/>", $match[1]);
			$sentences = array();
			foreach ($content_br as $br){
				$sentences[] = '          <s xml:id="segm_s'.($sentence_no++).'">'.$br.'</s>';
			}
			$paragraphs[] = '        <p xml:id="segm_p'.($paragraphs_no++).'">'."\n".implode("\n", $sentences)."\n        </p>";
		}
		return implode("\n", $paragraphs);
	}
}

?>

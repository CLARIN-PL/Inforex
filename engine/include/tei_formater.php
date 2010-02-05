<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-02-05
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */
 
class TeiFormater{

	function corpus_header(){
		
	}
	
	function report_to_header($report_row){
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
		$xml .= '  <profileDesc>'."\n";
		$xml .= '    todo'."\n";
		$xml .= '  </profileDesc>'."\n";
		$xml .= '  <revisionDesc>'."\n";
		$xml .= '    todo'."\n";
		$xml .= '  </revisionDesc>'."\n";
		$xml .= '</teiHeader>';
		
		$replace = array();
		$replace["@NUMBER@"] = $report_row['number'];
		$replace["@TITLE@"] = $report_row['title'];
		$replace["@LINK@"] = $report_row['link'];
		
		$xml = str_replace(array_keys($replace), array_values($replace), $xml);
		
		return $xml;
	}
	
	function report_to_text($report_row){
		
		
		
		$xml = '<teiCorpus'."\n";
		$xml .= ' xmlns:xi="http://www.w3.org/2001/XInclude"'."\n";
		$xml .= ' xmlns="http://www.tei-c.org/ns/1.0">'."\n";
		$xml .= '  <xi:include href="GPW2004_header.xml"/>'."\n";
		$xml .= '  <TEI>'."\n";
		$xml .= '    <xi:include href="header.xml"/>'."\n";
		$xml .= '    <text xml:id="struct_text">'."\n";
		$xml .= '      <body>'."\n";
		$xml .= '      </body>'."\n";
		$xml .= '    </text>'."\n";
		$xml .= '  </TEI>'."\n";
		$xml .= '</teiCorpus>'."\n";
		
		return $xml;
	}
	
}
?>

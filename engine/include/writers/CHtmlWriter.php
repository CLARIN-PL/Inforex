<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
  
class HtmlWriter{
	
	static function writeRelations($filename, $cclDocuments, $relations){
						
		$f = fopen($filename, "w");
		fwrite($f, "<html>\n");
		fwrite($f, "<head>\n");
		fwrite($f, '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' . "\n");
		fwrite($f, '<style type="text/css">' . "\n");
		fwrite($f, 'body { font-size: 12px; }' . "\n");
		fwrite($f, 'sub { color: #555; }' . "\n");
		fwrite($f, 'span.source { border: 1px solid #FF4848; background: #FFDFDF } ' . "\n");
		fwrite($f, 'span.target { border: 1px solid #1FCB4A; background: #BDF4CB } ' . "\n");
		fwrite($f, 'li { line-height: 25px } ' . "\n");
		fwrite($f, '</style>' . "\n");
		fwrite($f, "</head>\n");
		fwrite($f, "<body>\n");
		
		$relations = array();
		
		foreach ($cclDocuments as $ccl){
			
			try{
			$doc = DocumentConverter::wcclDocument2AnnotatedDocument($ccl);
			}catch(Exception $ex){
				echo $ex->getMessage() . "\n\n";
				echo $ccl->name;
			}
			
			foreach ($doc->getRelations() as $relation){
				
				$source = $relation->getSource();
				$target = $relation->getTarget();
				
				if ($source->getSentence()->getId() <> $target->getSentence()->getId()){
					echo "skip relation between sentences \n";
					continue;
				}
				
				
				$relstr = "<small>" . $doc->getName() . "</small><br/>";
								
				foreach ($relation->getSource()->getSentence()->getTokens() as $token){
					if ( $token->getId() == $source->getFirstToken()->getId())
						$relstr .= "<span class='source'>";
					if ( $token->getId() == $target->getFirstToken()->getId())
						$relstr .= "<span class='target'>";
					$relstr .= $token->orth;
					if ( $token->getId() == $source->getLastToken()->getId())
						$relstr .= "</span><sub>" . $source->getType() . "</sub>";
					if ( $token->getId() == $target->getLastToken()->getId())
						$relstr .= "</span><sub>" . $target->getType() . "</sub>";
					$relstr .= " ";					
				}
				
				$relations[$relation->getType()][] = $relstr;
			}			
		}
		
		foreach ($relations as $relation_type=>$rels){
			fwrite($f, "<h1>$relation_type</h1>");
			fwrite($f, "<ol>");
			foreach ($rels as $rel){
				fwrite($f, "<li>$rel</li>\n");
			}
			fwrite($f, "</ol>");			
		}
		
		fwrite($f, "</body>");
		fwrite($f, "</html>");		
		fclose($f);
		
	} 
	
} 
 
?>

<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveTokenization extends CPerspective {
	
	function execute()
	{
		$this->set_tokens();
	}

	function set_tokens(){
		global $db;
		$id = $this->page->id;
		$cid = $this->page->cid;
		$row = $this->page->row;
		
		$exceptions = array();
		$htmlStr = new HtmlStr($row['content'], true);
		
		if ( count($exceptions) > 0 )
			$this->set("exceptions", $exceptions);	
	
		$sql = "SELECT t.*, ctag.ctag" .
				" FROM tokens t" .
				" JOIN tokens_tags_optimized tag USING (token_id)" .
				" JOIN tokens_tags_ctags ctag ON (tag.ctag_id = ctag.id)" .
				" WHERE report_id=? AND tag.disamb=1" .
				" ORDER BY `from` ASC";		
		$tokens = $db->fetch_rows($sql, array($row['id']));
		
		foreach ($tokens as $ann){
			try{
				$htmlStr->insertTag($ann['from'], sprintf('<span class="token" title="%s">', $ann['ctag']), $ann['to']+1, "</span>", true);
			}
			catch (Exception $ex){	
			}
		}
		
		$this->page->set('content_inline', Reformat::xmlToHtml($htmlStr->getContent()));
	}

	
}

?>

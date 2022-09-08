<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class HtmlStr2 implements IHtmlStr2 {
	private $content = null;
	/** Tablica z widocznymi znakami */
	public $chars = array();
	/** Tablica z niewidocznymi znakami (tagi, białe znaki) */
	public $tags = array();

	private $charRawToVisIndex = array();


	/**
	 * @throws Exception
	 */
	public function __construct($content, $recognize_tags=true){
        $content = str_replace(json_decode('"\u200a"'), " ", $content); // HAIR SPACE
		$content = str_replace(json_decode('"\u200b"'), " ", $content); // ZERO WIDTH SPACE
		$content = str_replace(json_decode('"\u200d"'), " ", $content);
		$content = str_replace(json_decode('"\u00a0"'), " ", $content); // NO-BREAK SPACE
		$content = str_replace(json_decode('"\u00ad"'), "-", $content); // SOFT HYPHEN
		$content = str_replace(json_decode('"\uf02d"'), "-", $content); // SOFT HYPHEN
		$content = str_replace(json_decode('"\ufeff"'), " ", $content); // ZERO WIDTH NO-BREAK SPACE
        $this->content = $content;

		// ToDo: Dla długich tekstów klasa HtmlStr2 zużywa strasznie dużo pamięci, nawet ponad 500MB.
		// Dopóki nie zostanie rozwiązany problem zużycia pamięci zostało wprowadzone ograniczenie na wielkość
		// obsługiwanych tekstów, tj. do 50k znaków.
		if ( strlen($this->content) > 265000 ){
			throw new Exception("Text too long to display (over 50k characters)");
		}		
		
		$h = new HtmlParser2($this->content);
		$os = $h->getObjects($recognize_tags);

		$chars = array();
		$tags = array();
		$stack = array();
		$hay = array();
		$indexRaw = 0;
		$indexVis = 0;

		foreach ($os as $o){
			if ($o instanceof IHtmlChar){
				$zn = $o->toString();
				if ( strlen( trim($zn)) > 0 ){
					$chars[] = $o;
					$tags[] = $stack;
					$stack = array();

					$this->charRawToVisIndex[$indexRaw] = $indexVis;
					$indexVis++;
				}
				else{
					$stack[] = $o;
				}
			}
			elseif( $o instanceof IHtmlTag){
				$t = new XmlTagPointer($o);
				$stack[] =  $t;
				
				if ( $o->getType() == IHtmlTag::HTML_TAG_OPEN )
					$hay[] = array($t, count($chars));
				elseif ( $o->getType() == IHtmlTag::HTML_TAG_CLOSE ){
					list($tl, $index) = array_pop($hay);
					if ( $tl->getTag()->getName() != $t->getTag()->getName() )
						throw new Exception('Different tag names.');
						
					$tl->setIndex(count($chars));
					$t->setIndex($index);
				}
			}
			$indexRaw += mb_strlen($o->toString(), "UTF-8");
		}
		$tags[] = $stack;
		
		$this->chars = $chars;
		$this->tags = $tags;


	}
	
	/**
	 * Get the position of opening and closing tags for given placements.
	 * The positions is an offset in the stack of elements.
	 */
	private function _getInsertTagPositions($from, $to){
		
		/** Opening tag */
		$i = count($this->tags[$from]);
		if ( count($this->tags[$from]) > 0 ){ 
			while ($i > 0
					&& 
					   ( $this->tags[$from][$i-1] instanceof IHtmlChar 
					     || $this->tags[$from][$i-1]->getTag()->getType() == IHtmlTag::HTML_TAG_SELF_CLOSE
					     || ( $this->tags[$from][$i-1]->getTag()->getType() == IHtmlTag::HTML_TAG_OPEN
					     		&& $this->tags[$from][$i-1]->getIndex() < $to )					     
					     || $this->tags[$from][$i-1]->getIndex() == $from
					   )
					){
				$i--;
			}
	
			while ($i < count($this->tags[$from])
					&& 
					   ( $this->tags[$from][$i] instanceof IHtmlChar 
					     || $this->tags[$from][$i]->getTag()->getType() == IHtmlTag::HTML_TAG_SELF_CLOSE
					     || $this->tags[$from][$i]->getIndex() == $from
					   )
					){
				$i++;
			}
			
			if ( $i > count($this->tags[$from]) ){
				throw new Exception("Cannot insert the opening tag");
			}
		}

		/** Closing tag */
		$j = 0;
		if ( count($this->tags[$to]) > 0 ) {
			while ($j < count($this->tags[$to])
					&& 
					   ( $this->tags[$to][$j] instanceof IHtmlChar 
					     || $this->tags[$to][$j]->getTag()->getType() == IHtmlTag::HTML_TAG_SELF_CLOSE
					     || ( $this->tags[$to][$j]->getTag()->getType() == IHtmlTag::HTML_TAG_CLOSE 
					     		&& $this->tags[$to][$j]->getIndex() > $from)
					     || $this->tags[$to][$j]->getIndex() == $to
					   )
					){
				$j++;
			}
		
			while ($j >0
					&& 
					   ( $this->tags[$to][$j-1] instanceof IHtmlChar 
					     || $this->tags[$to][$j-1]->getTag()->getType() == IHtmlTag::HTML_TAG_SELF_CLOSE
					     || $this->tags[$to][$j-1]->getIndex() == $to
					   )
					){
				$j--;
			}

			if ( $j > count($this->tags[$to]) ){
				throw new Exception("Cannot insert the closing tag");
			}
		}

		return array($i, $j);		
	}
	
	/**
	 * Verify tags consistency between given positions.
	 * Include: pairs of opening/closing tags.
	 */
	private function _verifyConsistency($from, $fi, $to, $ti){
		$tags = array_slice($this->tags[$from], $fi);
		for ($i=$from+1; $i<$to; $i++)
			$tags = array_merge($tags, $this->tags[$i]);
		$tags = array_merge($tags, array_slice($this->tags[$to], 0, $ti));
		foreach ($tags as $e){
			if ( $e instanceof XmlTagPointer
					&& $e->getTag()->getType() != IHtmlTag::HTML_TAG_SELF_CLOSE 
					&& ( $e->getIndex() > $to || $e->getIndex() < $from )
					)
				return $e;
		}
		return true;
	}
	
	/**
	 * Insert pair of opening and closing tags into XML document.
	 */
    public function insertTag($from, $tag_begin, $to, $tag_end, $force_insert=FALSE){

        if ( $from < 0 || $from > count($this->chars))
            throw new Exception("Starting index out of char array.\n\nfrom=$from;\ncount(chars)=".count($this->chars));
        if ( $to < 0 || $to > count($this->chars))
            throw new Exception("Starting index out of char array.\n\nfrom=$from;\ncount(chars)=".count($this->chars));

        list($i, $j) = $this->_getInsertTagPositions($from, $to);

        if ( !$force_insert && $this->_verifyConsistency($from, $i, $to, $j) !== true ){
            throw new Exception(sprintf("Annotation %s is crossing existing annotation", $tag_begin));
        }

        $xot = new XmlTagPointer(new HtmlTag("x", IHtmlTag::HTML_TAG_OPEN, $tag_begin));
        $xot->setIndex($to);
        $xct = new XmlTagPointer(new HtmlTag("x", IHtmlTag::HTML_TAG_CLOSE, $tag_end));
        $xct->setIndex($from);

        array_splice($this->tags[$from], $i, 0, array($xot));
        array_splice($this->tags[$to], $j, 0, array($xct));
    }

	public function getContent(){
		$strs = array();

		for ($i=0; $i<count($this->chars); $i++) {
			for ($j = 0; $j < count($this->tags[$i]); $j++) {
				$strs[] = $this->tags[$i][$j]->toString();
			}
			$strs[] = $this->chars[$i]->toString();

		}
		for ($j = 0; $j < count($this->tags[$i]); $j++) {
			$strs[] = $this->tags[$i][$j]->toString();
		}
		return implode($strs);
	}
	
	public function getText($from, $to){
		$text = "";
		for ($i=$from; $i<=$to; $i++){
            if ($i > $from) {
                if (is_array($this->tags[$i])) {
                    foreach ($this->tags[$i] as $t) {
                        if ($t instanceof IHtmlChar) {
                            $text .= $t->toString();
                        }
                    }
                }
            }
			if (($i>=0) && (is_object($this->chars[$i]))) {
                $text .= $this->chars[$i]->toString();
            }
		}
		return $text;
	}
	
	/**
	 * Return text for given range of visible characters.
	 * @param $from Index of first visible character.
	 * @param $to Index of last visible character.
	 * @param $align_left Align from left to a continous sequence of characters.
	 * @param $align_right Align from right to a continous sequence of characters.
	 * @param $keep_tags Include xml tags.
	 */
	public function getTextAlign($from, $to, $align_left, $align_right, $keep_tags=false){
		$text = "";
		while ( $align_left && $from > 0 && count($this->tags[$from]) == 0){
			$from--;
		}
		while ( $align_right && $to+1 < count($this->tags) && count($this->tags[$to+1]) == 0){
			$to++;
		}
		for ($i=$from; $i<=$to; $i++){
            if ($i > $from) {
                if (is_array($this->tags[$i])) {
                    foreach ($this->tags[$i] as $t) {
                        if ($t instanceof IHtmlChar || $keep_tags) {
                            $text .= $t->toString();
                        }
                    }
                }
            }
            if (is_object($this->chars[$i])) {
                $text .= $this->chars[$i]->toString();
            }
		}
		return $text;
	}	
	
	public function getSentencePos($pos_in_sentence){
        $sentence_begin = -1;
        $i=$pos_in_sentence;
        while ($i >= 0 && $sentence_begin === -1) {
            if (is_array($this->tags[$i])) {
                foreach ($this->tags[$i] as $t) {
                    if ( $t instanceof XmlTagPointer && $t->getTag() instanceof IHtmlTag 
                            && $t->getTag()->getName() === 'sentence' && $t->getTag()->getType() == IHtmlTag::HTML_TAG_OPEN) {
                        $sentence_begin = $i;
                    }
                }
            }
            $i--;
        }
        
        $sentence_end = -1;
        $i=$pos_in_sentence+1;
        while ($i <= count($this->chars) && $sentence_end === -1) {
            if (is_array($this->tags[$i])) {
                foreach ($this->tags[$i] as $t) {
                    if ( $t instanceof XmlTagPointer && $t->getTag() instanceof IHtmlTag 
                            && $t->getTag()->getName() === 'sentence' && $t->getTag()->getType == IHtmlTag::HTML_TAG_CLOSE) {
                        $sentence_end = $i;
                    }
                }
            }
            $i++;
        }
		if ($sentence_begin !== -1 && $sentence_end !== -1) {
                    $return = array($sentence_begin, $sentence_end-1);
                } else {
                    $return = array(-1, -1);
                }
		return $return;
	}
	
	public function getCharNumberBetweenPositions($pos1, $pos2){
		return mb_strlen($this->getText($pos1, $pos2));
	}
	
	public function isSpaceAfter($pos){
		if ( $pos + 1 < count($this->tags) )
			foreach ($this->tags[$pos+1] as $tag)
				if ( $tag instanceof IHtmlChar)
					return true;
		return false;
	}

	public function rawToVisIndex($rawIndex){
		return $this->charRawToVisIndex[$rawIndex];
	}
}

?>

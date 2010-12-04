<?php

class Reformat{
	
	static function xcesToHtml($content){
		$content = preg_replace('/(<chunk type="h1">.*?<\/chunk>)/', '<h1>$1</h1>', $content);
		$content = preg_replace('/(<chunk type="title">.*?<\/chunk>)/', '<h1>$1</h1>', $content);
		$content = preg_replace('/(<chunk type="p">.*?<\/chunk>)/', '<p>$1</p>', $content);
		$content = preg_replace('/((<chunk type="li">.*?<\/chunk>\s*)+)/', '<ul>$1</ul>', $content);
		$content = preg_replace('/((<chunk type="li2">.*?<\/chunk>\s*)+)/', '<ul>$1</ul>', $content);
		$content = preg_replace('/(<chunk type="li">.*?<\/chunk>)/', '<li>$1</li>', $content);
		$content = preg_replace('/(<chunk type="li1">.*?<\/chunk>)/', '<b>$1</b>', $content);
		$content = preg_replace('/(<chunk type="li2">.*?<\/chunk>)/', '<li>$1</li>', $content);
		return $content;		
	}
	
	static function xmlToHtml($content){
		return Reformat::xcesToHtml($content);
	}
}

?>
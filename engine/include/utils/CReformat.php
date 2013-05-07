<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Reformat{
	
	static function xcesToHtml($content){
		$content = preg_replace('/(<chunk type="h1"( xlink:href="[^"]+")?>.*?<\/chunk>)/', '<h1>$1</h1>', $content);
		$content = preg_replace('/(<chunk type="title"( xlink:href="[^"]+")?>.*?<\/chunk>)/', '<h1>$1</h1>', $content);
		$content = preg_replace('/(<chunk type="p"( xlink:href="[^"]+")?>.*?<\/chunk>)/ms', '<p>$1</p>', $content);
		$content = preg_replace('/((<chunk type="li"( xlink:href="[^"]+")?>.*?<\/chunk>\s*)+)/', '<ul>$1</ul>', $content);
		$content = preg_replace('/((<chunk type="li2"( xlink:href="[^"]+")?>.*?<\/chunk>\s*)+)/', '<ul>$1</ul>', $content);
		$content = preg_replace('/(<chunk type="li"( xlink:href="[^"]+")?>.*?<\/chunk>)/', '<li>$1</li>', $content);
		$content = preg_replace('/(<chunk type="li1"( xlink:href="[^"]+")?>.*?<\/chunk>)/', '<b>$1</b>', $content);
		$content = preg_replace('/(<chunk type="li2"( xlink:href="[^"]+")?>.*?<\/chunk>)/', '<li>$1</li>', $content);
		
		// TEI tags
		$content = preg_replace('/<lb\/>/', '<br/>', $content);
		$content = preg_replace('/<dateline[^>]*>(.*?)<\/dateline>/ms', '<p>$1</p>', $content);
		$content = preg_replace('/<head[^>]+>(.*?)<\/head>/ms', '<p>$1</p>', $content);
		$content = preg_replace('/<opener>(.*?)<\/opener>/ms', '<h2>$1</h2>', $content);
		$content = preg_replace('/<closer>(.*?)<\/closer>/ms', '<h2>$1</h2>', $content);
		$content = preg_replace('/<hi rend="(.*?)">(.*?)<\/hi>/ms', '<em class="$1">$2</em>', $content);
		$content = preg_replace('/<figure[^>]*\/>/ms', '<fig/>', $content);
		$content = preg_replace('/<figure[^>]*>/ms', '<fig>', $content);
		$content = preg_replace('/<\/figure>/ms', '</fig>', $content);
		//$content = preg_replace('/<figure[^>]*>[.*?]<\/figure>/ms', '<fig/>', $content);
		
		return $content;		
	}
	
	static function xmlToHtml($content){
		return Reformat::xcesToHtml($content);
	}
}

?>

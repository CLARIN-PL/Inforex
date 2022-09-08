<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

interface IHtmlStr2 {

	public function __construct($content, $recognize_tags=true);

	/**
	 * Insert pair of opening and closing tags into XML document.
	 */
    public function insertTag($from, $tag_begin, $to, $tag_end, $force_insert=FALSE);
	public function getContent();
	public function getText($from, $to);

	/**
	 * Return text for given range of visible characters.
	 * @param $from Index of first visible character.
	 * @param $to Index of last visible character.
	 * @param $align_left Align from left to a continous sequence of characters.
	 * @param $align_right Align from right to a continous sequence of characters.
	 * @param $keep_tags Include xml tags.
	 */
	public function getTextAlign($from, $to, $align_left, $align_right, $keep_tags=false);
	public function getSentencePos($pos_in_sentence);
	public function getCharNumberBetweenPositions($pos1, $pos2);
	public function isSpaceAfter($pos);
	public function rawToVisIndex($rawIndex);

}

?>

<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class DbCtag{

	/**
	 * Usuwa Ctagi(tagi morfologiczne) nie przypisane do żadnego tagu
	 */
	static function clean(){
		global $db;
		$sql = "DELETE ctag.* FROM tokens_tags_ctags ctag ".
				" LEFT JOIN tokens_tags_optimized tto ON(ctag.id = tto.ctag_id) ".
				" WHERE tto.ctag_id IS NULL";
		$db->execute($sql);
	}
	
}

?>
<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */


class DbBase{
	
	static function clean(){
		global $db;
		$sql = "DELETE bases.* FROM bases ".
				" LEFT JOIN tokens_tags_optimized tto ON(bases.id = tto.base_id) ".
				" WHERE tto.token_id IS NULL";
		$db->execute($sql);
	}
	
}
?>
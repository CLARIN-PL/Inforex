<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */


class DbBase{

	static function saveIfNotExists($base){
		global $db;
		$base_id = DbBase::getBaseId($base);
		if($base_id) return $base_id;
		
		$sql = "INSERT INTO bases(`text`) VALUES(?);";
		$db->execute($sql, array($base));
		return $db->last_id();
	}

	static function getBaseId($base){
		global $db;
		$sql = "SELECT id FROM bases WHERE text = ?";
		return $db->fetch_one($sql, array($base));
	}
	
	static function clean(){
		global $db;
		$sql = "DELETE bases.* FROM bases ".
				" LEFT JOIN tokens_tags_optimized tto ON(bases.id = tto.base_id) ".
				" WHERE tto.token_id IS NULL";
		$db->execute($sql);
	}
	
}
?>
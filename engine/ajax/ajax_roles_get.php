<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_roles_get extends CPage {

	/**
	 * Zwraca tablice JSON z dostępnymi rolami.
	 */
	function execute(){
		global $db;
		$rows = $db->fetch_rows("SELECT * FROM roles WHERE role != ? ORDER BY description", array(ROLE_SYSTEM_REPORT_GENERATION));
		$descriptions = array(
			'admin' => 'Administrator access',
			'create_corpus' => 'Create new corpora',
			'editor_schema_events' => 'Edit schema events',
			'editor_schema_relations' => 'Edit schema relations',
		);
		foreach ($rows as &$row) {
			if (isset($descriptions[$row['role']])) {
				$row['description'] = $descriptions[$row['role']];
			}
		}
		unset($row);
		return $rows;		
	}	
}

<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_administration_annotation_schema extends CPageAdministration {

	function execute(){
		$sql = "SELECT ans.annotation_set_id AS id, ans.name, ans.description, ans.public, u.screename " .
				" FROM annotation_sets ans" .
                " JOIN users u ON u.user_id = ans.user_id " .
				" ORDER BY id";
		$annotationSets = $this->getDb()->fetch_rows($sql);
		foreach ($annotationSets as &$annotationSet) {
			$annotationSet['owner_initials'] = $this->getInitials($annotationSet['screename']);
		}
		$this->set("annotationSets", $annotationSets);
	}

	private function getInitials($name) {
		$words = preg_split('/\s+/u', trim($name));
		$initials = "";
		foreach ($words as $word) {
			if ($word !== "") {
				$initials .= mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8');
			}
		}

		return $initials;
	}
}

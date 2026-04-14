<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_administration_relation_schema extends CPageAdministration {

	function execute(){

		$sql = "SELECT rs.relation_set_id AS id, rs.name, rs.description, rs.public, u.screename FROM relation_sets rs
                JOIN users u ON u.user_id = rs.user_id";
		$relationSets = $this->getDb()->fetch_rows($sql);
        foreach ($relationSets as &$relationSet) {
            $relationSet['owner_initials'] = $this->getInitials($relationSet['screename']);
        }
		$this->set("relationSets", $relationSets);

        $sql = "SELECT ans.annotation_set_id AS id, ans.name, ans.description, ans.public" .
            " FROM annotation_sets ans " .
            " ORDER BY id";
        $annotationSets = $this->getDb()->fetch_rows($sql);
        $this->set("annotationSets", $annotationSets);

        $sql = "SELECT * FROM relations_groups";
        $relationGroups = $this->getDb()->fetch_rows($sql);
        $this->set("relationsGroups", $relationGroups);
	}

    private function getInitials($name){
        $parts = preg_split('/\s+/u', trim($name));
        $initials = "";

        foreach ($parts as $part) {
            if ($part !== "") {
                $initials .= mb_strtoupper(mb_substr($part, 0, 1, "UTF-8"), "UTF-8");
            }
        }

        return $initials !== "" ? $initials : "?";
    }
}


?>

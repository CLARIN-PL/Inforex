<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_annotation_edit_update extends CPageCorpus {

    /**
     * ToDo: implement custom permissions to this action
     */
    function customPermissionRule($user=null, $corpus=null){
        return true;
    }

	function execute(){
		global $db, $user;

		if (!intval($user['user_id'])){
			throw new Exception("Brak identyfikatora użytkownika");
		}
		$name_str = $_POST['name_str'];
		$desc_str = $_POST['desc_str'];
        $description = $_POST['description'];
		$element_id = intval($_POST['element_id']);
		$element_type = $_POST['element_type'];
		$access = $_POST['set_access'] == "public" ? 1 : 0;

		if ($element_type=="annotation_set") {
            $sql = "UPDATE annotation_sets SET name= ?, description= ?, public = ? 
                    WHERE annotation_set_id= ?";
            $params = array(
                $desc_str,
                $description,
                $access,
                $element_id
            );
            $db->execute($sql, $params);
        }
		else if ($element_type=="annotation_subset") {
            $sql = "UPDATE annotation_subsets SET name= ?, description = ? WHERE annotation_subset_id = ?";
            $params = array(
                $desc_str,
                $description,
                $element_id
            );
            $db->execute($sql, $params);
        }
		else if ($element_type=="annotation_type"){
			$annotation_type_id = $_POST['annotation_type_id'];
			$group_id = $_POST['set_id'];
			$level = 0;
			$short_description = $_POST['short'];
            $shortlist = ($_POST['shortlist'] == 'Hidden' ? 1 : 0);
			$css = $_POST['css'];
			$sql = "UPDATE annotation_types SET 
                    name = ?, description = ?, group_id = ?, level = ?, short_description = ?, shortlist = ?, css = ?
                    WHERE annotation_type_id = ?";
			$params = array(
			    $name_str,
                $desc_str,
                $group_id,
                $level,
                $short_description,
                $shortlist,
                $css,
                $annotation_type_id
            );

            $db->execute($sql, $params);
		}
		return;
	}

}

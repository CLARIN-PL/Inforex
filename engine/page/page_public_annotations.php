<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Page_public_annotations extends CPage{

    function execute(){
        global $db;

        $sql = "SELECT ase.annotation_set_id as id, ase.name, u.screename, ase.description, ase.public
                FROM annotation_sets ase
                JOIN users u ON u.user_id = ase.user_id";
        $annotationSets = $db->fetch_rows($sql);

        foreach($annotationSets as $key => $annotationSet){
            $used_in_corpora = DbAnnotationSet::getCorporaOfAnnotationSet($annotationSet['id']);
            $public_corpora = array();
            foreach($used_in_corpora as $corpus){
                if($corpus['public'] == 1){
                    $public_corpora[] = $corpus;
                }
            }

            $annotationSets[$key]['count_ann'] = count($used_in_corpora);
            $annotationSets[$key]['count_public'] = count($public_corpora);
            $annotationSets[$key]['corpora'] = $public_corpora;
        }

        $this->set("annotationSets", $annotationSets);
    }
}


?>
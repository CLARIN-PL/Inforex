<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Page_public_annotations extends CPagePublic{

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
            $annotationSets[$key]['owner_initials'] = $this->getInitials($annotationSet['screename']);
        }

        $this->set("annotationSets", $annotationSets);
    }

    private function getInitials($name){
        $parts = preg_split('/\s+/', trim($name));
        $initials = "";
        $count = 0;

        foreach ($parts as $part){
            if ($part !== ""){
                $initials .= function_exists('mb_substr') ? mb_substr($part, 0, 1, 'UTF-8') : substr($part, 0, 1);
                $count++;
            }
            if ($count >= 2){
                break;
            }
        }

        return function_exists('mb_strtoupper') ? mb_strtoupper($initials, 'UTF-8') : strtoupper($initials);
    }
}


?>

<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_report_annotation_pad_tree extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_READ;
    }

    function execute(){
        global $user;

        $corpusId = $this->getCorpusId();
        $selectedAnnotationTypes = CookieManager::getSelectedAnnotationTypeTreeAnnotationTypes($corpusId);
        $selectedTypesString = implode(',', $selectedAnnotationTypes);

        if (empty($selectedTypesString)) {
            return array("html" => "");
        }

        $sql = "SELECT t.*, s.name as `set`" .
            " , ss.name AS subset" .
            " , ss.annotation_subset_id AS subsetid" .
            " , s.annotation_set_id AS groupid" .
            " , t.shortlist AS common" .
            " FROM annotation_types t" .
            " JOIN annotation_sets_corpora c ON (t.group_id=c.annotation_set_id)" .
            " JOIN annotation_sets s ON (s.annotation_set_id = t.group_id)" .
            " LEFT JOIN annotation_subsets ss USING (annotation_subset_id)" .
            " WHERE (c.corpus_id = ? AND t.group_id IN ($selectedTypesString))" .
            " ORDER BY `set`, subset, t.name";
        $annotationTypes = $this->getDb()->fetch_rows($sql, array($corpusId));

        $sql = "SELECT * FROM annotation_types_shortlist ats WHERE ats.user_id = ?";
        $userPreferences = isset($user['user_id'])
            ? $this->getDb()->fetch_rows($sql, array($user['user_id']))
            : array();

        foreach ($userPreferences as $key => $pref){
            $userPreferences[$pref['annotation_type_id']] = $pref;
            unset($userPreferences[$key]);
        }

        foreach ($annotationTypes as $key => $annotationType){
            $id = $annotationType['annotation_type_id'];
            if (!array_key_exists($id, $userPreferences)){
                continue;
            }

            if (($userPreferences[$id]['shortlist'] == 1 && $annotationTypes[$key]['common'] == 0)
                || ($userPreferences[$id]['shortlist'] == 0 && $annotationTypes[$key]['common'] == 1)){
                $annotationTypes[$key]['not_default'] = 1;
            } else {
                $annotationTypes[$key]['not_default'] = null;
            }

            if ($userPreferences[$id]['shortlist'] == 1){
                $annotationTypes[$key]['common'] = 1;
            } else {
                $annotationTypes[$key]['common'] = 0;
            }
        }

        $annotationGrouped = array();
        foreach ($annotationTypes as $annotationType){
            $set = $annotationType['group_id'];
            $setName = $annotationType['set'];
            $subset = $annotationType['subset'] ? $annotationType['subset'] : "none";

            if (!isset($annotationGrouped[$set])){
                $annotationGrouped[$set][$setName] = array();
                $annotationGrouped[$set][$setName]['groupid'] = $annotationType['groupid'];
            }
            if (!isset($annotationGrouped[$set][$setName][$subset])){
                $annotationGrouped[$set][$setName][$subset] = array();
                $annotationGrouped[$set][$setName][$subset]['subsetid'] = $annotationType['subsetid'];
                $annotationGrouped[$set][$setName][$subset]['notcommon'] = !$annotationType['common'];
            }

            $annotationGrouped[$set][$setName][$subset][$annotationType['name']] = $annotationType;
            $annotationGrouped[$set][$setName][$subset]['notcommon'] |= !$annotationType['common'];
        }

        $this->set('annotation_types_tree', $annotationGrouped);
        $templatePath = Config::Cfg()->get_path_engine() . "/templates/inc_report_annotator_annotation_pad_tree.tpl";
        $html = $this->template->fetch($templatePath);

        return array("html" => $html);
    }
}

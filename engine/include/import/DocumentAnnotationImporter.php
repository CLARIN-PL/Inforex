<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class DocumentAnnotationImporter extends GroupedLogger {

    var $annotationNameToId = null;

    function __construct($annotationNameToId){
        $this->annotationNameToId = $annotationNameToId;
    }

    function importAnnotationsFromCcl($reportId, $cclContent, $userId){
        $annotations = HelperBootstrap::transformCclToAnnotations($cclContent);
        foreach ($annotations as $an){
            if (!isset($this->annotationNameToId[$an->getType()])){
                $this->warn("Annotation type {$an->getType()} not found in the mapping", "Error for $reportId");
            } else {
                $an->setReportId($reportId);
                $an->setTypeId($this->annotationNameToId[$an->getType()]);
                $an->setUserId($userId);
                $an->setCreationTime(date("Y-m-d H:i:s"));
                $an->setStage("new");
                $an->setSource("bootstrapping");
                $an->save();
            }
        }
    }

}
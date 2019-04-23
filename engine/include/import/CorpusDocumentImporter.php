<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class CorpusDocumentImporter extends GroupedLogger{

    var $extFields = null;
    var $extTable = null;
    var $corpusId = null;

    function __construct($corpusId){
        $this->corpusId = $corpusId;
        $corpus = DbCorpus::getCorpusById($corpusId);
        if ( $corpus['ext'] ) {
            $this->extFields = DbCorpus::getCorpusExtColumnsWithMetadata($corpus['ext']);
            $this->extTable = $corpus['ext'];
        }
    }

    function insert($content, $metadata, $customMetadata){
        global $db;
        $r = new TableReport();
        foreach ($r->getFields() as $field){
            if ( isset($metadata[$field]) ){
                $r->$field = $metadata[$field];
            }
        }
        $r->content = $content;
        $r->corpora = $this->corpusId;
        $r->save();

        if ( $this->extFields && is_array($customMetadata) ){
            $row = array();
            $row['id'] = $r->id;
            foreach ($this->extFields as $f){
                if ( isset($customMetadata[$f['field']]) ){
                    $row[$f['field']] = $customMetadata[$f['field']];
                }
            }
            foreach ($customMetadata as $k=>$v){
                if ( !isset($row[$k]) ){
                    $this->warn("Custom metadata $k could not be mapped", "Document title={$metadata['title']}; field value={$v}");
                }
            }
            print_r($row);
            $db->replace($this->extTable, $row);
        }
    }

}
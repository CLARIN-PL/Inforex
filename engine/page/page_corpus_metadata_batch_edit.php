<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Page_corpus_metadata_batch_edit extends CPage{

    var $isSecure = true;

    function CPage($name = null, $description = null)
    {
        parent::__construct($name, $description);
        $this->includeJs("libs/handsontable-0.19.0/handsontable.full.min.js");
        $this->includeCss("libs/handsontable-0.19.0/handsontable.full.min.css");
        $this->includeJs("libs/chosen-1.8.3/chosen.jquery.js");
        $this->includeCss("libs/chosen-1.8.3/chosen.css");
        $this->includeJs("libs/handsontable-chosen-editor-0.1.2/handsontable-chosen-editor.js");
        $this->includeJs("js/c_regex_pattern.js");

    }

    function checkPermission(){
        return isCorpusOwner() || hasCorpusRole(CORPUS_ROLE_EXPORT);
    }

    function execute(){
        global $corpus, $db;

        $corpus_id = $corpus['id'];

        $corpus_flags = DbCorporaFlag::getCorpusFlags($corpus_id);
        $flags = DbCorporaFlag::getFlags();

        $filenames = DbCorpus::getDocumentFilenames($corpus_id);

        $metadata_columns = DbCorpus::getCorpusAllMetadataColumns($corpus_id);

        $this->set("filenames", $filenames);
        $this->set("corpus_flags", $corpus_flags);
        $this->set("flags", $flags);
        $this->set("metadata_columns", $metadata_columns);
    }
}


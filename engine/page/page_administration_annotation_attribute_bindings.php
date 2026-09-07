<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Page_administration_annotation_attribute_bindings extends CPageAdministration {

    function execute(){
        $sql = "SELECT id, name, public FROM corpora ORDER BY name";
        $this->set("corpora", $this->getDb()->fetch_rows($sql));
    }
}

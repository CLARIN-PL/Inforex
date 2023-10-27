<?php

class XmlFactory {

    private function setDataToExport(&$ccl,&$annotations,&$relations,&$lemmas,&$attributes) {

        CclFactory::setAnnotationsAndRelations($ccl, $annotations, $relations);
        CclFactory::setAnnotationLemmas($ccl, $lemmas);
        CclFactory::setAnnotationProperties($ccl, $attributes);
 
        // returns modified $ccl by reference

    } // 

    public function exportToXmlAndRelxml($filePathWithoutExt,&$ccl,&$annotations,&$relations,&$lemmas,&$attributes) {

        // set all other data to ccl structure
        $this->setDataToExport($ccl,$annotations,$relations,$lemmas,$attributes); 
        // export from $ccl to files
        CclWriter::write($ccl, $filePathWithoutExt.".xml", CclWriter::$CCL);
        CclWriter::write($ccl, $filePathWithoutExt.".rel.xml", CclWriter::$REL);

    } // exportToXmlAndRelxml()

} // XmlFormatExport class

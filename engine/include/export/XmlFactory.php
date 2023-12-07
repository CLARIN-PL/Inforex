<?php

class XmlFactory {

    public function exportToXmlAndRelxml($filePathWithoutExt,CclExportDocument &$ccl,&$annotations,&$relations,&$lemmas,&$attributes) {

        // set all other data to ccl structure
        $ccl->setCclProperties($annotations,$relations,$lemmas,$attributes);
        // export from $ccl to files
        CclWriter::write($ccl, $filePathWithoutExt.".xml", CclWriter::$CCL);
        CclWriter::write($ccl, $filePathWithoutExt.".rel.xml", CclWriter::$REL);

    } // exportToXmlAndRelxml()

} // XmlFactory class

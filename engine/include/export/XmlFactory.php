<?php

class XmlFactory {

    public function exportToXmlAndRelxml($filePathWithoutExt,CclExportDocument &$ccl,&$annotations,&$relations,&$lemmas,&$attributes, array $metadata = array()) {

        // set all other data to ccl structure
        $ccl->setCclProperties($annotations,$relations,$lemmas,$attributes);
        $ccl->setMetadata($metadata);
        // export from $ccl to files
        $writer = new CclWriter();
        $writer->write($ccl, $filePathWithoutExt.".xml", CclWriter::$CCL);
        $writer->write($ccl, $filePathWithoutExt.".rel.xml", CclWriter::$REL);

    } // exportToXmlAndRelxml()

} // XmlFactory class

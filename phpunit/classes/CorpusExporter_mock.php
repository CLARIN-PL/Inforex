<?php

class CorpusExporter_mock extends CorpusExporter {

    // access to protected method in CorpusExporter:
    //   parse_extractor($description)
    // which are not needed for rest of application. Should be
    // private in future

    public function mock_parse_extractor($description){

        return $this->parse_extractor($description);

    } // mock_parse_extractor()

    public function mock_export_document($report_id, $extractors, $disamb_only, &$extractor_stats, &$lists, $output_folder, $subcorpora, $tagging_method){

        // returns void
        $this->export_document($report_id, $extractors, $disamb_only, $extractor_stats, $lists, $output_folder, $subcorpora, $tagging_method);

    } // mock_export_document()

} // CorpusExporter_mock class

?>

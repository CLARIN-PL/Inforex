<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_export_new extends CPageCorpus {
    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_EXPORT;
    }
	
	function execute(){
		global $corpus, $db;
		$corpus_id = $corpus['id'];
		
		$extractors = $_POST['extractors'];
		$selectors = $_POST['selectors'];
		$description = $_POST['description'];
		$indices = $_POST['indices'];
		$tagging = $_POST['tagging'];
        $export_format = isset($_POST['export_format']) ? strtolower(trim($_POST['export_format'])) : 'legacy';
        if (!in_array($export_format, array('legacy', 'text', 'conllu', 'conllu_standard', 'clarin_json', 'clarin_parquet_zst', 'dialog_parquet_zst', 'clarin_jsonl_zst'))) {
            $export_format = 'legacy';
        }

		$attributes = array();
		$attributes['corpus_id'] = $corpus_id;
		$attributes['datetime_submit'] = date("Y-m-d H:i:s");
		$attributes['description'] = strval($description);
		$attributes['extractors'] = strtolower(strval($extractors));
		$attributes['selectors'] = strtolower(strval($selectors));
		$attributes['indices'] = strtolower(strval($indices));
		$attributes['tagging'] = strtolower(strval($tagging));
        $attributes['export_format'] = $export_format;
		$attributes['status'] = "new";
		
		fb($attributes);
		
		$db->insert("exports", $attributes);
		
		return array();
	}
	
}

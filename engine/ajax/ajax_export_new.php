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

    function customPermissionRule($user = null, $corpus = null){
        $postExportAction = isset($_POST['post_export_action']) ? trim(strtolower((string) $_POST['post_export_action'])) : '';
        if ($postExportAction === 'korpuskop') {
            return hasUserReportGenerationAccess($user, $corpus);
        }
        return null;
    }
	
	function execute(){
		global $corpus, $user;
		$corpus_id = $corpus['id'];
		$user_id = intval(isset($user['user_id']) ? $user['user_id'] : 0);
		if ($user_id <= 0) {
			throw new UserDataException('Do zlecenia eksportu wymagane jest aktywne konto użytkownika.');
		}
		
		$extractors = $_POST['extractors'];
		$selectors = $_POST['selectors'];
		$description = $_POST['description'];
		$indices = $_POST['indices'];
		$tagging = $_POST['tagging'];
        $post_export_action = isset($_POST['post_export_action']) ? trim(strtolower((string) $_POST['post_export_action'])) : '';
        $post_export_payload = isset($_POST['post_export_payload']) ? trim((string) $_POST['post_export_payload']) : '';
        $export_format = isset($_POST['export_format']) ? strtolower(trim($_POST['export_format'])) : 'legacy';
        if (!in_array($export_format, array('legacy', 'text', 'conllu', 'conllu_standard', 'clarin_json', 'clarin_parquet_zst', 'dialog_parquet_zst', 'clarin_jsonl_zst'))) {
            $export_format = 'legacy';
        }

		$attributes = array();
		$attributes['corpus_id'] = $corpus_id;
		$attributes['user_id'] = $user_id;
		$attributes['datetime_submit'] = date("Y-m-d H:i:s");
		$attributes['description'] = strval($description);
		$attributes['extractors'] = strtolower(strval($extractors));
		$attributes['selectors'] = strtolower(strval($selectors));
		$attributes['indices'] = strtolower(strval($indices));
		$attributes['tagging'] = strtolower(strval($tagging));
        $attributes['export_format'] = $export_format;
        $attributes['post_export_action'] = $post_export_action !== '' ? $post_export_action : null;
        $attributes['post_export_payload'] = $post_export_payload !== '' ? $post_export_payload : null;
		$attributes['status'] = "new";
		
		fb($attributes);
		
		return DbExport::createOrReuseExport($attributes);
	}
	
}

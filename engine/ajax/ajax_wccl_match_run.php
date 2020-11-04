<?php
 
class Ajax_wccl_match_run extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anySystemRole[] = USER_ROLE_LOGGEDIN;
    }

    function execute(){
		global $corpus;
		
		$rules = strval($_POST['wccl_rules']);
		$annotations = strval($_POST['annotations']);
		$report_id = intval($_POST['report_id']);

		$cmd = "python ".Config::Config()->get_wccl_match_script()." -r %s -f %s -a %s 2>&1";
		
		$file_path = sprintf("%s/ccls/corpus%04d/%08d.xml", Config::Config()->get_path_secured_data(), $corpus['id'], $report_id);
		
		$cmd = sprintf($cmd, escapeshellarg($rules), $file_path, escapeshellarg($annotations));
		fb($cmd);

		$output = array();
		exec($cmd, $output);
		
		$errors = array();

		if (!file_exists(Config::Config()->get_wccl_match_script()))
			$errors[] = "Błąd konfiguracji: plik nie istnieje ".Config::Config()->get_wccl_match_script();

		if (count($output) > 1){
			$output_joined = implode($output);
			if (strpos($output_joined, "Mark action would overwrite existing annotation") > -1)
				$errors[] = "<em>Rulers error:</em> $output_joined";
			else
				$errors[] = "<em>Execution error:</em> More than one line was returned.\n\n" . implode("\n", $output);
		}
		$response = json_decode($output[0]);
		
		
		if ( isset($response->error) && count($response->error) > 0 ){
			$errors = array_merge($errors, $response->error);
		}
		
		$return = array();
		$return["errors"] = $errors;
		$return["items"] = $response->items;
									
		return $return;
	}
	
}

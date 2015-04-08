<?php
 
class Ajax_wccl_match_run extends CPage {
	
	var $isSecure = false;
	
	function checkPermission(){
		return true;
	}
	
	function execute(){
		global $config, $corpus;
		
		$rules = strval($_POST['wccl_rules']);
		$annotations = strval($_POST['annotations']);
		$report_id = intval($_POST['report_id']);

		$cmd = "python {$config->wccl_match_script} -r %s -f %s -a %s 2>&1";
		
		$file_path = sprintf("%s/ccls/corpus%04d/%08d.xml", $config->path_secured_data, $corpus['id'], $report_id);
		
		$cmd = sprintf($cmd, escapeshellarg($rules), $file_path, escapeshellarg($annotations));

		$output = array();
		exec($cmd, $output);
		
		$errors = array();

		if (!file_exists($config->wccl_match_script))
			$errors[] = "Błąd konfiguracji: plik nie istnieje {$config->wccl_match_script}";

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
?>
 
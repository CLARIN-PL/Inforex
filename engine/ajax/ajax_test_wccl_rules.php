<?php
 
class Ajax_test_wccl_rules extends CPageCorpus {
	
	function execute(){
		
		$start = intval($_POST['start']);
		$offset = intval($_POST['offset']);
		$rules = strval($_POST['wccl_rules']);
		$corpus = intval($_POST['corpus']);
		$corpus_path = Config::Cfg()->get_wccl_match_tester_corpora()[$corpus]["path"];

		$cmd = "python ".Config::Cfg()->get_wccl_match_tester_script()." -s %d -o %d -r %s -c %s 2>&1";
		$cmd = sprintf($cmd, $start, $offset, escapeshellarg($rules), $corpus_path);

		$output = array();
		exec($cmd, $output);
		
		$errors = array();

		if (!file_exists(Config::Cfg()->get_wccl_match_tester_script()))
			$errors[] = "Błąd konfiguracji: plik nie istnieje {Config::Cfg()->get_wccl_match_script()}";

		if (count($output) > 1){
			$output_joined = implode($output);
			if (strpos($output_joined, "Mark action would overwrite existing annotation") > -1)
				$errors[] = "<em>Błąd wykonania reguły:</em> Próba nadpisania anotacji utworzonej przez inną regułę.";
			else
				$errors[] = "<em>Błąd wywołania skryptu:</em> Zwrócono więcej niż jedną linię.\n\n" . implode("\n", $output);
		}

		$response = json_decode($output[0]);
		
		if ( isset($response->error) && count($response->error) > 0 )
			$errors = array_merge($errors, $response->error);
		
		$return = array();
		$return["errors"] = $errors;
		if(isset($response->processed)) {
			$return["finished"] = $response->processed == 0;
			$return["total_processed"] = $start + $response->processed;  
		}
		$return["items"] = isset($response->items) ? $response->items : 0;
		$return["total_documents"] = isset($response->total) ? $response->total : 0;
									
		return $return;
	}
	
}
 

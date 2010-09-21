<?php
class Page_ner extends CPage{

	var $isSecure = false;
	
	function execute(){		
		global $mdb2, $config;
		
		$model = intval($_POST['model']);

		$models = array();
		$models[] = array("description"=>"Raporty giełdowe anotowane jednostkami osób i firm", "file"=>"gpw-person-company.model");
		$models[] = array("description"=>"Raporty giełdowe anotowane tylko jednostkami osób", "file"=>"gpw-person.model");
		$models[] = array("description"=>"Raporty giełdowe anotowane tylko jednostkami firm", "file"=>"gpw-company.model");
		
		if ($model>=count($models)) $model = 0;

		$content = stripslashes($_REQUEST['content']);
		$content_submitted = $content;
		$formated = array();
		
		if ($content){
			// Podziel tekst na tokeny
			$xml = $this->tag($content);
			// Zamień XML na czysty tekst
			$content = $this->xml2txt($xml);
			// Uruchom moduł NER
			$result = $this->ner($content, $models[$model]["file"]);
			// Formatuje wynik do wyświetlenia
			$formated = $this->formatOutput($result);
		}
		
		$this->set('models', $models);
		$this->set('model', $model);
		$this->set('content', $content);
		$this->set('content_submitted', $content_submitted);
		$this->set('content_marked', implode("\n", $formated));
	}
	
	/**
	 * Uruchamia zewnetrzny moduł NER i zwraca wynik jego działania.
	 * @param $content tekst do przetworzenia z podziałem na tokeny (spacjami) i na zdania (\n).
	 * @param $model model nauczony na zbiorze danych.
	 */
	function ner($content, $model){
		global $config;
		$path = $config->path_engine . "/ner";
		//$model = "gpw-person-company.model";
		$cmd = "LANG=en_US.utf-8; java -cp {$path}/lingpipe-3.8.2.jar:{$path}/neDemo.jar RunNER test {$path}/{$model} " . '"' . str_replace('"', '\"', $content) . '"';
		return shell_exec($cmd);		
	}
	
	/**
	 * Taguje podany tekst za pośrednictwem TaKIPI-WS.
	 * @param $content tekst do tagowania
	 */
	function tag($content){
		global $config;

		// Create a stub of the web service 
		$client = new SoapClient($config->takipi_wsdl); 

		// Send a request 
		$request = $client->Tag($content, "TXT", false);			
		$token = $request->msg; 
		$status = $request->status;
	
		// Check whether the request was queued 
		if ( $status == 2 ){ 
		    // Check the request status until is 2 (queued) or 3 (in processing) 
		    do 
		    { 
		        sleep(1);  
		        $status = $client->GetStatus($token);
		    }while ( $status == 2 || $status == 3 ); 
		     
		    // If the status is 1 then fetch the result and print it 
		    if ( $status == 1 ){ 
		        $result = $client->GetResult($token);
		        //$json = array("tagged" => $this->align($result->msg, $id));		        
		        $client->DeleteRequest($token);
		    } 
		}
		return $result->msg;
	}

	/**
	 * Zamienia XML (wynik tagowania) na czysty tekst.
	 * @param $xml wynik tagowania w postaci XML-a.
	 */
	function xml2txt($xml){
		$takipiDocument = TakipiReader::createDocumentFromText("<doc>".$xml."</doc>");
		$content = "";
		foreach ($takipiDocument->sentences as $sentence){
			foreach ($sentence->tokens as $token)
				$content .= $token->orth . " ";
			$content .= "\n";
		}			
		return trim($content);		
	}
	
	/**
	 * Formatuje wynik rozpoznawania jednostek do wyświetlenia na stronie.
	 * @return String[] tablica sformatowanych zdań.
	 */
	function formatOutput($result){
		$formated_sentences = array();
		foreach (explode("\n", $result) as $sentence){
			if (!preg_match("/(.*) : \[.*\]/", $sentence, $matches_sentence))
				continue;
			
			preg_match_all("/([0-9]+)-([0-9]+):(PERSON|COMPANY)/", $sentence, $matches, PREG_SET_ORDER);

			$formated = $matches_sentence[1];				
			foreach (array_reverse($matches) as $match){
				$annotation_text = trim(mb_substr($sentence, $match[1], $match[2]-$match[1]+1));
				$result = mb_substr($result, 0, $match[2])."</b>".mb_substr($result, $match[2]);
//				if (ner_filter($annotation_text))
//					$formated = mb_substr($result, 0, $match[1])."<b style='background: yellow'>".mb_substr($result, $match[1]);
//				else
				$formated = mb_substr($formated, 0, $match[2])."</span>".mb_substr($formated, $match[2]);
				$class = mb_strtolower($match[3]);
				$formated = mb_substr($formated, 0, $match[1])."<span class='$class'>".mb_substr($formated, $match[1]);						
			}
			$formated_sentences[] = $formated;
		}
		return $formated_sentences;
	}	
}


?>

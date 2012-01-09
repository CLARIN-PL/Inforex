<?

/**
 * Testy spójności dla annotacji w dokumencie 
 */

class AnnotationsIntegrity{	
	
	/** 
	 * Sprawdza czy tokeny przecinają anotacje
	 * Opis: Dla każdej anotacji A nie istnieje taki token T, dla którego (T.from > A.from AND T.from < A.to) OR (T.to > A.from AND T.to < A.to)
	 * Input: lista annotacji, lista tokenów 
	 * Return - liczba naruszeń spójności w dokumencie 
	 */	
	static function checkAnnotations($annotations,$tokens){
		$count_wrong_annotations = 0;
		foreach($annotations as $key => $annotation){
			foreach($tokens as $token){
				if(($token['from'] > $annotation['from'] && $token['from'] < $annotation['to']) || ($token['to'] > $annotation['from'] && $token['to'] < $annotation['to'])){
					$count_wrong_annotations++;
				}
			}
		}
		return $count_wrong_annotations;
	}	
}

?>
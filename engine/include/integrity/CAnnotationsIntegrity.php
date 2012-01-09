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
	static function checkAnnotationsByTokens($annotations,$tokens){
		$count_wrong_annotations = 0;
		foreach($annotations as $key => $annotation){
			if($annotation['stage'] == 'final'){
				foreach($tokens as $token){
					if(($token['from'] > $annotation['from'] && $token['from'] < $annotation['to']) || ($token['to'] > $annotation['from'] && $token['to'] < $annotation['to'])){
						$count_wrong_annotations++;
					}
				}
			}
		}
		return $count_wrong_annotations;
	}
	
	/** 
	 * Sprawdza wzajemne przecinanie anotacji
	 * Opis: Dla każdej anotacji A1 nie istnieje taka anotacja A2 będąca tego samego typu, dla której (A2.from > A1.from AND A2.from < A1.to) OR (A2.to > A1.from AND A2.to < A1.to)
	 * Input: lista annotacji  
	 * Return: liczba naruszeń spójności w dokumencie 
	 */	
	static function checkAnnotationsByAnnotation($annotations){
		$count_wrong_annotations = 0;
		$annotation_stages = array();
		foreach($annotations as $annotation1){
			if($annotation1['stage'] == 'final'){
				foreach($annotations as $annotation2){
					if($annotation1['type'] == $annotation2['type']){
						if($annotation2['stage'] == 'final'){
							if(($annotation2['from'] > $annotation1['from'] && $annotation2['from'] < $annotation1['to']) || ($annotation2['to'] > $annotation1['from'] && $annotation2['to'] < $annotation1['to'])){
								$count_wrong_annotations++;
							}
						}		
					}
				}
			}
		}
		return $count_wrong_annotations;
	}	
}

?>
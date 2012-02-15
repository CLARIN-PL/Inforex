<?

/**
 * Testy spójności dla annotacji w dokumencie 
 */

class AnnotationsIntegrity{	
	
	/** 
	 * Sprawdza czy tokeny przecinają anotacje
	 * Opis: Dla każdej anotacji A nie istnieje taki token T, dla którego (T.from > A.from AND T.from < A.to AND T.to > A.to) OR (T.from < A.from AND T.to > A.from AND T.to < A.to)
	 * Input: lista annotacji, lista tokenów 
	 * Return: liczba naruszeń spójności w dokumencie, lista elementów naruszających spójność 
	 */	
	static function checkAnnotationsByTokens($annotations,$tokens){
		$count_wrong_annotations = 0;
		$annotation_data = array();
		foreach($annotations as $key => $annotation){
			if($annotation['stage'] == 'final'){
				foreach($tokens as $token){
					if(($token['from'] > $annotation['from'] && $token['from'] < $annotation['to'] && $token['to'] > $annotation['to']) || ($token['from'] < $annotation['from'] && $token['to'] > $annotation['from'] && $token['to'] < $annotation['to'])){
						$count_wrong_annotations++;
						$annotation_data[] = array('annotation_id' => $annotation['id'], 'annotation_type' => $annotation['type'], 'annotation_text' => $annotation['text'], 'annotation_from' => $annotation['from'], 'annotation_to' => $annotation['to'], 'token_id' => $token['token_id'], 'token_from' => $token['from'], 'token_to' => $token['to']);
					}
				}
			}
		}
		return array('count' => $count_wrong_annotations, 'data' => $annotation_data);
	}
	
	/** 
	 * Sprawdza wzajemne przecinanie anotacji
	 * Opis: Dla każdej anotacji A1 nie istnieje taka anotacja A2 będąca tego samego typu, dla której (A2.from > A1.from AND A2.from < A1.to AND A2.to > A1.to) OR (A2.from < A1.from AND A2.to > A1.from AND A2.to < A1.to)
	 * Input: lista annotacji  
	 * Return: liczba naruszeń spójności w dokumencie, lista elementów naruszających spójność 
	 */	
	static function checkAnnotationsByAnnotation($annotations,$annotations_types){
		$count_wrong_annotations = 0;
		$annotation_data = array();
		$annotation_lists = array();
		foreach($annotations as $annotation1){
			if($annotation1['stage'] == 'final'){
				foreach($annotations as $annotation2){
					if($annotations_types[$annotation1['type']] == $annotations_types[$annotation2['type']]){
						if($annotation2['stage'] == 'final'){
							if(($annotation2['from'] > $annotation1['from'] && $annotation2['from'] < $annotation1['to']  && $annotation2['to'] > $annotation1['to']) || ($annotation2['from'] < $annotation1['from'] && $annotation2['to'] > $annotation1['from'] && $annotation2['to'] < $annotation1['to'])){
								if(!array_key_exists($annotation1['id'], $annotation_lists) || !array_key_exists($annotation2['id'], $annotation_lists)){
									$count_wrong_annotations++;
									$annotation_lists[$annotation1['id']] = $annotation2['id'];
									$annotation_lists[$annotation2['id']] = $annotation1['id'];								 		
									$annotation_data[] = array('id1' => $annotation1['id'], 'type1' => $annotation1['type'], 'text1' => $annotation1['text'], 'id2' => $annotation2['id'], 'type2' => $annotation2['type'], 'text2' => $annotation2['text']);
								}elseif($annotation_lists[$annotation1['id']] != $annotation2['id'] || $annotation_lists[$annotation2['id']] != $annotation1['id']){
									$count_wrong_annotations++;
									$annotation_lists[$annotation1['id']] = $annotation2['id'];
									$annotation_lists[$annotation2['id']] = $annotation1['id'];								 		
									$annotation_data[] = array('id1' => $annotation1['id'], 'type1' => $annotation1['type'], 'text1' => $annotation1['text'], 'id2' => $annotation2['id'], 'type2' => $annotation2['type'], 'text2' => $annotation2['text']);
								}
							}
						}		
					}
				}
			}
		}
		return array('count' => $count_wrong_annotations, 'data' => $annotation_data);
	}
	
	/** 
	 * Sprawdza występowanie duplikatów 
	 * Opis: Duplikatem jest para anotacji, które posiadają takie same wartości dla atrybutów `report_id`, `from`, `to`, `type` oraz ustawione są jako stage=final.
	 * Input: lista annotacji  
	 * Return: liczba naruszeń spójności w dokumencie, lista elementów naruszających spójność 
	 */	
	static function checkAnnotationsDuplicate($annotations){
		$count_wrong_annotations = 0;
		$annotation_data = array();
		$annotation_lists = array();
		foreach($annotations as $annotation){
			if($annotation['stage'] == 'final'){
				foreach($annotation_lists as $check_element){
					if($annotation['type'] == $check_element['type'] && $annotation['from'] == $check_element['from'] && $annotation['to'] == $check_element['to']){
						$count_wrong_annotations++;	
						$annotation_data[] = array('id1' => $annotation['id'], 'type1' => $annotation['type'], 'text1' => $annotation['text'], 'id2' => $check_element['id'], 'type2' => $check_element['type'], 'text2' => $check_element['text']);
					}
				}
				$annotation_lists[] = $annotation;
			}
		}
		return array('count' => $count_wrong_annotations, 'data' => $annotation_data);
	}	
	
	/** 
	 * Sprawdza występowanie anotacji w anotacjach tego samego typu 
	 * Opis: Dla każdej anotacji A1 nie istnieje anotacja A2 będąca tego samego typu, dla której (A2.from >= A1.from AND A2.to <= A1.to)
	 * Input: lista annotacji
	 * Return: liczba naruszeń spójności w dokumencie, lista elementów naruszających spójność
	 */	
	static function checkAnnotationInAnnotation($annotations){
		$count_wrong_annotations = 0;
		$annotation_data = array();
		$annotation_lists = array();
		foreach($annotations as $annotation){
			if($annotation['stage'] == 'final'){
				foreach($annotations as $check_element){
					if($check_element['stage'] == 'final' && $annotation['type'] == $check_element['type']){
						if($annotation['id'] != $check_element['id'] && $check_element['from'] >= $annotation['from'] && $check_element['to'] <= $annotation['to']){
							if(!array_key_exists($annotation['id'], $annotation_lists) || !array_key_exists($check_element['id'], $annotation_lists)){
								$annotation_lists[$annotation['id']][] = $check_element['id'];
								$annotation_lists[$check_element['id']][] = $annotation['id'];
								$count_wrong_annotations++;
								$annotation_data[] = array('id1' => $annotation['id'], 'type1' => $annotation['type'], 'text1' => $annotation['text'], 'id2' => $check_element['id'], 'type2' => $check_element['type'], 'text2' => $check_element['text']);
							}
							elseif(!in_array($check_element['id'],$annotation_lists[$annotation['id']])){
								$annotation_lists[$annotation['id']][] = $check_element['id'];
								$annotation_lists[$check_element['id']][] = $annotation['id'];
								$count_wrong_annotations++;
								$annotation_data[] = array('id1' => $annotation['id'], 'type1' => $annotation['type'], 'text1' => $annotation['text'], 'id2' => $check_element['id'], 'type2' => $check_element['type'], 'text2' => $check_element['text']);
							}
						}		
					}
				}				
			}
		}
		return array('count' => $count_wrong_annotations, 'data' => $annotation_data);
	}
}

?>
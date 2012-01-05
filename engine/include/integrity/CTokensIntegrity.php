<?

class TokensIntegrity{
	
	static function checkTokens($tokens_list){
		$count_wrong_tokens = 0;
		$tokens_count = count($tokens_list)-1; //bez ostatnego tokenu
		foreach($tokens_list as $key => $value){
			if($key < $tokens_count){
//				echo $key ."->". $tokens_list[$key + 1]['from'] ." ". $value['to'] ."\n";
				if($value['to']+1 != $tokens_list[$key + 1]['from']){
					$count_wrong_tokens++;
				}
			}
		}
		return $count_wrong_tokens;
	}	
}


/**
 * 
 * $chunk = str_replace("<"," <",$chunk);
 *		$chunk = str_replace(">","> ",$chunk);
 *		$tmpStr = trim(preg_replace("/\s\s+/"," ",html_entity_decode(strip_tags($chunk),ENT_COMPAT, 'UTF-8')));
 *		$tmpStr2 = preg_replace("/\n+|\r+|\s+/","",$tmpStr);
 *		$to = $from + mb_strlen($tmpStr2)-1;
 * 
 * najlepiej będzie użyć strip_tags to usunięcia tagów, 
 * html_entity_decode to zamiany encji na pojedyncze znaki, 
 * preg_replace to usunięcia białych znaków i 
 * mb_strlen do policzenia długości
 */

?>
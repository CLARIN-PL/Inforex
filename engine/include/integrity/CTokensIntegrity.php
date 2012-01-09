<?

/**
 * Testy spójności dla tokenów w dokumencie 
 */

class TokensIntegrity{
	
	/** 
	 * Sprawdza ciągłość tokenów w dokumencie
	 * Opis: Dla każdego tokenu A w dokumencie (oprócz ostatniego) istnieje token B taki, że (A.to+1 = B.from)
	 * Input: lista tokenów 
	 * Return - liczba naruszeń spójności w dokumencie 
	 */	
	static function checkTokens($tokens_list){
		$count_wrong_tokens = 0;
		$tokens_count = count($tokens_list)-1; //bez ostatnego tokenu
		foreach($tokens_list as $key => $token){
			if($key < $tokens_count){
				if($token['to']+1 != $tokens_list[$key + 1]['from']){
					$count_wrong_tokens++;
				}
			}
		}
		return $count_wrong_tokens;
	}
	
	/** 
	 * Sprawdza czy indeksy tokenów nie wykraczają poza ramy dokumnetu
	 * Opis: Dla każdego tokenu T w dokumencie D spełniona jest zależność, (T.from <= D.length AND T.to <= D.length)
	 * Input: lista tokenów, treść dokumentu
	 * Return - liczba naruszeń spójności w dokumencie 
	 */
	static function checkTokensScale($tokens_list,$content){
		$count_wrong_tokens = 0;
		$tokens_count = count($tokens_list);
		$content_with_space = trim(preg_replace("/\s\s+/"," ",html_entity_decode(strip_tags($content),ENT_COMPAT, 'UTF-8')));
		$content_without_space = preg_replace("/\n+|\r+|\s+/","",$content_with_space);;
		$content_length = mb_strlen($content_without_space); 
		foreach($tokens_list as $key => $token){
			if($token['from'] > $content_length || $token['to'] > $content_length){
				$count_wrong_tokens++;
			}
		}
		return $count_wrong_tokens;
	}		
}

?>
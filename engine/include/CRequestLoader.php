<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * Loads data according to the state of $_REQUEST variables
 */
class RequestLoader{

	/********************************************************************
	 * Determine and load corpus context according to following attributes:
	 * - annotation_id,
	 * - id or report_id,
	 * - corpus,
	 * - relation_id
	 */
	static function loadCorpus(){
		global $user, $db;
		$annotation_id = isset($_REQUEST['annotation_id']) ? intval($_REQUEST['annotation_id']) : 0; 
		$report_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : (isset($_REQUEST['report_id']) ? intval($_REQUEST['report_id']) : 0); 
		$corpus_id = isset($_GET['corpus']) ? intval($_GET['corpus']) : 0; 
		$relation_id = isset($_REQUEST['relation_id']) ? intval($_REQUEST['relation_id']) : 0; 
		
		// Obejście na potrzeby żądań, gdzie nie jest przesyłany id korpusu tylko raportu lub anotacji
		if ($corpus_id==0 && $report_id==0 && $annotation_id)
			$report_id = $db->fetch_one("SELECT report_id FROM reports_annotations WHERE id = ?", $annotation_id);
		if ($corpus_id==0 && $report_id>0)
			$corpus_id = $db->fetch_one("SELECT corpora FROM reports WHERE id = ?", $report_id);
		if ($relation_id>0)	
			$corpus_id = $db->fetch_one("SELECT corpora FROM relations r JOIN reports_annotations a ON (r.source_id = a.id) JOIN reports re ON (a.report_id = re.id) WHERE r.id = ?", $relation_id);
		
		$corpus = $db->fetch("SELECT * FROM corpora WHERE id=".intval($corpus_id));
		// Pobierz prawa dostępu do korpusu dla użytkowników
		if ($corpus){
			$roles = $db->fetch_rows("SELECT *" .
					" FROM users_corpus_roles ur" .
					" WHERE ur.corpus_id = ?", array($corpus['id']));
			$corpus['role'] = array();
			foreach ($roles as $role)
				$corpus['role'][$role['user_id']][$role['role']] = 1;
		}
		if(isset($user['user_id'])){
			if (hasRole(USER_ROLE_ADMIN))
				$sql="SELECT id AS corpus_id, name FROM corpora ORDER BY name";
			else
				$sql="SELECT c.id AS corpus_id, c.name FROM corpora c LEFT JOIN users_corpus_roles ucs ON c.id=ucs.corpus_id WHERE (ucs.user_id={$user['user_id']} AND ucs.role='". CORPUS_ROLE_READ ."') OR c.user_id={$user['user_id']}";
			$corpus['user_corpus'] = $db->fetch_rows($sql);
		} 
		
		return $corpus;		
	}	

}
?>
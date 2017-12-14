<?php
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
<<<<<<< HEAD
        $export_id = isset($_GET['export_id']) ? intval($_GET['export_id']) : 0;
        $relation_id = isset($_REQUEST['relation_id']) ? intval($_REQUEST['relation_id']) : 0;
		
=======
		$relation_id = isset($_REQUEST['relation_id']) ? intval($_REQUEST['relation_id']) : 0; 

		//Próba zastąpienia wartości corpus_id wartością z $_REQUEST
		if($corpus_id == 0){
		    if(isset($_REQUEST['corpus_id'])){
                $corpus_id = intval($_REQUEST['corpus_id']);
            }
        }

>>>>>>> origin/mikolaj
		// Obejście na potrzeby żądań, gdzie nie jest przesyłany id korpusu tylko raportu lub anotacji
		if ($corpus_id==0 && $report_id==0 && $annotation_id) {
            $report_id = $db->fetch_one("SELECT report_id FROM reports_annotations WHERE id = ?", $annotation_id);
        }
		if ($corpus_id==0 && $report_id>0) {
            $corpus_id = $db->fetch_one("SELECT corpora FROM reports WHERE id = ?", $report_id);
        }
		if ($relation_id>0) {
            $corpus_id = $db->fetch_one("SELECT corpora FROM relations r JOIN reports_annotations a ON (r.source_id = a.id) JOIN reports re ON (a.report_id = re.id) WHERE r.id = ?", $relation_id);
        }
        if ($export_id>0) {
            $corpus_id = $db->fetch_one("SELECT corpus_id FROM exports WHERE export_id = ?", $export_id);
        }

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
				$sql="SELECT c.id AS corpus_id, c.name FROM corpora c LEFT JOIN users_corpus_roles ucs ON c.id=ucs.corpus_id WHERE (ucs.user_id={$user['user_id']} AND ucs.role='". CORPUS_ROLE_READ ."') OR c.user_id={$user['user_id']} GROUP BY c.id";
			$corpus['user_corpus'] = $db->fetch_rows($sql);

			//Corpora owned by the user
			$sql_owned_corpora = "SELECT id as corpus_id, name FROM corpora WHERE user_id = ? ORDER BY name";
			$corpus['user_owned_corpora'] = $db->fetch_rows($sql_owned_corpora, array($user['user_id']));

            //Private corpora that the user has access to
            $sql_private_corpora = "SELECT c.id AS corpus_id, c.name, u.screename FROM corpora c LEFT JOIN users u ON c.user_id = u.user_id LEFT JOIN users_corpus_roles ucs ON c.id=ucs.corpus_id WHERE (ucs.user_id= ? AND ucs.role='". CORPUS_ROLE_READ ."') AND c.user_id != ? AND c.public = 0 GROUP BY c.id";
            $corpus['private_corpora'] = $db->fetch_rows($sql_private_corpora, array($user['user_id'], $user['user_id']));
		}

        //Public corpora
        $sql_public_corpora = "SELECT c.id as corpus_id, c.name, u.screename FROM corpora c LEFT JOIN users u ON c.user_id = u.user_id WHERE c.public = 1 ORDER BY c.name";
        $corpus['public_corpora'] = $db->fetch_rows($sql_public_corpora);
		return $corpus;
	}

    /**
     * Returns current document id passed as a variable in the $_POST or $_GET.
     * @return int
     */
	static function getDocumentId(){
        return isset($_REQUEST['id']) ? intval($_REQUEST['id']) : (isset($_REQUEST['report_id']) ? intval($_REQUEST['report_id']) : null);
    }
}
?>
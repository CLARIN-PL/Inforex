<?php
/**
 * Formularz do ustawienia filtrowania raportów.
 * 
 * Filtrowanie obejmuje:
 * - status dokumentu (sprawdzony, odrzucony, zaakceptowany, itd.)
 * - rodzaj raportu.
 */
class Page_set_filter extends CPage{
	
	function execute(){
		$statues = db_reports_get_statuses();
		$types = db_reports_get_types();
		
		$this->set('statuses', $statues);
		$this->set('types', $types);
	}
}


?>

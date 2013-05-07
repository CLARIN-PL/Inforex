<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class PerspectiveEvent_groups extends CCorpusPerspective {
	
	function execute()
	{
		global $corpus, $db;
		$sql = "SELECT eg.event_group_id AS id, eg.name, eg.description, ceg.corpus_id AS cid " .
				"FROM event_groups eg " .
				"LEFT JOIN corpus_event_groups ceg ON (eg.event_group_id = ceg.event_group_id AND ceg.corpus_id = ?)";
		$this->page->set('eventList', $db->fetch_rows($sql, array($corpus['id'])));
	}
}
?>

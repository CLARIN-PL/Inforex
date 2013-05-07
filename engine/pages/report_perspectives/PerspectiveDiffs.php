<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
include_once "Text/Diff.php";
include_once "Text/Diff/Renderer.php";
include_once "Text/Diff/Renderer/inline.php";
include_once "Text/Diff/Renderer/context.php";
include_once "Text/Diff/Renderer/unified.php";

class PerspectiveDiffs extends CPerspective {
	
	function execute()
	{
				
		$diffs = db_fetch_rows("SELECT d.*, u.screename FROM reports_diffs d JOIN users u USING (user_id) WHERE report_id = ? ORDER BY `datetime` DESC", array($this->document['id']));
		$before = $this->document['content'];
		
		$df = new DiffFormatter();
		
		foreach ($diffs as $k=>$diff){
			$diff = gzinflate($diffs[$k]['diff']);
			$current = $before;
			$before = xdiff_string_patch($before, $diff, XDIFF_PATCH_REVERSE);
			$diff   = new Text_Diff('auto', array(explode("\n", htmlspecialchars($before)), explode("\n", htmlspecialchars($current)) ));
			$diffs[$k]['diff_raw'] = $df->generateFormatedDiff($before, $current);
			$diffs[$k]['diff'] = xdiff_string_diff( htmlspecialchars($before), htmlspecialchars($current), 0, false);
			$diffs[$k]['datetime'] = date("m.d, Y (H:i)", strtotime($diffs[$k]['datetime']));
		}
		$this->page->set('diffs', $diffs);
	}
	
}

?>

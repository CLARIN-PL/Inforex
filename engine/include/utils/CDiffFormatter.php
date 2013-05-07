<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * Generates a difference between two string in a pretty HTML format.
 * @author Michał Marcińczuk
 * 
 */
class DiffFormatter{
	
	var $_addition_opener = '<div class="added">';
	var $_addition_closer = '</div>';
	var $_deletion_opener = '<div class="deleted">';
	var $_deletion_closer = '</div>';
	var $_unchanged_opener = '<div class="unchaged">';
	var $_unchanged_closer = '</div>';
	
	var $__current_source_line_number = 1;
	
	function __construct(){
	}
	
	/*
	 * Generate line header with line number.
	 */
	function ln($withNumber=true){
		return '<span class="line-number">'.( $withNumber ? $this->__current_source_line_number : "+") . "</span>";		
	}
	
	function _format($diff, $old_lines=null ){
		$this->__current_source_line_number = 1;		
		$lines = array(); 
		foreach ( explode("\n", $diff) as $line ){
			//@@ -2,2 +2,1 @@			
			if (preg_match("/@@ -([0-9]+)(,([0-9]+))? \+([0-9]+)(,[0-9]+)? @@/", $line, $matches)){
				$old_line = $matches[1];				
				$old_count = $matches[2] ? $matches[3] : 1;
				if ($old_lines != null){
					for ( $i = $this->__current_source_line_number; $i<$old_line; $i++){
						$lines[] = $this->_unchanged_opener . $this->ln() . $old_lines[$i-1] . $this->_unchanged_closer;
						$this->__current_source_line_number++;
					}					
				}
				$this->__current_source_line_number = $old_line;
				if ( $old_lines == null )
					if ( $old_count <= 1 )
						$lines[] = 	sprintf("<div class='diff-header'>Linia %d</div>", $old_line);
					else			
						$lines[] = 	sprintf("<div class='diff-header'>Linia od %d do %d</div>", $old_line, $old_count + $old_line - 1);			
			}else{
				$type = $line[0];
				$line = trim(substr($line, 1), "\n");
				if ( $type == "+" )
					$lines[] = $this->_addition_opener . $this->ln(false) . $line . $this->_addition_closer;
				elseif ( $type == "-" ){
					$lines[] = $this->_deletion_opener . $this->ln() . $line . $this->_deletion_closer;
					$this->__current_source_line_number++;
				}elseif ($type == " "){
					$lines[] = $this->_unchanged_opener . $this->ln() . $line . $this->_unchanged_closer;
					$this->__current_source_line_number++;
				}elseif ($type == "\\" && $line == " No newline at end of file")
					$lines[] = "\n";
			}
		}
		if ( $old_lines != null ) {
			for ( $i = $this->__current_source_line_number; $i<=count($old_lines); $i++){
				$lines[] = $this->_unchanged_opener . $this->ln() . $old_lines[$i-1] . $this->_unchanged_closer;
				$this->__current_source_line_number++;
			}
		}					
		
		$lines[] = "<div style='clear: both'></div>";
		
		return implode($lines);		
	}
	
	/**
	 * Generates formated diff from given diff.
	 */
	function formatDiff($diff){
		return $this->_format($diff);
	}
	
	/**
	 * Return formated diff calculated from two strings.
	 */
	function generateFormatedDiff($old, $new){
		$this->__current_source_line_number = 1;
		$diff = xdiff_string_diff( htmlspecialchars($old), htmlspecialchars($new), 0, false);
		$old_lines = explode("\n", htmlspecialchars($old));
		return $this->_format($diff);
	}
	
	/**
	 * Compare strings ignoring preciding and trailing white spaces in every line
	 */
	function diff($source, $target, $withTrim = false){
		if ($withTrim){
			$lines = array();
			foreach ( explode("\n", $source) as $line )
				$lines[] = trim($line);
			$source_trim = implode("\n", $lines);

			$lines = array();
			foreach ( explode("\n", $target) as $line )
				$lines[] = trim($line);
			$target_trim = implode("\n", $lines);
			
			$diff_trim = xdiff_string_diff($source_trim, $target_trim, 0, true);
			$source_lines = explode("\n", $source);
			$target_lines = explode("\n", $target);
			
			$source_line = 0;
			$target_line = 0;
			$diffs = array();
			 
			foreach ( explode("\n", $diff_trim) as $line ){
				if (preg_match("/@@ -([0-9]+)(,([0-9]+))? \+([0-9]+)(,[0-9]+)? @@/", $line, $matches)){
					$source_line = $matches[1];				
					$target_line = $matches[4];
					$diffs[] = $line;
				}
				else{					
					$char = strlen($line) > 0 ? $line[0] : "";
					$line = substr($line, 1);
					
					if ($char == "+"){
						$diffs[] = "+" . $target_lines[$target_line-1];
						$target_line++;
					}elseif ($char == "-"){
						$diffs[] = "-" . $source_lines[$source_line-1];
						$source_line++;						
					}else{
						$diffs[] = $char . $line;
					}
				}
			}
			
			return implode("\n", $diffs);
		}
		else			
			return xdiff_string_diff($source, $target, 0, false);
	}
}
?>
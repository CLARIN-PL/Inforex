<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-01-13
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */
 
function takipi($content){
	$temporary_file_name_in = ".takipi_temp_23kdf9.in";
	$temporary_file_name_out = ".takipi_temp_23kdf9.out";	
	file_put_contents($temporary_file_name_in, $content);
	$cmd = sprintf("takipi -i %s -o %s", $temporary_file_name_in, $temporary_file_name_out);
	$cmd .= " 2>&1 2>/dev/null"; // Required to redirect the std:err. Fixed in php 5.3+
	exec($cmd, $output = array());
	$content_tagged = file_get_contents($temporary_file_name_out);
	unlink($temporary_file_name_in);
	unlink($temporary_file_name_out);
	// Fix output
	$content_tagged = "<doc>$content_tagged</doc>";	
	return $content_tagged;
}
?>

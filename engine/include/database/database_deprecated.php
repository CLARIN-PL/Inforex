<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

/**
 * Fetch single row as assoc array.
 * @param $sql SELECT query statement
 * @return array with the query result
 */
//######################### deprecated functions ##########################
function db_fetch($sql, $args=null){
	global $mdb2, $sql_log;
	if ($sql_log){
                fb(__CLASS__.':'.__METHOD__.'() ('.__FILE__.':'.__LINE__.')', "SQL");
                $time_start = microtime(TRUE);
		fb($sql, "SQL");
	}
	$args = $args == null ? array() : $args;
	
	if (PEAR::isError($sth = $mdb2->prepare($sql)))
		throw new Exception("<pre>{$sth->getUserInfo()}</pre>");
		
	if (PEAR::isError($r = $sth->execute($args)))
		throw new Exception("<pre>{$r->getUserInfo()}</pre>");	
        if ($sql_log){
            fb('Execute time: '.number_format(microtime(TRUE)-$time_start, 6).' s.', "SQL");
        }
	return $r->fetchRow(MDB2_FETCHMODE_ASSOC);			
}


?>

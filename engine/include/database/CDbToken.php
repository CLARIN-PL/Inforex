<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class DbToken{
	
	static function saveToken($report_id, $from, $to, $eos=0){
		global $db;
		$sql = "INSERT INTO tokens(`report_id`, `from`, `to`, `eos`) VALUES(?,?,?,?);";
		$db->execute($sql, array($report_id, $from, $to, $eos));
		return $db->last_id();
	}

	static function get($tokenId){
	    global $db;
	    $sql = "SELECT * FROM tokens WHERE token_id = ?";
	    return $db->fetch($sql, array($tokenId));
    }

    static function updateToken($token_id, $from, $to, $eos=0){
        global $db;
        $sql = "UPDATE `tokens` SET `from` = ?, `to` = ?, `eos` = ? WHERE (`token_id` = ?);";
        $db->execute($sql, array($from, $to, $eos, $token_id));
        return self::get($token_id);
    }

	/**
     * Return list of tokens.
     *
     * @param $report_id
     * @param null $fields, null => 'select *'
     * @param bool $sorted, should tokens be sorted by the `from` field
     * @return mixed
     */
	static function getTokenByReportId($report_id,$fields=null,$sorted=false){
		global $db;
		
		$sql = " SELECT " .
				($fields ? $fields : " * " ) .
				" FROM tokens " .
                " LEFT JOIN orths USING (orth_id)" .
                " WHERE report_id = ? ".
                ($sorted ? "ORDER BY `from`": "");

		return $db->fetch_rows($sql, array($report_id));
	}

    /**
     * Return list of tokens with ctag by report id
     *
     * @param $report_id
     * @return mixed
     */
    static function getTokenByReportIdWitCTagSorted($report_id){
        global $db;

        $sql = " select" .
           " t.report_id, t.from, t.to, o.orth, b.text as 'base', ttc.ctag as 'ctag'" .
           " from tokens t" .
           " left join orths o on t.orth_id = o.orth_id" .
           " left join tokens_tags_optimized tto on  t.token_id = tto.token_id" .
           " left join tokens_tags_ctags ttc on ttc.id = tto.ctag_id" .
           " left join bases b on b.id = tto.base_id" .
           " where t.report_id = ? and tto.disamb = 1" .
           " order by t.from";
        return $db->fetch_rows($sql, array($report_id));
    }



	static function getTokenByReportIdObj($report_id,$fields=null,$sorted=false){
	    $rows = self::getTokenByReportId($report_id, $fields, $sorted);
	    $objs = array();
	    foreach ($rows as $row){
	        $token = new TableToken();
	        $token->assign($row);
	        $objs[] = $token;
        }
	    return $objs;
    }

    static function getTokenCountByReportId($report_id){
	    global $db;
	    $sql = "SELECT COUNT(*) FROM tokens t WHERE t.report_id = ?";
	    return $db->fetch_one($sql, array($report_id));
    }

    static function getTokenCountByCorpusId($corpusId){
        global $db;
        $sql = "SELECT COUNT(*) FROM tokens t JOIN reports r ON (r.id=t.report_id) WHERE r.corpora = ?";
        return $db->fetch_one($sql, array($corpusId));
    }

    static function getTokensByReportIds($report_ids, $fields=null){
		global $db;		
		$sql = "SELECT ".($fields ? $fields : " * " )." FROM tokens " .
				"WHERE report_id IN('" . implode("','",$report_ids) . "') ORDER BY report_id, `from` limit 200000";

        return $db->fetch_rows($sql);
	}

	static function deleteReportTokens($report_id){
		global $db;
		$sql = "DELETE FROM tokens WHERE report_id=?";
		$db->execute($sql, array($report_id));
	}
	
	static function deleteToken($token_id){
		global $db;
		$sql = "DELETE FROM tokens WHERE token_id=?";
		$db->execute($sql, array($token_id));
	}


	static function deleteTokenWithIndexUpdate($tokenId){
	    global $db;
	    $token = DbToken::get($tokenId);
	    $reportId = $token[DB_COLUMN_TOKENS__REPORT_ID];
        $from = $token[DB_COLUMN_TOKENS__FROM];
        $length = intval($token[DB_COLUMN_TOKENS__TO]) - intval($token[DB_COLUMN_TOKENS__FROM]) + 1;
        DbToken::deleteToken($tokenId);

        $sql = "UPDATE tokens SET `from` = `from` - $length, `to` = `to` - $length WHERE report_id = ? AND `from` > ?";
        $db->execute($sql, array($reportId, $from));
    }

	static function clean(){
		global $db;
		$sql = "DELETE t.* FROM tokens t".
				" LEFT JOIN reports ON (t.report_id = reports.id) ".
				" WHERE reports.id IS NULL";
		$db->execute($sql);

		DbToken::cleanAfterDelete();
	}
	
	static function cleanAfterDelete(){
		DbTag::clean();
		DbBase::clean();
	}
}
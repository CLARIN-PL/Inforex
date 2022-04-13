<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class DbReport{

	static function getByFilenameAndCorpusId($basename, $corpusId){
		global $db;
		$sql = "SELECT * FROM reports WHERE filename = ? AND corpora = ?";
		return $db->fetch_rows($sql, array($basename, $corpusId));
	}

	/**
	 * Return list of reports. 
	 */
	static function getReportsByCorpusId($corpus_id,$fields=null){
		global $db;
		
		$sql = " SELECT " .
				($fields ? $fields : " * " ) .
				" FROM reports " .
				" WHERE corpora = ?";

		return $db->fetch_rows($sql, array($corpus_id));
	}



	/**
	 * Return list of reports with limit 
	 */
	static function getReportsByCorpusIdLimited($corpus_id,$limit_from,$limit_to,$fields=null){
		global $db;
		
		$sql = " SELECT " .
				($fields ? $fields : " * " ) .
				" FROM reports " .
				" WHERE corpora=? " .
				" LIMIT ". $limit_from .", " . $limit_to . " ";
		return $db->fetch_rows($sql, array($corpus_id));
	}	
	
	/**
	 * Return list of reports
	 * Input (reports.corpora, select, join, where, group_by)
	 * Return (select)
	 */
	static function getReportsByCorpusIdWithParameters($corpus_id,$select,$join,$where,$group_by){
  		global $db;
  		$sql = "SELECT ".
  				$select .
  				" FROM reports r " .
  				$join.  				
  				" WHERE r.corpora=? " .
  				$where .
  				$group_by;
  		return $db->fetch_rows($sql, array($corpus_id));
	}

	/*
	 * Changes the subcorpus that the report belongs to.
	 */
	static function changeReportsSubcorpus($subcorpus_id, $document){
		global $db;

        $sql = 'UPDATE reports
                            SET subcorpus_id = ?
                            WHERE id= ?';
        $db->execute($sql, array($subcorpus_id, $document));
	}
	
	/**
	 * Return a list of reports
	 * Input (reports.corpora, select, join, where, group_by)
	 * Return (select)
	 */
	static function getReportsByReportsListWithParameters($report_ids,$select,$join,$where,$group_by){
  		global $db;
  		$sql = "SELECT $select".
  				" FROM reports r " .
  				$join.  				
  				" WHERE r.id IN  ('". implode("','",$report_ids) ."') $where" .
  				$group_by;
		return $db->fetch_rows($sql);
	}


  							
	/**
	 * Return list of reports with specified corpus OR subcorpus OR document_id AND flags.
	 * 
	 * @param $flags Tablica asocjacyjna "skrócona nazwa flagi" => array(flag_id) 
	 */
	static function getReports($corpus_id=null,$subcorpus_id=null,$documents_id=null, $flags=null, $fields=null){
		global $db;
		$where = array();
		
		if ( $corpus_id && !is_array($corpus_id))
			$corpus_id = array($corpus_id);
		if ( $subcorpus_id && !is_array($subcorpus_id))
			$subcorpus_id = array($subcorpus_id);
		if ( $documents_id && !is_array($documents_id))
			$documents_id = array($documents_id);
		
		if ( $corpus_id  && count($corpus_id) > 0)
			$where[] = "r.corpora IN (" . implode(",", $corpus_id) . ")";
		if ( $subcorpus_id  && count($subcorpus_id) > 0)
			$where[] = "r.subcorpus_id IN (" . implode(",", $subcorpus_id) . ")";
		if ( $documents_id  && count($documents_id) > 0)
			$where[] = "r.id IN (" . implode(",", $documents_id) . ")";
			
		$sql_fields = "r.*";
		if ( $fields !== null ){
			if ( !is_array($fields) ){
				$fields = array("id");
			}
			$sql_fields = array();
			foreach ($fields as $field){
				$sql_fields[] = "r.$field";
			}
			$sql_fields = implode(", ", $sql_fields);
		}
			
		$sql = " SELECT r.id, $sql_fields, reports_formats.format, cs.*" .
				" FROM reports r" .
				" LEFT JOIN reports_formats ON(r.format_id = reports_formats.id)".
				" LEFT JOIN corpus_subcorpora cs USING (subcorpus_id) " .
				(count($where)>0 ? " WHERE " . implode(" OR ", $where) : "");
		$reports = $db->fetch_rows($sql);
		
		/** Pobierz flagi dla poszczególnych dokumentów */
		$flag_names = array();
		if ( $flags !== null ) {
			$flag_names = array_keys($flags);
        }
        $flag_names = array_map('strtolower', $flag_names);

        if ( $flags !== null && count($flags)>0 ){
			$sql = "SELECT r.id, cf.short, rf.flag_id" .
					" FROM reports_flags rf " .
					" JOIN reports r ON r.id = rf.report_id" .
					" JOIN corpora_flags cf USING (corpora_flag_id)" .
					" WHERE LOWER(cf.short) IN ('" . implode("','", $flag_names) . "')" .
					"   AND (" . implode(" OR ", $where) . ")";
			$report_flags = array();
			foreach ($db->fetch_rows($sql) as $row){
				$report_flags[sprintf("%s-%s-%s", $row['id'], strtolower($row['short']), $row['flag_id'])] = 1;
			}

			/** Filter by flags */
			$reports2 = array();
			foreach ($reports as $row){
				$has_flags = true;
				foreach ($flags as $flag_name=>$flag_values){
					$has_flag_or = false;
					foreach ($flag_values as $flag_value){
						$key = sprintf("%s-%s-%s", $row['id'], trim(strtolower($flag_name)), trim($flag_value));
						$has_flag_or = $has_flag_or || isset($report_flags[$key]);
					}
					$has_flags = $has_flags && $has_flag_or;
				}
				if ( $has_flags ) {
                    $reports2[] = $row;
                }
			}
			$reports = $reports2;
		}
		
		return $reports;
	}
	
	/**
	 * Zwraca listę dokumentów dla danego selektora.
	 * 
	 * @param $selector Selektor tekstowy do wyboru dokumentów z bazy danych, np. corpus_id=7&flag:names=3,4
	 */
	static function getReportsBySelector($selector, $fields=null){
		$conds = explode("&", $selector);
		
		$corpus_id = null;
		$subcorpus_id = null;
		$report_id = null;
		$flags = array();
		
		foreach ($conds as $cond){
			$parts = explode("=", $cond);
			if ( count($parts) != 2 ){
				throw new Exception("Niepoprawna postać selektora: " . $selector);
			}
			$name = $parts[0];
			$value = $parts[1]; 
			if ( $name == "corpus_id" ){
				$corpus_id = intval($value);			
			}
			elseif ( $name == "subcorpus_id" ){
				$subcorpus_id = intval($value);			
			}
			elseif ( $name == "report_id" ){
				$report_id = intval($value);			
			}
			elseif ( substr($name, 0, 5) === "flag:" ){
				$name = substr($name, 5);
				$flags[$name] = array_map("trim", explode(",", $value));
			}
			else{
				throw new Exception("Nieznany selektor: " . $parts[0]);				
			}
		}
		$reports = DbReport::getReports($corpus_id, $subcorpus_id, $report_id, $flags, $fields);
		return $reports;
	}

	/**
	 * Get a list of documents for corpus id (corpus_id) with extended set of attributes.
	 * @param $corpus_id — corpus identifier
	 * @return array of assoc arrays
	 */
	static function getExtReports($corpus_id){
		global $db;
		$ext = DbCorpus::getCorpusExtTable($corpus_id);
		$sql = "SELECT *" .
				" FROM reports r" .
				" JOIN $ext e ON (r.id = e.id)" .
				" WHERE r.corpora = ?";
		return $db->fetch_rows($sql, array($corpus_id));
	}

	/**
	 * @param $corpus_id — id of corpus
	 * @param $where     — assoc array of string 'column name' => 'column value', 
	 * 			  		    contains attributes from 'reports' table,
	 * @param $where_ext — assoc array of string 'column name' => 'column value', 
	 * 					    contains attributes from extended table ext_?.
	 */
	static function getExtReportsFiltered($corpus_id, $where, $where_ext){
		global $db;
		
		$params = array($corpus_id);
		$cols = array("r.corpora = ?");
		foreach ( $where as $k=>$v){
			$cols[] = "r.$k = ?";
			$params[] = $v;
		}
		foreach ( $where_ext as $k=>$v){
			$cols[] = "e.$k = ?";
			$params[] = $v;
		}
		
		$ext = DbCorpus::getCorpusExtTable($corpus_id);
		$sql = "SELECT *" .
				" FROM reports r" .
				($where_ext ? " JOIN $ext e ON (r.id = e.id)" : "") .
				" WHERE " . implode(" AND ", $cols);
		return $db->fetch_rows($sql, $params);
	}

	
	/**
	 * Get report with extended set of attributes from given table.
	 */
	static function getReportExtByIds($report_ids, $ext){
		global $db;
		$sql = "SELECT * FROM $ext WHERE id IN (" . implode(", ", $report_ids) . ")";
		return $db->fetch_rows($sql);			
	}

    /**
     * Get report by id.
     */
    static function get($reportId){
        return self::getReportById($reportId);
    }

	/**
	 * Get report by id.
	 */
	static function getReportById($report_id){
		global $db;
		$sql = "SELECT * FROM reports WHERE id = ?";
		return $db->fetch($sql, array($report_id));
	}
	
	static function getReportExtById($report_id){
		global $db;
		
		$report = DbReport::getReportById($report_id);
		$corpus = DbCorpus::getCorpusById($report['corpora']);
		if ( $corpus['ext'] ){
			$sql = "SELECT * FROM {$corpus['ext']} WHERE id = ?";
			return $db->fetch($sql, $report_id);
		}else
			return null;
	}

    static function getCustomMetadataTextColumns($corpus_ext){
        $columns = DbCorpus::getCorpusExtColumns($corpus_ext);

        $insert_columns = array();
        $insert_values = array();

        foreach($columns as $column){
            if($column['type'] == "text" && $column['default'] != "empty"){
            	$insert_columns[] = $column['field'];
                $insert_values[] = $column['default'];
            }
        }

        $default_values = array(
        	'insert_columns' => $insert_columns,
			'insert_values' => $insert_values
		);

        return $default_values;
    }

	static function insertEmptyReportExt($report_id){
		global $db;
		$report = DbReport::getReportById($report_id);
		$corpus = DbCorpus::getCorpusById($report['corpora']);
		$ext = $corpus['ext'];

        if ( $ext ){
            $default_values = self::getCustomMetadataTextColumns($ext);
			array_unshift($default_values['insert_columns'], 'id');
			array_unshift($default_values['insert_values'], $report_id);

			ChromePhp::log($default_values);

			$fields = implode(",", $default_values['insert_columns']);
            $values = implode(", ", array_fill(0, count($default_values['insert_values']), "?"));

			$sql = "INSERT IGNORE INTO {$corpus['ext']} (".$fields.") VALUES(".$values.")";
			$db->execute($sql, $default_values['insert_values']);
		}
	}
	
	static function updateReportExt($report_id, $metadata_ext){
		global $db;
		$report = DbReport::getReportById($report_id);
		$corpus = DbCorpus::getCorpusById($report['corpora']);
		$ext = $corpus['ext']; 
		$args = array();
		$columns = array();
		if ($ext){
			foreach ($metadata_ext as $k=>$v){
				if ( $v === null ) {
					$columns[] = "`$k` = NULL";								
				}
				else{
					$columns[] = "`$k` = ?";
					$args[] = $v;
				}
			}
			$args[] = $report_id;
	
			$sql = "UPDATE $ext SET " . implode(", ", $columns) . " WHERE id = ?";
			$db->execute($sql, $args);
		}		
	}
	
	
	/**
	 * Insert Report Diffs
	 * data - array(column1=>value1, column2=>value2,...)
	 * reports_diffs column:
	 * diff_id, report_id, datetime, user_id, diff, comment
	 */
	static function insertReportDiffs($data){
		global $db;
		
		$columns = array();
	 	$parameters = array();
	 	
	 	foreach ($data as $k=>$v){
			$columns[] = "`".$k."`";
			$parameters[] = "'".$db->escape($v)."'";
	 	}
		
		$db->execute("INSERT INTO reports_diffs (".implode(", ", $columns).") VALUES(".implode(", ", $parameters).")");
	}

	static function getReportsCount($corpus_id,$subcorpus_id=null){
		global $db;
		$sql = "SELECT COUNT(*) FROM reports WHERE corpora = ?";
		$args = array($corpus_id);
		if ( $subcorpus_id ){
			$sql .= " AND subcorpus_id = ?";
			$args[] = $subcorpus_id;
		}
		return $db->fetch_one($sql, $args);
	}
	
	static function getTokensFlagId($corpus_id){
		global $db;
		$corpora_flag_id = $db->fetch_one(
		 			"SELECT corpora_flag_id " .
		 			"FROM corpora_flags " .
		 			"WHERE corpora_id=? " .
		 			"AND short=\"Tokens\"", array($corpus_id));
		return $corpora_flag_id;
	}
	
	static function documentTokenized($corpus_id, $report_id){
		global $db;
		$flag = $db->fetch_one(
				"SELECT flag_id ".
				"FROM reports_flags ".
				"JOIN corpora_flags ".
				"USING ( corpora_flag_id ) ".
				"WHERE corpora_id =? ".
				"AND short = \"Tokens\" ".
				"AND report_id =?",
				array($corpus_id, $report_id)
		);
		
		return $flag && !in_array($flag, array(-1,1,2));
	}
		
	static function updateFlag($report_id,$corpus_id, $flag_id){
		global $db;
		if(DbReport::documentTokenized($corpus_id, $report_id)){
			$corpora_flag_id = DbReport::getTokensFlagId($corpus_id, $report_id);
			if($corpora_flag_id){
				$db->execute(
						"REPLACE reports_flags (corpora_flag_id, report_id, flag_id) " .
						"VALUES (?,?,?)", array($corpora_flag_id, $report_id, $flag_id));
			}
		}
	}
	
	static function getAllFormats(){
		global $db;
		$sql = "SELECT id, format FROM reports_formats";
		$formats = $db->fetch_rows($sql);
		return $formats;
	}
	
	static function getAllFormatsByName(){
		global $db;
		$sql = "SELECT id, format FROM reports_formats";
		$formats = $db->fetch_rows($sql);
		$result = array();
		foreach($formats as $format){
			$result[$format['format']] = $format['id'];
		}
		return $result;
	}
	
	static function formatName($format_id){
		global $db;
		$sql = "SELECT format FROM reports_formats WHERE id = ?";
		return $db->fetch_one($sql, array($format_id));
	}

	
	static function deleteReport($report_id){
		global $db;
		$sql = "DELETE FROM reports WHERE id={$report_id}";
		$db->execute($sql);
	}
	
	static function cleanAfterDelete(){
		DbToken::clean();
	}

	static function getReportTokenCount($report_id = null, $corpus_id = null){
        global $db;

        // returning token count for one report
        if($report_id !== null){
            $sql = "SELECT count(*) as token_cnt 
            		FROM `tokens`
            		WHERE report_id = ". $report_id . "
            		GROUP BY report_id;";

            return $db->fetch_one($sql);
		}

		// returning token count for reports in corpus
        $sql = "SELECT count(*) as token_cnt, report_id
            		FROM `tokens` `tok`
            		JOIN `reports` `rep` on rep.id = tok.report_id
            		WHERE rep.corpora = " . $corpus_id .
					" GROUP BY report_id;";

        return $db->fetch_rows($sql);
	}

	static function getReportTypes(){
		global $db;
        $sql = "SELECT * FROM reports_types ORDER BY name";

        return $db->fetch_rows($sql);
	}

	static function getReportStatuses(){
		global $db;
		$sql = "SELECT * FROM reports_statuses ORDER BY status";

		return $db->fetch_rows($sql);
	}

	static function getParentReport($id){
		global $db;
		$sql = "SELECT * FROM reports where id = ?";
		$parent_report = $db->fetch_rows($sql, array($id));
		return $parent_report[0];
	}

	static function getReportTranslationLanguages($report_id){
		global $db;
		$sql = "SELECT DISTINCT l.language, r.lang AS 'code' FROM reports r
				LEFT JOIN lang l ON r.lang = l.code
				WHERE r.parent_report_id = ?";
		$languages = $db->fetch_rows($sql, array($report_id));
		return $languages;
	}

	static function getReportTranslations($report_id){
        global $db;
        $sql = "SELECT r.id, r.content, l.language, r.lang AS 'code' FROM reports r
				LEFT JOIN lang l ON r.lang = l.code
				WHERE r.parent_report_id = ?";
        $translations = $db->fetch_rows($sql, array($report_id));
        return $translations;
	}

	static function getReportsByFilter($search, $corpus_id, $page){
		global $db;
		$results_per_page = 10;
		$upper_limit = $page * $results_per_page;
		$lower_limit = $upper_limit - $results_per_page;

		$next_upper_limit = $upper_limit + $results_per_page;
		$next_lower_limit = $lower_limit + $results_per_page;

		$sql = "SELECT id, title AS 'text' FROM reports
				WHERE corpora = ? AND (id LIKE CONCAT('%', ?, '%') OR title LIKE CONCAT('%', ?, '%'))
				LIMIT ?, ?";
		$reports = $db->fetch_rows($sql, array($corpus_id, $search, $search, $lower_limit, $upper_limit));
		$next_reports = $db->fetch_rows($sql, array($corpus_id, $search, $search, $next_lower_limit, $next_upper_limit));

		$results = array(
			'results' => $reports,
			'pagination' => array(
				"more" => !empty($next_reports)
			)
		);

		return $results;

	}

	static function getLanguagesByFilter($search, $page){
        global $db;

        $results_per_page = 10;
        $upper_limit = $page * $results_per_page;
        $lower_limit = $upper_limit - $results_per_page;

        $next_upper_limit = $upper_limit + $results_per_page;
        $next_lower_limit = $lower_limit + $results_per_page;

        $sql = "SELECT code AS 'id', language AS 'text' FROM lang
				WHERE code LIKE CONCAT('%', ?, '%') OR language LIKE CONCAT('%', ?, '%')
				LIMIT ?, ?";

        $languages = $db->fetch_rows($sql, array($search, $search, $lower_limit, $upper_limit));
        $next_languages = $db->fetch_rows($sql, array($search, $search, $next_lower_limit, $next_upper_limit));

        $results = array(
            'results' => $languages,
            'pagination' => array(
                "more" => !empty($next_languages)
            )
        );

        return $results;

    }

    static function getFullLanguageName($code){
		global $db;

		$sql = "SELECT language FROM lang WHERE code = ?";
		$language = $db->fetch_one($sql, array($code));
		return $language;
	}

	static function getReportContentAndFormatById($report_id){
		global $db;
		$sql = "SELECT r.id, r.content, f.format AS 'format' FROM reports r
				LEFT JOIN reports_formats f ON r.format_id = f.id
				WHERE r.id = ?";
		$report_data = $db->fetch_rows($sql, array($report_id));
		return $report_data[0];
	}
}
?>

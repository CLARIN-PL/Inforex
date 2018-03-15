<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class DbCorpus{

	static function getCorpora($public = 1){
		global $db;
		$sql = "SELECT c.*, COUNT(r.id) AS `reports`" .
				" FROM corpora c" .
				" LEFT JOIN reports r ON (c.id = r.corpora)" .
				" WHERE c.public = ? ".
				" GROUP BY c.id" .
				" ORDER BY c.name";
		$corpora = $db->fetch_rows($sql, array($public));
		return $corpora;
	}
	
	static function getPrivateCorporaForUser($user_id, $is_admin){
		global $db;
		$sql = "SELECT c.*, COUNT(r.id) AS `reports`, u.screename" .
				" FROM corpora c" .
                " LEFT JOIN users u ON (u.user_id = c.user_id)" .
				" LEFT JOIN reports r ON (c.id = r.corpora)" .
				" LEFT JOIN users_corpus_roles cr ON (c.id=cr.corpus_id AND cr.user_id=? AND role='". CORPUS_ROLE_READ ."')" .
				" WHERE (c.user_id = ?" .
				"    OR cr.user_id = ?" .
				"    OR 1=?)" .
				"	 AND c.public = 0" .
				" GROUP BY c.id" .
				" ORDER BY c.name";
		
		$corpora = $db->fetch_rows($sql,array($user_id, $user_id, $user_id, $is_admin));
		return $corpora;	
	}
	
	static function getCorpusById($corpus_id){
		global $db;
		$sql = "SELECT * FROM corpora WHERE id = ?";
		return $db->fetch($sql, array($corpus_id));
	}

	/**
	 * Return subcorpus data for given id.
	 * @param unknown $subcorpus_id
	 * @return {Array}
	 */
	static function getSubcorpusById($subcorpus_id){
		global $db;
		$sql = "SELECT * FROM corpus_subcorpora WHERE subcorpus_id = ?";
		return $db->fetch($sql, array($subcorpus_id));
	}
	
	/**
	 * Return list of subcorpus. 
	 */
	static function getCorpusSubcorpora($corpus_id){
		global $db;
		
		$sql = "SELECT *" .
				" FROM corpus_subcorpora" .
				" WHERE corpus_id = ?";
		return $db->fetch_rows($sql, array($corpus_id));
	}
	
	/**
	 * Return list of corpus flags. 
	 */
	static function getCorpusFlags($corpus_id){
		global $db;
		
		$sql = "SELECT short, corpora_flag_id " .
				"FROM corpora_flags " .
				"WHERE corpora_id = ?";
		return $db->fetch_rows($sql, array($corpus_id));
	}
	
	/**
	 * Return list of corpus reports ids. 
	 */
	static function getCorpusReports($corpus_id){
		global $db;
		
		$sql = "SELECT id " .
				"FROM reports " .
				"WHERE corpora = ?";
		return $db->fetch_rows($sql, array($corpus_id));
	}
	
	/**
	 * Return corpus id by report id. 
	 */
	static function getCorpusByReportId($report_id){
		global $db;
		
		$sql = "SELECT corpora " .
				"FROM reports " .
				"WHERE id = ?";
		return $db->fetch_one($sql, array($report_id));
	}
	
	/**
	 * Return name of a table with additional document fields.
	 */
	static function getCorpusExtTable($corpus_id){
		global $db;
		$sql = "SELECT ext FROM corpora WHERE id = ?";
		return $db->fetch_one($sql, array($corpus_id));
	}
	
	/**
	 * Return array of table columns with their description.
	 */
	static function getCorpusExtColumns($table_name){
		global $db;
		if (!$table_name){
			return array();
		}
		else{
			$sql = "SHOW FULL COLUMNS FROM $table_name WHERE `key` <> 'PRI'";
			$rows = $db->fetch_rows($sql);
			$fields = array();
			foreach ($rows as &$row){
				$field = array();
				if (!isset($row['Field'])){
					throw new Exception("Attribute called Field not found");
				}
				$name_and_comment = explode("###", $row['Comment']);
				$field['field'] = $row['Field'];
				$field['comment'] = $name_and_comment[1];
				$field['field_name'] = $name_and_comment[0];

				if(isset($name_and_comment[2])){
				    $field['default'] = $name_and_comment[2];
                } else{
                    if($row['Default'] != null){
                        $field['default'] = $row['Default'];
                    } else{
                        $field['default'] = 'empty';
                    }
                }

                if ($row['Null'] == 'YES') {
                    $field['null'] = "Yes";
                } else{
                    $field['null'] = "No";
                }

				if (preg_match('/^enum\((.*)\)$/', $row['Type'], $match)){
					$field['type'] = 'enum';
					$values = array();
					foreach ( split(",", $match[1]) as $v )
						$values[] = trim($v, "'");
					$field['field_values'] = $values; 
				}
				else
					$field['type'] = 'text';
				$fields[] = $field;		
			}
			return $fields;
		}
	}

	static function getCorpusAllMetadataColumns($corpus_id){
        $ext = self::getCorpusExtTable($corpus_id);
        $metadata_columns = array(
            'Title',
            'Author',
            'Source',
            'Subcorpus',
            'Format',
            'Status',
            'Date'
        );
        if($ext != null) {
            $columns = self::getCorpusExtColumns($ext);

            foreach($columns as $column){
                $metadata_columns[] = $column['field'];
            }
        }

        ChromePhp::log($metadata_columns);
        return $metadata_columns;
    }


    /**
     * Return array of table columns with their description.
     */
    static function getCorpusExtColumnsWithMetadata($table_name){
        global $db;
        if (!$table_name){
            return array();
        }
        else{
            $sql = "SHOW FULL COLUMNS FROM $table_name WHERE `key` <> 'PRI'";
            $rows = $db->fetch_rows($sql);

            $fields = array();
            foreach ($rows as &$row){
                $field = array();
                if (!isset($row['Field'])){
                    throw new Exception("Attribute called Field not found");
                }
                $field['field'] = $row['Field'];
                $field['comment'] = $row['Comment'];
                if ($row['Null'] == 'YES') {
                    $field['null'] = "Yes";
                } else{
                    $field['null'] = "No";
                }

                if (preg_match('/^enum\((.*)\)$/', $row['Type'], $match)){
                    $field['type'] = 'enum';
                    $values = array();
                    foreach ( split(",", $match[1]) as $v )
                        $values[] = trim($v, "'");
                    $field['field_values'] = $values;
                }
                else{
                    $field['type'] = 'text';

                    $sql = "SELECT ".$field['field']." AS 'name', COUNT( ".$field['field'].") AS count_field FROM ".$table_name."
                            WHERE ".$field['field'] ." != '' 
                            GROUP BY " . $field['field'] .
                            " ORDER BY count_field DESC";
                    $data = $db->fetch_rows($sql);
                    if(count($data) < 30){
                        $field['data'] = $data;
                    } else{
                        $field['data'] = 'oob';
                    }
                }
                if(!isset($field['data']) || ($field['data'] !== 'oob' && !empty($field['data']))){
                    $fields[] = $field;
                }
            }
            return $fields;
        }
    }

    static function getBasicMetadata($corpus_id){
        $basic_metadata = array();

        $basic_metadata['subcorpora'] = DbCorpus::getCorpusSubcorpora($corpus_id);
        $basic_metadata['statuses'] = DbStatus::getAll();
        $basic_metadata['formats'] = DbReport::getAllFormats();

        ChromePhp::log($basic_metadata);
        return $basic_metadata;
    }

    static function getDocumentsWithMetadata($corpus_id){
        global $db;
        $ext = self::getCorpusExtTable($corpus_id);
        ChromePhp::log($ext);
        if($ext != null){
            $basic_meta = self::getBasicMetadata($corpus_id);
            $columns = self::getCorpusExtColumns($ext);
            $basic_metadata_columns = array("Report ID", "Filename", "Title", "Author", "Source", "Subcorpus", "Format", "Status", "Date");
            $basic_metadata = array();
            foreach($basic_metadata_columns as $basic){
                $meta['field'] = $basic;
                if($basic === "Subcorpus"){
                    $subcorpora = DbCorpus::getCorpusSubcorpora($corpus_id);
                    foreach($subcorpora as $subcorpus){
                        $meta['field_values'][] = $subcorpus['name'];
                        $meta['field_ids'][] = $subcorpus['subcorpus_id'];
                        $meta['type'] = 'enum';
                    }
                }
                $basic_metadata[] = $meta;
            }
            $columns = array_merge($basic_metadata, $columns);
            $sql = "SELECT r.id AS 'Report ID', r.filename AS 'Filename', r.title AS 'Title', r.author AS 'Author', r.source AS 'Source', 
                    cs.subcorpus_id AS 'Subcorpus', 
                    rf.format AS 'Format', rf.id AS 'format_id',
                    rs.status AS 'Status', rs.id AS 'status_id', 
                    r.date AS 'Date', ext.* FROM reports r 
                    JOIN " . $ext . " ext ON ext.id = r.id
                    LEFT JOIN corpus_subcorpora cs ON cs.subcorpus_id = r.subcorpus_id
                    LEFT JOIN reports_formats rf ON rf.id = r.format_id
                    LEFT JOIN reports_statuses rs ON rs.id = r.status";

            $documents = $db->fetch_rows($sql);

            $metadata['documents'] = $documents;
            $metadata['columns'] = $columns;
            return $metadata;
        } else{
            return array('columns' => array(), 'documents' => array());
        }
    }

    static function batchUpdateMetadata($corpus_id, $batchUpdateMetadata){
        global $db;

        $ext = self::getCorpusExtTable($corpus_id);
        foreach($batchUpdateMetadata as $metadata_update){
            $sql = "UPDATE " . $ext . " SET " . $metadata_update['field'] . " = '" . $metadata_update['value'] . "' 
            WHERE id = ?";
            //$db->execute($sql, array($metadata_update['report_id']));
        }
    }
	
	/**
	 * Zwraca listę wszystkich podkorpusów.
	 */
	static function getSubcorpora(){
		global $db;
		$sql = "SELECT * FROM corpus_subcorpora";
		return $db->fetch_rows($sql);
	}
	
	/**
	 * 
	 * @param unknown $corpus_id
	 * @param unknown $name
	 * @param unknown $description
	 * @return subcorpus id
	 */
	static function createSubcopus($corpus_id, $name, $description){
		global $db;
		$sql = "INSERT INTO corpus_subcorpora (corpus_id, name, description) VALUES (?, ?, ?) ";		
		$db->execute($sql, array($corpus_id, $name, $description));
		return $db->last_id();
	}
}

?>
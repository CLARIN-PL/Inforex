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

	static function deleteCorpus($corpusId){
	    global $db;
	    
	    try {
            $db->execute("SET autocommit=0;");
            $db->execute("START TRANSACTION;");
            $corpus_id = array($corpusId);

            //annotation_sets_corpora
            $sql = "DELETE FROM annotation_sets_corpora WHERE annotation_set_id = ?;";
            $db->execute($sql, $corpus_id);

            //activities
            $sql = "DELETE FROM activities WHERE corpus_id = ?;";
            $db->execute($sql, $corpus_id);

            //corpora_relations
            $sql = "DELETE FROM corpora_relations WHERE corpus_id = ?;";
            $db->execute($sql, $corpus_id);

            //corpus_and_report_perspectives
            $sql = "DELETE FROM corpus_and_report_perspectives WHERE corpus_id = ?;";
            $db->execute($sql, $corpus_id);

            //corpus_event_groups
            $sql = "DELETE FROM corpus_event_groups WHERE corpus_id = ?;";
            $db->execute($sql, $corpus_id);

            //Get exports_ids
            $sql = "SELECT export_id FROM exports WHERE corpus_id = ?;";
            $export_ids = $db->fetch_ones($sql, 'export_id', $corpus_id);

            if($export_ids){
                //corpus_event_groups
                $sql = "DELETE FROM export_errors WHERE export_id IN (".implode(",", array_fill(0, count($export_ids), "?")).");";
                $db->execute($sql, $export_ids);
            }

            //exports
            $sql = "DELETE FROM exports WHERE corpus_id = ?;";
            $db->execute($sql, $corpus_id);

            //tasks
            $sql = "DELETE FROM tasks WHERE corpus_id = ?;";
            $db->execute($sql, $corpus_id);

            //users_corpus_roles
            $sql = "DELETE FROM users_corpus_roles WHERE corpus_id = ?;";
            $db->execute($sql, $corpus_id);

            //wccl_rules
            $sql = "DELETE FROM wccl_rules WHERE corpus_id = ?;";
            $db->execute($sql, $corpus_id);

            //------- Handling reports --------
            //Get report ids
            $sql = "SELECT id FROM reports WHERE corpora = ?;";
            $report_ids = $db->fetch_ones($sql, 'id', $corpus_id);

            if($report_ids){
                //flag_status_history
                $sql = "DELETE FROM flag_status_history WHERE report_id IN (".implode(",", array_fill(0, count($report_ids), "?")).");";
                $db->execute($sql, $report_ids);

                //flag_status_history
                $sql = "DELETE FROM flag_status_history WHERE report_id IN (".implode(",", array_fill(0, count($report_ids), "?")).");";
                $db->execute($sql, $report_ids);

                //reports_and_images
                $sql = "DELETE FROM reports_and_images WHERE report_id IN (".implode(",", array_fill(0, count($report_ids), "?")).");";
                $db->execute($sql, $report_ids);

                //Get annotation ids
                $sql = "SELECT id FROM reports_annotations_optimized WHERE report_id IN(".implode(",", array_fill(0, count($report_ids), "?")).");";
                $annotation_ids = $db->fetch_ones($sql, 'id', $report_ids);
            }


            if($annotation_ids){
                //reports_annotations_attributes
                $sql = "DELETE FROM reports_annotations_attributes WHERE annotation_id IN (".implode(",", array_fill(0, count($annotation_ids), "?")).");";
                $db->execute($sql, $annotation_ids);

                //reports_annotations_attributes
                $sql = "DELETE FROM reports_annotations_lemma WHERE report_annotation_id IN (".implode(",", array_fill(0, count($annotation_ids), "?")).");";
                $db->execute($sql, $annotation_ids);

                //reports_annotations_shared_attributes
                $sql = "DELETE FROM reports_annotations_shared_attributes WHERE annotation_id IN (".implode(",", array_fill(0, count($annotation_ids), "?")).");";
                $db->execute($sql, $annotation_ids);
            }

            if($report_ids){
                //reports_annotations_optimized
                $sql = "DELETE FROM reports_annotations_optimized WHERE report_id IN (".implode(",", array_fill(0, count($report_ids), "?")).");";
                $db->execute($sql, $report_ids);

                //reports_diffs
                $sql = "DELETE FROM reports_diffs WHERE report_id IN (".implode(",", array_fill(0, count($report_ids), "?")).");";
                $db->execute($sql, $report_ids);

                //Get event ids
                $sql = "SELECT report_event_id FROM reports_events WHERE report_id IN (".implode(",", array_fill(0, count($report_ids), "?")).");";
                $event_ids = $db->fetch_ones($sql, 'report_event_id', $report_ids);

                if($event_ids){
                    //reports_events
                    $sql = "DELETE FROM reports_events_slots WHERE report_event_id IN (".implode(",", array_fill(0, count($event_ids), "?")).");";
                    $db->execute($sql, $event_ids);
                }
            }

            if($report_ids){
                //reports_events
                $sql = "DELETE FROM reports_events WHERE report_id IN (".implode(",", array_fill(0, count($report_ids), "?")).");";
                $db->execute($sql, $report_ids);

                //reports_flags
                $sql = "DELETE FROM reports_flags WHERE report_id IN (".implode(",", array_fill(0, count($report_ids), "?")).");";
                $db->execute($sql, $report_ids);

                //reports_users_selection
                $sql = "DELETE FROM reports_users_selection WHERE report_id IN (".implode(",", array_fill(0, count($report_ids), "?")).");";
                $db->execute($sql, $report_ids);

                //tasks_reports
                $sql = "DELETE FROM tasks_reports WHERE report_id IN (".implode(",", array_fill(0, count($report_ids), "?")).");";
                $db->execute($sql, $report_ids);

                //tokens_tags_optimized
                $sql = "DELETE FROM tokens_tags_optimized WHERE token_id IN (SELECT token_id FROM tokens WHERE report_id IN (".implode(",", array_fill(0, count($report_ids), "?"))."));";
                $db->execute($sql, $report_ids);

                //tokens_tags_optimized
                $sql = "DELETE FROM tokens WHERE report_id IN (".implode(",", array_fill(0, count($report_ids), "?")).");";
                $db->execute($sql, $report_ids);

                //relations
                $sql = "DELETE FROM relations WHERE (source_id IN (".implode(",", array_fill(0, count($report_ids), "?")).") OR target_id IN (".implode(",", array_fill(0, count($report_ids), "?"))."));";
                $db->execute($sql, array_merge($report_ids, $report_ids));
            }

            //corpora_flags after reports_flags
            $sql = "DELETE FROM corpora_flags WHERE corpora_id = ?;";
            $db->execute($sql, $corpus_id);

            //images (after reports_and_images)
            $sql = "DELETE FROM images WHERE corpus_id = ?;";
            $db->execute($sql, $corpus_id);

            // corpus_subcorpora (after reports)
            $sql = "DELETE FROM corpus_subcorpora WHERE corpus_id = ?;";
            $db->execute($sql, $corpus_id);


            //Getting metadata table name before deleting the corpus
            $ext = self::getCorpusExtTable($corpusId);
            //Deleting corpus
            $db->execute("DELETE FROM corpora WHERE id=?;", array($corpusId));
            $db->execute("COMMIT;");

            //Dropping metadata table
            //Has to be at the end, DROP TABLE executes even if it's inside a transaction.
            if($ext){
                $sql = "DROP TABLE " . $ext . ";";
                $db->execute($sql);
            }

        }
        catch(Exception $ex){
            $db->execute("ROLLBACK");
        }
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

        return $basic_metadata;
    }

    static function getBasicMetadataColumns(){
        $basic_metadata_columns = array("Report_ID", "Filename", "Title", "Author", "Source", "Subcorpus", "Format", "Status", "Date");
        return $basic_metadata_columns;
    }

    private static function getBasicMetadataInfo($corpus_id){
        $basic_metadata_columns = self::getBasicMetadataColumns();
        $basic_metadata = array();
        foreach($basic_metadata_columns as $basic){
            $meta = array();
            $meta['field'] = $basic;
            if($basic === "Subcorpus"){
                $subcorpora = DbCorpus::getCorpusSubcorpora($corpus_id);
                foreach($subcorpora as $subcorpus){
                    $meta['field_values'][] = $subcorpus['name'];
                    $meta['field_ids'][] = $subcorpus['subcorpus_id'];
                    $meta['type'] = 'enum';
                }
            } else if($basic === "Format"){
                $formats = DbReport::getAllFormats();
                foreach($formats as $format){
                    $meta['field_values'][] = $format['format'];
                    $meta['field_ids'][] = $format['id'];
                    $meta['type'] = 'enum';
                }
            } else if($basic === "Status"){
                $statuses = DbStatus::getAll();
                foreach($statuses as $status){
                    $meta['field_values'][] = $status['status'];
                    $meta['field_ids'][] = $status['id'];
                    $meta['type'] = 'enum';
                }
            }
            $basic_metadata[] = $meta;
        }
        return $basic_metadata;
    }

    static function getDocumentFilenames($corpus_id){
        global $db;

        $sql = "SELECT filename from reports WHERE (corpora = ? AND filename != '')";
        $names = $db->fetch_rows($sql, array($corpus_id));
        return $names;
    }

    static function getDocumentsWithMetadata($corpus_id){
        global $db;
        $ext = self::getCorpusExtTable($corpus_id);
        $basic_metadata = self::getBasicMetadataInfo($corpus_id);
        $metadata = array();
        if($ext != null){
            $columns = array_merge($basic_metadata, self::getCorpusExtColumns($ext));
            $sql = "SELECT r.id AS 'Report_ID', r.filename AS 'Filename', r.title AS 'Title', r.author AS 'Author', r.source AS 'Source', 
                    cs.subcorpus_id AS 'Subcorpus', 
                    rf.id AS 'Format',
                    rs.id AS 'Status',
                    r.date AS 'Date', ext.* FROM reports r 
                    LEFT JOIN " . $ext . " ext ON ext.id = r.id
                    LEFT JOIN corpus_subcorpora cs ON cs.subcorpus_id = r.subcorpus_id
                    LEFT JOIN reports_formats rf ON rf.id = r.format_id
                    LEFT JOIN reports_statuses rs ON rs.id = r.status
                    WHERE r.corpora = ?";

            $documents = $db->fetch_rows($sql, array($corpus_id));

            $metadata['documents'] = $documents;
            $metadata['columns'] = $columns;
        } else{
            $sql = "SELECT r.id AS 'Report_ID', r.filename AS 'Filename', r.title AS 'Title', r.author AS 'Author', r.source AS 'Source', 
                    cs.subcorpus_id AS 'Subcorpus', 
                    rf.id AS 'Format',
                    rs.id AS 'Status',
                    r.date AS 'Date' FROM reports r 
                    LEFT JOIN corpus_subcorpora cs ON cs.subcorpus_id = r.subcorpus_id
                    LEFT JOIN reports_formats rf ON rf.id = r.format_id
                    LEFT JOIN reports_statuses rs ON rs.id = r.status
                    WHERE r.corpora = ?";

            $documents = $db->fetch_rows($sql, array($corpus_id));
            $metadata['documents'] = $documents;
            $metadata['columns'] = $basic_metadata;
        }
        return $metadata;
    }

    static function convertBasicMetadataToDBNames($field){
        switch($field){
            case "Title":
                return "title";
            case "Author":
                return "author";
            case "Source":
                return "source";
            case "Subcorpus":
                return "subcorpus_id";
            case "Format":
                return "format_id";
            case "Status":
                return "status";
            case "Date":
                return "date";
        }
    }

    static function batchUpdateMetadata($corpus_id, $batchUpdateMetadata){
        global $db;

        $ext = self::getCorpusExtTable($corpus_id);
        try{
            foreach($batchUpdateMetadata as $key => $metadata_update){
                //get report_id and field from the key
                $parts = explode("_", $key);
                $report_id = $parts[0];
                array_shift($parts);
                $field = implode("_", $parts);


                $params = array($metadata_update['value'], $report_id);
                if(in_array($field, self::getBasicMetadataColumns())){
                    $sql = "UPDATE reports SET " . self::convertBasicMetadataToDBNames($field ). " = ? 
                WHERE id = ?";
                    $db->execute($sql, $params);
                } else{
                    $sql = "UPDATE ".$ext." SET " . $field . " = ? 
                WHERE id = ?";
                    $db->execute($sql, $params);
                }
            }
            ChromePhp::log("Ok");
            return true;
        }catch(Exception $e){
            ChromePhp::log("Error");
            return false;
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


    static function getSubcorporaByIds($subcorpora_ids, $fields=null){
        global $db;
        $sql = "SELECT ".
            ($fields ? $fields : " * " ).
            " FROM corpus_subcorpora " .
            "WHERE subcorpus_id IN('" . implode("','",$subcorpora_ids) . "') ORDER BY subcorpus_id";
        return $db->fetch_rows($sql);
    }
}

?>
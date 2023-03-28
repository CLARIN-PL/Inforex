<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_corpus_edit_ext extends CPageCorpus {
	
	function execute(){
		global $db, $corpus;

		$action = $_POST['action'];
        $name = $_POST['field'];
        $type = $_POST['type'];
        $comment = $_POST['comment'];
        $field_name = $_POST['field_name'];
        $enum_values = $_POST['enum_values'];
        $default = ($_POST['default'] == "null" || $_POST['default'] == "" ? null : $_POST['default']);
        ChromePhp::log($_POST);

		if ($action == 'get'){
		    // ToDO: Change to DB method
			$sql = "SELECT ext FROM corpora WHERE id=?";
			$ext = $db->fetch_one($sql, array($corpus['id']));
			return DbCorpus::getCorpusExtColumns($ext);
		}			
		elseif ($action == 'add'){
            // ToDO: Change to DB method
			$sql = "SELECT ext FROM corpora WHERE id=?";
			$ext = $db->fetch_one($sql, array($corpus['id']));

			if($ext == null){
                $ext = "reports_ext_" . $corpus['id'];

                if($type == "enum"){
                    // ToDO: Change to DB method
                    $sqlComment = sprintf("%s###%s", $field_name, $comment);
                    $sql = "CREATE TABLE IF NOT EXISTS `".$ext."` (`id` BIGINT(20) AUTO_INCREMENT PRIMARY KEY ,`".$name."` ".$type."(".$enum_values.") "
                            . ($default == null ? "" : " DEFAULT '".$default."' NOT" ) . " NULL COMMENT ('$sqlComment') CHARACTER SET utf8 COLLATE utf8_unicode_ci";
                } else{
                    // ToDO: Change to DB method
                    $sqlComment = sprintf("%s###%s", $field_name, $comment . ($default == null ? "" : "###$default"));
                    $sql = "CREATE TABLE IF NOT EXISTS `".$ext."` (`id` BIGINT(20) AUTO_INCREMENT PRIMARY KEY ,`".$name."` ".$type." ". ($default == null ? "" : " NOT" ) . " NULL COMMENT '$sqlComment') CHARACTER SET utf8 COLLATE utf8_unicode_ci)";
                }
			    $db->execute($sql);

                // ToDO: Change to DB method
                $sql = "UPDATE corpora SET ext = ? WHERE id = ?";
                $db->execute($sql, array($ext, $corpus['id']));
            } else {
			    if($type == "enum"){
                    // ToDO: Change to DB method
                    $sql = "ALTER TABLE {$ext} ADD {$_POST['field']} {$_POST['type']}({$enum_values}) ". ($default == null ? "" : " DEFAULT '".$default."' NOT" ) . " NULL COMMENT '".$field_name."###".$comment."'";
                } else{
                    // ToDO: Change to DB method
                    $sql = "ALTER TABLE {$ext} ADD {$_POST['field']} {$_POST['type']} ". ($default == null ? "" : " NOT" ) . " NULL COMMENT '".$field_name."###".$comment . ($default == null ? "" : "###" . $default )."'";
                }
                ob_start();
                $db->execute($sql);
                $error_buffer_content = ob_get_contents();
                ob_clean();

                if($type == "text"){
                    //Set default values for all records.
                    $sqlDefault = "UPDATE `".$ext."` SET `".$name."` = ? WHERE 1";
                    $db->execute($sqlDefault, array($default));
                }

                if(strlen($error_buffer_content))
                    throw new Exception("Error: ". $error_buffer_content);
                else
                    return;
            }
		}
		elseif ($action == 'edit'){
            // ToDO: Change to DB method
			$sql = "SELECT ext FROM corpora WHERE id=?";
			$ext = $db->fetch_one($sql, array($corpus['id']));

            if($type == "enum"){
                // ToDO: Change to DB method
                $sql = "ALTER TABLE {$ext} CHANGE {$_POST['old_field']} {$name} {$type}({$enum_values}) ". ($default == null ? "" : " DEFAULT '".$default."' NOT" ) . " NULL COMMENT '".$field_name . "###" . $comment."'";
            } else{
                // ToDO: Change to DB method
                $sql = "ALTER TABLE {$ext} CHANGE {$_POST['old_field']} {$name} {$type} ". ($default == null ? "" : " NOT" ) . " NULL COMMENT '".$field_name . "###" . $comment . ($default == null ? "" : "###" . $default )."'";
            }

            ob_start();
			$db->execute($sql);
			$error_buffer_content = ob_get_contents();
			ob_clean();
			if(strlen($error_buffer_content)) {
                throw new Exception("Error: " . $error_buffer_content);
            }else {
                return;
            }
		}
		else if($action == 'delete'){
            // ToDO: Change to DB method
            $sql = "SELECT ext FROM corpora WHERE id=?";
            $table_name = $db->fetch_one($sql, array($corpus['id']));
            ChromePhp::log($table_name);

            if(count(DbCorpus::getCorpusExtColumns($table_name)) > 1){
                // ToDO: Change to DB method
                $sql = "ALTER TABLE ".$table_name." DROP " .$name;
                $db->execute($sql);
            } else{
                // ToDO: Change to DB method
                $sql = "DROP TABLE ".$table_name;
                $db->execute($sql);

                // ToDO: Change to DB method
                $sql = "UPDATE corpora SET ext = '' WHERE id = ?";
                $db->execute($sql, $corpus['id']);
            }
        }
		elseif ($action == 'add_table'){
			$table_name = "reports_ext_".$corpus['id'];
            // ToDO: Change to DB method
			$sql = "CREATE TABLE {$table_name} (id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY , {$_POST['field']} {$_POST['type']} ". ($_POST['is_null'] == "true" ? "" : " NOT" ) . " NULL ) ENGINE = InnoDB ";
			ob_start();
			$db->execute($sql);
			$error_buffer_content = ob_get_contents();
			ob_clean();
			if(strlen($error_buffer_content))
				throw new Exception("Error: ". $error_buffer_content);
			else{
                // ToDO: Change to DB method
				$sql = "UPDATE corpora SET ext = '{$table_name}' WHERE id = {$corpus['id']}";
				ob_start();
				$db->execute($sql);
				$error_buffer_content = ob_get_contents();
				ob_clean();
				if(strlen($error_buffer_content)) {
                    throw new Exception("Error: " . $error_buffer_content);
                }else {
                    return;
                }
			}			
		}
		else{
			throw new Exception("Wrong action");			
		}		
	}	
}

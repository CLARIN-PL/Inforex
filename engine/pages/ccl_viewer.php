<?php
class Page_ccl_viewer extends CPage{

	var $isSecure = false;
	
	function execute(){		
		
		if(isset($_POST["MAX_FILE_SIZE"])){
			$this->upload_files();
		}
		elseif(isset($_GET['content'])){
			$this->fill_content($_GET['content'], $_GET['elements']);
		}
		$this->set_panels();
		$this->set_relation_sets();
	}
	
	
	function upload_files(){
		if(isset($_FILES['pre_morph']) && $_FILES['pre_morph']['error'] == 0){
			if (file_exists($_FILES['pre_morph']['tmp_name'])) {
    			$content = file_get_contents($_FILES['pre_morph']['tmp_name']);
    		} else {
    			echo "The file {$_FILES['pre_morph']['tmp_name']} does not exist";
			}
		}elseif(isset($_FILES['annotations_file']) && $_FILES['annotations_file']['error'] == 0){
			if (file_exists($_FILES['annotations_file']['tmp_name'])) {
				$ccl = WcclReader::readDomFile($_FILES['annotations_file']['tmp_name']);
    			$content = $this->get_contents_from_ccl($ccl);
    		} else {
    			echo "The file {$_FILES['annotations_file']['tmp_name']} does not exist";
			}
		}else{
			$content = "";
		}
		
		$ccl_elements = array("annotations" => array(), "relations" => array());
    			
    	if(isset($_FILES['annotations_file']) && $_FILES['annotations_file']['error'] == 0){
			if (file_exists($_FILES['annotations_file']['tmp_name'])) {
				$ccl = WcclReader::readDomFile($_FILES['annotations_file']['tmp_name']);
				
				if(isset($_FILES['relations_file']) && $_FILES['relations_file']['error'] == 0){
					if (file_exists($_FILES['relations_file']['tmp_name'])) {
						$ccl_rels = WcclReader::readDomFile($_FILES['relations_file']['tmp_name']);
						$ccl->relations = $ccl_rels->relations;
					}
				}
				
				$ccl_elements = $this->get_ccl($ccl);
			} 		
		}
		$this->redirect("index.php?page=ccl_viewer&content=".urlencode(gzcompress( $content, 9))."&elements=".urlencode(gzcompress( json_encode($ccl_elements), 9)));
	}
	

	function fill_content($content, $elements){
		global $db;
		if (get_magic_quotes_gpc()){
			$decode_content = gzuncompress(stripcslashes($content));
			$decode_elements = json_decode(urldecode(gzuncompress(stripcslashes($elements))), true);
		}else{
			$decode_content = gzuncompress($content);
			$decode_elements = json_decode(urldecode(gzuncompress($elements)), true);
		}
		
		$htmlStr =  new HtmlStr2($decode_content);
		$htmlStr2 = clone $htmlStr;
		
		
		$chunksToInset = array("leftContent" => array(), "rightContent" => array());
		$show_relation = array("leftContent" => array(), "rightContent" => array());
		$this->set_navigation_elements($decode_elements, $htmlStr, &$chunksToInset, &$show_relation);
		
		$sql = "SELECT name, relation_set_id " .
				"FROM relation_types " .
				"WHERE relation_set_id IS NOT NULL";
		$relations_types_array = $db->fetch_rows($sql);
		$relations_types = array();
		$active_annotation_types = ( $_COOKIE['active_annotation_types'] && $_COOKIE['active_annotation_types']!="{}" ? explode(',', preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['active_annotation_types'])) : array());

		foreach($relations_types_array as $relation_type)
			$relations_types[strtolower($relation_type['name'])] = $relation_type['relation_set_id'];
		
		foreach ($decode_elements['relations'] as $r){
			if(!array_key_exists(strtolower($r['name']), $relations_types) || in_array($relations_types[strtolower($r['name'])], $active_annotation_types)){
				if(array_key_exists($r['source_id'],$show_relation["leftContent"]) && array_key_exists($r['target_id'],$show_relation["leftContent"]))
						$show_relation["leftContent"][$r['source_id']][] = "<sup class='rel' title='".$r['name']."' sourcegroupid='".$r['source_id']."' target='".$r['target_id']."'/></sup>";
				if(array_key_exists($r['source_id'],$show_relation["rightContent"]) && array_key_exists($r['target_id'],$show_relation["rightContent"]))
						$show_relation["rightContent"][$r['source_id']][] = "<sup class='rel' title='".$r['name']."' sourcegroupid='".$r['source_id']."' target='".$r['target_id']."'/></sup>";
			}
		}
		
		foreach ($chunksToInset["leftContent"] as $ann){
			try{
				$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s:%d:%d>", $ann['id'], $ann['type'], $ann['group_id'], $ann['subsetid']), $ann['to']+1, "</an>".implode($show_relation["leftContent"][$ann['id']]));
			}catch (Exception $ex){
				fb($ex);			
			}
		}
		
		foreach ($chunksToInset["rightContent"] as $ann){
			try{
				$htmlStr2->insertTag($ann['from'], sprintf("<an#%d:%s:%d:%d>", $ann['id'], $ann['type'], $ann['group_id'], $ann['subsetid']), $ann['to']+1, "</an>".implode($show_relation["rightContent"][$ann['id']]));
			}catch (Exception $ex){
				fb($ex);			
			}
		}
		
		$this->set('content_inline', Reformat::xmlToHtml($htmlStr->getContent()));
		$this->set('content_inline2', Reformat::xmlToHtml($htmlStr2->getContent()));
	}
	

	function set_navigation_elements($elements, $htmlStr, $chunksToInset, $show_relation){
		global $db;

		$sql = "SELECT t.*, s.description as `set`, ss.description AS subset, ss.annotation_subset_id AS subsetid, s.annotation_set_id AS groupid " .
				"FROM annotation_types t " .
				"LEFT JOIN annotation_subsets ss ON (ss.annotation_subset_id = t.annotation_subset_id) " .
				"LEFT JOIN annotation_sets s ON (s.annotation_set_id = t.group_id) " .
				"ORDER BY `set`, subset, t.short_description, t.name";
		$annotations_types = $db->fetch_rows($sql);
		
		$annotationsClear = !$_COOKIE['clearedLayer'];
		$clearedLayer = ( $_COOKIE['clearedLayer'] && $_COOKIE['clearedLayer']!="{}" ? explode(',', preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['clearedLayer'])) : array());
		$clearedSublayer = ( $_COOKIE['clearedSublayer'] && $_COOKIE['clearedSublayer']!="{}" ? explode(',', preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['clearedSublayer'])) : array());
		$rightSublayer = ( $_COOKIE['rightSublayer'] && $_COOKIE['rightSublayer']!="{}" ? explode(',', preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['rightSublayer'])) : array());
		
		$annotation_set_map = array();
		$all_relations = $elements['relations'];
		
		foreach ($elements['annotations'] as $channel_name=>$v){
			$an = $this->find_annotation_types($channel_name, $annotations_types);
			$set = $an['set'];
			$subset = $an['subset'] ? $an['subset'] : "none"; 
			if (!isset($annotation_grouped[$set])){
				$annotation_grouped[$set] = array();
				$annotation_grouped[$set]['groupid']=$an['groupid'];
			}
			if (!isset($annotation_grouped[$set][$subset])){
				$annotation_grouped[$set][$subset] = array();
				$annotation_grouped[$set][$subset]['subsetid']=$an['subsetid'];
				$annotationsSubsets[] = $an['subsetid'];
			}
			$annotation_grouped[$set][$subset][$an[name]] = $an;
			
			$subsetName = $an['subset']==NULL ? "!uncategorized" : $an['subset'];
			$anntype = $an['name'];
			
			if ($annotation_set_map[$set][$subsetName][$anntype]==NULL){
				$annotation_set_map[$set][$subsetName]['subsetid'] = $an['subsetid'];
				$annotation_set_map[$set][$subsetName][$anntype] = array();
				$annotation_set_map[$set][$subsetName][$anntype]['description']=$an['short_description'];
				$annotation_set_map[$set]['groupid']=$an['group_id'];
			}
			
			foreach ($v as $be){
				$full_annotation = array(
					"id" => $be[2],
					"type" => $channel_name,
					"from" => $be[0],
					"to" => $be[1],
					"len" => $be[1] - $be[0],
					"text" => $htmlStr->getText($be[0], $be[1]),
					"group_id" => $an['groupid'],
					"setname" => $set,
					"subsetname" => $subsetName,
					"annotation_subset_id" => $an['annotation_subset_id'],
					"typename" => $channel_name,
					"typedesc" => $an['short_description'],
					"stage" => "final",
					"source" => "file"
				);
				array_push($annotation_set_map[$set][$subsetName][$anntype], $full_annotation);
				$this->update_relations($full_annotation, &$all_relations);
			}
		
			if (!$annotationsClear && !in_array($an['groupid'], $clearedLayer) && !in_array($an['subsetid'], $clearedSublayer)){
				$content_position = (in_array($an['subsetid'], $rightSublayer) ? "rightContent" : "leftContent");
				foreach ($v as $be){
					$chunksToInset[$content_position][] = array(
						"group_id" => $an['groupid'],
						"subsetid" => $an['subsetid'],
						"type" => $channel_name,
						"from" => $be[0],
						"to" => $be[1],
						"id" => $be[2]
					);
					$show_relation[$content_position][$be[2]] = array();
				}
			}
		}
		$this->set('annotation_types', $annotation_grouped);
		$this->set('sets', $annotation_set_map);
		$this->set('allrelations',$all_relations);
	}
	

	function set_panels(){
		$this->set('showRight', $_COOKIE['showRight']=="true"?true:false);
	}
	
	
	function set_relation_sets(){
		global $db;
		$sql = 	"SELECT * FROM relation_sets ";
		$relation_sets = $db->fetch_rows($sql);
		$types = explode(",",preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['active_annotation_types']));
		foreach($relation_sets as $key => $rel_set)
			$relation_sets[$key]['active'] = ($_COOKIE['active_annotation_types'] ? (in_array($rel_set['relation_set_id'],$types) ? 1 : 0) : 1 );
		$this->set('relation_sets', $relation_sets);
	}
	
	
	function get_contents_from_ccl($ccl){
		$content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
				"<!DOCTYPE cesAna SYSTEM \"xcesAnaIPI.dtd\">\n" .
				"<cesAna xmlns:xlink=\"http://www.w3.org/1999/xlink\" type=\"pre_morph\" version=\"WROC-1.0\">\n" .
				"<chunkList xml:base=\"text.xml\">";
		
		foreach ($ccl->chunks as $chunk){
			$content .= "<chunk>";
			foreach ($chunk->sentences as $sentence){
				/* Pomiń puste zdania o ile się zdażą */
				if ( count($sentence->tokens) == 0 )
					continue;
		
				$content .= "<sentence>";
				foreach ($sentence->tokens as $token){
					if (!$token->ns)
						$content .= " ";
					$content .= $token->orth;					
				}
				$content .= "</sentence>";				
			}
			$content .= "</chunk>\n";
		}
		$content .= "</chunkList>\n</cesAna>";
		return $content;
	}
	
	
	function get_ccl($ccl){
		$offset = 0;
		$annotations = array();
		$relations_in_doc = array();
		foreach ($ccl->relations as $key => $relation){
			$relations_in_doc[$key] = array("name" => $relation->type);
		}
		
		$annotation_id = 1;
		foreach ($ccl->chunks as $chunk){
			foreach ($chunk->sentences as $sentence){
				/* Pomiń puste zdania o ile się zdażą */
				if ( count($sentence->tokens) == 0 )
					continue;
				$channels = array_keys($sentence->tokens[0]->channels);
				$end = $offset;
			
				foreach ($channels as $ch){
					$current = $offset;
					$last = 0;
					$begin = 0;
					foreach ($sentence->tokens as $token){
						$ann = $token->channels[$ch];
						
						/* Sprawdź, czy utworzyć nową anotację */
						if ($ann <> $last && $last > 0){
							$this->find_relations($ccl->relations, $sentence->id, $ch, $last, $annotation_id, &$relations_in_doc);
							$annotations[$ch][] = array($begin, $current-1, $annotation_id++);		
							$begin = 0;
							$end = 0;
							$last = 0;
						}
					
						/* Sprawdź, czy utworzyć nowe śledzenie */
						if ($ann <> $last && $ann > 0){
							$begin = $current;
							$end = $current;
							$last = $ann;
						}
						$current += mb_strlen(htmlspecialchars_decode($token->orth));					
					}
					if ($last>0){
						$this->find_relations($ccl->relations, $sentence->id, $ch, $last, $annotation_id, &$relations_in_doc);
						$annotations[$ch][] = array($begin, $current-1, $annotation_id++);
					}
				}
			
				/* Zmodyfikuj offset początku następnego zdania */
				foreach ($sentence->tokens as $token)
					$offset += mb_strlen(htmlspecialchars_decode($token->orth));
			}			
		}
		return array("annotations" => $annotations, "relations" => $relations_in_doc);
	}
	
	function update_relations($annotation, $relations){
		foreach ($relations as $key => $relation){
			if ($relation['source_id'] == $annotation['id']){
				$relations[$key]['id'] = $key;
				$relations[$key]['source_group_id'] = $annotation['group_id']; 
				$relations[$key]['source_annotation_subset_id'] = $annotation['annotation_subset_id']; 
				$relations[$key]['source_text'] = $annotation['text'];
				$relations[$key]['source_type'] = $annotation['type'];
			}
			
			if ($relation['target_id'] == $annotation['id']){
				$relations[$key]['target_group_id'] = $annotation['group_id']; 
				$relations[$key]['target_annotation_subset_id'] = $annotation['annotation_subset_id']; 
				$relations[$key]['target_text'] = $annotation['text'];
				$relations[$key]['target_type'] = $annotation['type'];
			}				
		}
	}
	
	
	function find_relations($relations, $sentence_id, $channel, $annotation, $annotation_id, $relations_in_doc){
		foreach ($relations as $key => $relation){
			if ($relation->source_sentence_id == $sentence_id && $relation->source_channal_name == $channel && $relation->source_id == $annotation)
				$relations_in_doc[$key]['source_id'] = $annotation_id;
			
			if ($relation->target_sentence_id == $sentence_id && $relation->target_channal_name == $channel && $relation->target_id == $annotation)
				$relations_in_doc[$key]['target_id'] = $annotation_id;
		}
		
	}
	
	
	function find_annotation_types($channel_name, $annotations_types){
		foreach ($annotations_types as $at)
			if ($at['name'] == $channel_name)
				return $at;
		return array();
	}	
}
?>
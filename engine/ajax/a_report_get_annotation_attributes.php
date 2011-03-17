<?php
class Ajax_report_get_annotation_attributes extends CPage {
	
	function execute(){
		//sleep(1);
		$annotation_id = intval($_POST['annotation_id']);
		
		if ($annotation_id<=0){
			echo json_encode(array("error"=>"No identifier of annotation found"));
			return;
		}
			
		$sql = "SELECT * FROM reports_annotations an JOIN annotation_types_attributes at ON (an.type=at.annotation_type) WHERE name = 'sense' WHERE id = ?";

		$rows_attributes = db_fetch_rows("SELECT ta.*, v.value" .
				" FROM reports_annotations a" .
				" JOIN annotation_types_attributes ta ON (ta.annotation_type=a.type)" .
				" LEFT JOIN reports_annotations_attributes v ON (v.annotation_id=a.id AND v.annotation_attribute_id=ta.id)" .
				" WHERE a.id = $annotation_id");
		
		$attributes = array();
		foreach ($rows_attributes as $r){
			$attr = $r;
			$rows_values = db_fetch_rows("SELECT * FROM annotation_types_attributes_enum WHERE annotation_type_attribute_id=".intval($r['id']));
			$values = array();
			foreach ($rows_values as $v)
				$values[] = array("value"=>$v['value'], "description"=>$v['description']);
			$attr['values'] = $values;
			$attributes[] = $attr;
		}
		
//		$values = array();
//		$values[] = array("value"=>"zamek-1", "description"=>"Budowla");
//		$attributes[] = array("name"=>"sense", "type"=>"radio", "value"=>"zamek-1", "values"=>$values);
		
		echo json_encode(array("attributes" => $attributes));
		
	}
	
}
?>
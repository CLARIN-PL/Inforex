<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class CDbAnnotationSharedAttribute{

    function getAll(){
        global $db;
        $sql = "SELECT * FROM shared_attributes ORDER BY description";
        return $db->fetch_rows($sql);
    }

    function get($sharedAttributeId){
        global $db;
        $sql = "SELECT * FROM shared_attributes WHERE id = ?";
        return $db->fetch($sql, array($sharedAttributeId));
    }

    function existsAttributeEnumValue($sharedAttributeId, $value){
        global $db;
        $sql = "SELECT * FROM shared_attributes_enum WHERE shared_attribute_id = ? AND `value` = ?";
        return count($db->fetch_rows($sql, array($sharedAttributeId, $value)))>0;
    }

    function addAttributeEnumValue($sharedAttributeId, $value){
        global $db;
        $sql = "INSERT IGNORE INTO shared_attributes_enum (shared_attribute_id, `value`) VALUES(?, ?)";
        $db->execute($sql, array($sharedAttributeId, $value));
    }

    function addAttributeEnumValueWithDescription($sharedAttributeId, $value, $description){
        global $db;
        $sql = "INSERT INTO shared_attributes_enum (shared_attribute_id, `value`, description) VALUES(?, ?, ?)";
        $db->execute($sql, array($sharedAttributeId, $value, $description));
    }

    function deleteAttributeValue($attributeId, $value){
        global $db;
        $sql = "DELETE FROM shared_attributes_enum WHERE shared_attribute_id=? AND value=?";
        $db->execute($sql, array($attributeId, $value));
    }

    function getAnnotationSharedAttributes($annotationId){
        global $db;
        $sql = "SELECT atsa.*, sa.*, rasa.value FROM reports_annotations_shared_attributes rasa 
                JOIN shared_attributes sa ON rasa.shared_attribute_id = sa.id
                JOIN reports_annotations_optimized an ON an.id = rasa.annotation_id
                JOIN annotation_types_shared_attributes atsa ON atsa.shared_attribute_id = sa.id
                WHERE rasa.annotation_id = ? AND an.type_id = atsa.annotation_type_id";
        return $db->fetch_rows($sql, array($annotationId));
    }

    function getAttributeAnnotationValues($corpusId, $attributeId=null, $lang=null, $subcorpusId=null){
        global $db;
        $builder = new SqlBuilder("reports_annotations_shared_attributes", "rasa");
        $builder->addSelectColumn(new SqlBuilderSelect("rasa.value", "value"));
        $builder->addSelectColumn(new SqlBuilderSelect("COUNT(*)", "c"));
        $builder->addJoinTable(new SqlBuilderJoin("reports_annotations_optimized", "rao", "rao.id=rasa.annotation_id"));
        $builder->addJoinTable(new SqlBuilderJoin("reports", "r", "r.id = rao.report_id"));
        $builder->addWhere(new SqlBuilderWhere("r.corpora = ?", array($corpusId)));
        $builder->addOrderBy("`value`");
        $builder->addGroupBy("`value`");

        if ($attributeId){
            $builder->addWhere(new SqlBuilderWhere("rasa.shared_attribute_id = ?", array($attributeId)));
        }

        if ( strval($lang) != "" ){
            $builder->addWhere(new SqlBuilderWhere("r.lang = ?", array($lang)));
        }

        if ( strval($subcorpusId) != "" ){
            $builder->addWhere(new SqlBuilderWhere("r.subcorpus_id = ?", array($subcorpusId)));
        }

        list($sql, $params) = $builder->getSql();
        return $db->fetch_rows($sql, $params);
    }

    function getAnnotationsWithAttributeValue(
            $corpusId, $attributeId=null, $attributeValue=null, $lang=null, $subcorpusId=null){
        global $db;
        $builder = new SqlBuilder("reports_annotations_shared_attributes", "rasa");
        $builder->addSelectColumn(new SqlBuilderSelect("rasa.annotation_id", "id"));
        $builder->addSelectColumn(new SqlBuilderSelect("ral.lemma", "lemma"));
        $builder->addSelectColumn(new SqlBuilderSelect("t.name", "type"));
        $builder->addSelectColumn(new SqlBuilderSelect("r.id", "report_id"));
        $builder->addSelectColumn(new SqlBuilderSelect("rao.text", "text"));
        $builder->addJoinTable(new SqlBuilderJoin("reports_annotations_optimized", "rao", "rao.id=rasa.annotation_id"));
        $builder->addJoinTable(new SqlBuilderJoin("reports_annotations_lemma", "ral", "rao.id = ral.report_annotation_id"));
        $builder->addJoinTable(new SqlBuilderJoin("annotation_types", "t", "t.annotation_type_id = rao.type_id"));
        $builder->addJoinTable(new SqlBuilderJoin("reports", "r", "r.id = rao.report_id"));
        $builder->addWhere(new SqlBuilderWhere("r.corpora = ?", array($corpusId)));
        $builder->addOrderBy("r.id");

        if ( $attributeId ) {
            $builder->addWhere(new SqlBuilderWhere("rasa.shared_attribute_id = ?", array($attributeId)));
        }

        if ( strval($attributeValue) != "" ) {
            $builder->addWhere(new SqlBuilderWhere("rasa.value = ?", array($attributeValue)));
        }

        if ( strval($lang) != "" ){
            $builder->addWhere(new SqlBuilderWhere("r.lang = ?", array($lang)));
        }

        if ( strval($subcorpusId) != "" ){
            $builder->addWhere(new SqlBuilderWhere("r.subcorpus_id = ?", array($subcorpusId)));
        }

        list($sql, $params) = $builder->getSql();

        return $db->fetch_rows($sql, $params);
    }

    function updateAttributeDescription($attributeId, $value, $description){
        global $db;
        $sql = "UPDATE shared_attributes_enum SET description = ? WHERE shared_attribute_id = ? AND value = ?";
        $db->execute($sql, array($description, $attributeId, $value));
    }

    function updateAttributeValue($attributeId, $valueOld, $valueNew){
        global $db;
        $sql = "UPDATE shared_attributes_enum SET value = ? WHERE shared_attribute_id = ? AND value = ?";
        $db->execute($sql, array($valueNew, $attributeId, $valueOld));
    }

    function updateAnnotationAttributeValues($attributeId, $valueOld, $valueNew){
        global $db;
        $sql = "UPDATE reports_annotations_shared_attributes SET value = ? WHERE shared_attribute_id = ? AND value = ?";
        $db->execute($sql, array($valueNew, $attributeId, $valueOld));
    }
}
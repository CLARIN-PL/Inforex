<?php
class TableReportAnnotationSharedAttribute extends ATable
{

    var $_meta_table = "reports_annotations_shared_attributes";
    var $_meta_key = null;

    var $annotation_id = null;
    var $shared_attribute_id = null;
    var $value = null;
    var $user_id = null;

    /**
     * @return null
     */
    public function getAnnotationId()
    {
        return $this->annotation_id;
    }

    /**
     * @param null $annotation_id
     */
    public function setAnnotationId($annotation_id)
    {
        $this->annotation_id = $annotation_id;
    }

    /**
     * @return null
     */
    public function getSharedAttributeId()
    {
        return $this->shared_attribute_id;
    }

    /**
     * @param null $shared_attribute_id
     */
    public function setSharedAttributeId($shared_attribute_id)
    {
        $this->shared_attribute_id = $shared_attribute_id;
    }

    /**
     * @return null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param null $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return null
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param null $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }



}
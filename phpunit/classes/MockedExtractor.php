<?php

class MockedExtractor {

    private $flagName = '';                 // ex. 'f'
    private $flagIds  = array();            // ex. array(3,4)
    private $extractorName = '';            // ex. "annotation_subset_id"    
    private $extractorParams = array();     // ex. array(1)

    private $extractorReturnedData = array();

    public function __construct($flagName,$flagIds,$extractorName,$extractorParams) {
        $this->flagName         = $flagName;
        $this->flagIds          = $flagIds;
        $this->extractorName     = $extractorName;
        $this->extractorParams  = $extractorParams;

    } // __construct()

    public function getExtractorsTable() {

        $flag_name = strtolower($this->flagName);
        $flag_state = implode(',',$this->flagIds);
        $extractors = array(
            0 => array (
                    "flag_name" =>  $flag_name,
                    "flag_ids"  =>  $this->flagIds,
                    "name"      =>  $flag_name."=".$flag_state.":".$this->extractorName."=".implode(",",$this->extractorParams),
                    "params"    =>  $this->extractorParams,
                    "extractor" =>
 function($report_id, $params, &$elements) {
    switch($this->extractorName) {
        case 'annotation_set_id' :
        case 'annotations' :
        case 'annotation_subset_id' :
            $elements['annotations'] = array();
            foreach($this->extractorReturnedData['annotations'] as $annotation) {
                $elements['annotations'][] = $annotation;
            }
            break;
        case 'relation_set_id':
            $elements['relations'] = array();
            foreach($this->extractorReturnedData['relations'] as $relation) {
                $elements['relations'][] = $relation;
            }
            break;
        case 'lemma_annotation_set_id' :
        case 'lemma_annotation_subset_id' :
            $elements['lemmas'] = array();
            foreach($this->extractorReturnedData['lemmas'] as $lemma) {
                $elements['lemmas'][] = $lemma;
            }
            break;
        case 'attributes_annotation_set_id' :
        case 'attributes_annotation_subset_id' :
            $elements['attributes'] = array();
            foreach($this->extractorReturnedData['attributes'] as $attr) {
                $elements['attributes'][] = $attr;
            }
            break;
        default :
            var_dump('No proper extractorName in method getExtractorsTable defined !!!');
    } // switch
 } // function()
                 ) // extractors[0] row
        ); // $extractors

        return $extractors;

    } // getExtractorTables()
  
    public function setExtractorReturnedData($extractorDataType,$extractorDBDatas) {

        $this->extractorReturnedData[$extractorDataType] = $extractorDBDatas;

    } // setExtractorReturnedData()

    public function getStatisticsName() {

        // $parts = explode(":",$description)
        // $elements = $parts[1]
        // foreach($elements as $element )
        //      $extractor_name = $flag_name."=".implode(",",$flag_ids)
        //                          .":".$element
        //      $extractor["name"] = $extractor_name
        $element = $this->extractorName.
                   "=".
                   implode(",",$this->extractorParams);
        $name = $this->flagName."=".
                implode(",",$this->flagIds).
                ":"
                .$element;
        return $name;

    } // createStatisticsName()
    
}

?>

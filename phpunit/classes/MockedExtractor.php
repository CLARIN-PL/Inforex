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
    foreach( ['annotations','lemmas','attributes','relations'] as $n ) {
        $elements[$n] = array();
        if($data = $this->extractorReturnedData[$n]) {
            foreach($this->extractorReturnedData[$n] as $data) {
                $elements[$n][] = $data;
            }
        } // $data<>nil
    }
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

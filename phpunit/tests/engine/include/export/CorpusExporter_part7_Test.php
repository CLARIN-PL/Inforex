<?php

mb_internal_encoding("UTF-8");
require_once("CorpusExporterTest.php");

class CorpusExporter_part7_Test extends CorpusExporterTest
//PHPUnit_Framework_TestCase
{
    private $output_file_basename = '';
    private $extractorName = '';
    private $extractorReturnedData = array();

    private function createStatisticsName($flagName,$flagIds,$extractorName,$extractorParams) {

        // $parts = explode(":",$description)
        // $elements = $parts[1]
        // foreach($elements as $element )
        //      $extractor_name = $flag_name."=".implode(",",$flag_ids)
        //                          .":".$element
        //      $extractor["name"] = $extractor_name
        $element = $extractorName.
                   "=".
                   implode(",",$extractorParams);
        $name = $flagName."=".
                implode(",",$flagIds).
                ":"
                .$element;
        return $name;

    }

// subannotation export testing

    private function addExtractorReturnedData($extractorDataType,$id,$report_id,$type_id,$type,$from,$to,$text,$user_id,$creation_time,$stage,$source,$prop) {

        $this->extractorReturnedData[$extractorDataType][] = array(
            "id"=>$id,
            "report_id"=>$report_id,
            "type_id"=>$type_id,
            "type"=>$type,
            "from"=>$from,
            "to"=>$to,
            "text"=>$text,
            "user_id"=>$user_id,
            "creation_time"=>$creation_time,
            "stage"=>$stage,
            "source"=>$source,
            "prop"=>$prop
        );

    } // addExtractorReturnedData()

    private function setExtractorReturnedData($extractorDataType) {

        $this->extractorReturnedData[$extractorDataType] = array();

    }

    private function getExtractorsTable($flag_name='f',$flag_ids=array(3),$extractor_name = "attributes_annotation_subset_id",$annotation_sublayer = array(1)) {

        $flag_name = strtolower($flag_name);
        $flag_state = implode(',',$flag_ids);
        $this->extractorName = $extractor_name;
        $extractors = array(
            0 => array (
                    "flag_name" =>  $flag_name,
                    "flag_ids"  =>  $flag_ids,
                    "name"      =>  $flag_name."=".$flag_state.":".$extractor_name."=".implode(",",$annotation_sublayer),
                    "params"    =>  $annotation_sublayer,
                    "extractor" => 
 function($report_id, $params, &$elements) {
// use DbAnnotation::getAnnotationsBySubsets(array($report_id), $params);
//  returns $elements['annotations'] update
    switch($this->extractorName) {
        case 'annotation_subset_id' :       
            $elements['annotations'] = array();
            foreach($this->extractorReturnedData['annotations'] as $annotation) {
                $elements['annotations'][] = $annotation;
            }
            break;
        case 'annotation_set':
        default : 
            var_dump('No proper extractorName in method getExtractorsTable defined !!!');
    } // switch
 } // function()
                 ) // extractors[0] row
        ); // $extractors

        return $extractors;

    } // getExtractorTables()

    private function setWorkDir($method,$report_id) {

        $this->makeWorkDir($method,$report_id);
        $this->output_file_basename = $this->createBaseFilename($method,$report_id);
        return $this->createWorkDirName($method);

    } // setWorkDir()

    private function addReportCorporaExtDB(DatabaseEmulator $dbEmu, $report_id ){

        //  DbReport::getReportExtById($report_id)
        //
        // exCorpus - corpora.ext empty here
        // corpora[reports[$report_id]["corpora"]]["ext]
        // zawsze sprawdza rekord korpusu wskazywany przez 
        // reports[$report_id]["corpora"] wywołaniem:
        //  DbCorpora::getCorporaById() która 1 wiersz lub null
        // aby ustalić wartość ext dla tego korpusu

        $emptyDataRows = array();
        $dbEmu->setResponse("fetch_rows",
            'SELECT * FROM corpora WHERE id = ?',
            $emptyDataRows );

    } // addReportCorporaExtDB()

    private function addReportFlagDB(DatabaseEmulator $dbEmu, $report_id, $flagDBData ) {
        $short = $flagDBData["FlagName"];
        $flag_ids = $flagDBData["FlagValues"];
        //  zbiór flag dostępnych dla dokumentu $report_id
        //    nazwa i flaga z ekstraktora musi się zgadzać z istniejącymi
        //    w bazie dla tego dokumentu
        $reportFlags = array();
        foreach($flag_ids as $flag_id){
            $reportFlags[$id] = 
                array( "short"=>$short, "flag_id"=>$flag_id );
        }
        $allReturnedDataRows = $reportFlags;
        $dbEmu->setResponse("fetch_rows",
'SELECT cf.short, rf.flag_id FROM reports_flags rf  JOIN corpora_flags cf USING (corpora_flag_id) WHERE rf.report_id = ?',
            $allReturnedDataRows );

    } // addReportFlagDB()

    private function addTagsDB(DatabaseEmulator $dbEmu, $report_id, $token_ids) {

        // te dane nie mają wpływu na wynik exportu
        $id = 1;                           // numeracja id od 1
        $token_id = 1; // nie musi być zgodny z żadnym $token_ids
        $disamb = 0;
        $tto_ctag_id = 1;
        $ctag_id = 1;
        $ctag = 'tag';
        $tagset_id = 1; 
        $base_id = 1; 
        $base_text = 'text'; 

        $tags = array();
        foreach($token_ids as $token) {
            $tags[$id] = array( 'token_tag_id' => $id, 'token_id' => $token_id, 'disamb' => $disamb, 'tto.ctag_id' => $tto_ctag_id, 'ctag_id' => $ctag_id, 'ctag'=>$ctag, 'tagset_id' => $tagset_id, 'base_id' => $base_id, 'base_text' => $base_text );
            $id++;
        }
        $allReturnedDataRows = $tags;
        $dbEmu->setResponse("fetch_rows",
'SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, ttc.tagset_id, b.id as base_id, b.text as base_text FROM `tokens_tags_optimized` as tto JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id JOIN bases as b on tto.base_id = b.id WHERE tto.user_id IS NULL  AND token_id IN ('.implode(',',$token_ids).');',
            $allReturnedDataRows );

    } // addTagsDB()

    private function addTokenDB(DatabaseEmulator $dbEmu, $report_id, $tokensDBData) {

        // te dane nie mają wpływu na wynik exportu
        $eos = 1;
        $orth_id = 1;

        $tokens = array();
        $token_id = 1;
        $token_ids = array();
        foreach($tokensDBData as $tokenDBData) {
            $tokens[$token_id] = array( "token_id" => $token_id, "report_id" => $report_id, "from" => $from = $tokenDBData["from"], "to" => $tokenDBData["to"], "eos" => $eos, "orth_id" => $orth_id );
            $token_ids[] = $token_id;
            $token_id++;
        }
        $allReturnedDataRows = $tokens;
        $dbEmu->setResponse("fetch_rows",
' SELECT  *  FROM tokens  LEFT JOIN orths USING (orth_id) WHERE report_id = ? ORDER BY `from`',
            $allReturnedDataRows );

        $this->addTagsDB( $dbEmu, $report_id, $token_ids );

    } // setTokenDB
 
    private function addReportsDB(DatabaseEmulator $dbEmu, $report_id, $flagDBData, $documentDBData, $tokensDBData ) {

        // kolumna ext z wiersza w corpora dla id = corpora dokumentu
        $corpora_ext = $documentDBData["corpora_ext"]; 
        // pola spoza $documentDBData nie mają znaczenia dla wyników
        // generowanych podczas testu
        $report = array(
            "id" => $report_id,
            "corpora" => 1,
            "date" => $documentDBData["date"],
            "title" => $documentDBData["title"],
            "source" => $documentDBData["source"],
            "author" => $documentDBData["author"],
            "content" => $documentDBData["content"],
            "type" => 1,
            "status" => 1,
            "user_id" => 1,
            "subcorpus_id" => 1,
            "tokenization" => $documentDBData["tokenization"],
            "format_id" => 1,
            "lang" => 'pol',
            "filename" => 'nazwa pliku',
            "parent_report_id" => null,
            "deleted" => 0,
        );
        $allReturnedDataRows = array( $report );
        $dbEmu->setResponse("fetch_rows",
            'SELECT * FROM reports WHERE id = ?',
            $allReturnedDataRows );

        $this->addReportFlagDB( $dbEmu, $report_id, $flagDBData );
        $this->addTokenDB( $dbEmu, $report_id, $tokensDBData );
        $this->addReportCorporaExtDB( $dbEmu, $report_id, $corpora_ext );

    } // addReportsDB()
  
// function export_document($report_id, &$extractors, $disamb_only, &$extractor_stats, &$lists, $output_folder, $subcorpora, $tagging_method){
    public function test_export_document()
    {
        // dokument do eksportu - z parametru
        $report_id = 1;
        //   F=3:annotation_subset_id=1
        // flaga o skrócie 'F' w stanie 3; podtyp annotacji = 1
        // przeparsowanie ręczne do parametrów ekstraktora:
        $extractorParameters = array(
            "FlagName" => 'f',   // parse_extractor tu robi zawsze małą literę
            "FlagValues" => array(3),
            "Name" => 'annotation_subset_id',
            "Parameters" => array(1)
        );
        $disamb_only = true;
        $extractor_stats = array();
        $lists = array();
        $output_folder = $this->setWorkDir(__FUNCTION__,$report_id);
        $subcorpora = array();
        //String tagging method from ['tagger', 'final', 'final_or_tagger', 'user:{id}']
        $tagging_method = 'tagger';

        // wykreowanie elementów ekstraktora
        $extractors = $this->getExtractorsTable($extractorParameters["FlagName"],$extractorParameters["FlagValues"],$extractorParameters["Name"],$extractorParameters["Parameters"]);
        $this->setExtractorReturnedData('annotations'); 
            $extractedAnnotation1_id = 1;
            $extractedAnnotation1_report_id = $report_id;
            $extractedAnnotation1_type_id = 1;
            $extractedAnnotation1_type = 'typ annotacji 1';
            $extractedAnnotation1_from = 0;
            $extractedAnnotation1_to = 4;
            $extractedAnnotation1_text = 'tekst';
            $extractedAnnotation1_user_id = 1;
            $extractedAnnotation1_creation_time = '2022-11-11 11:11:11';
            $extractedAnnotation1_stage = 'final';
            $extractedAnnotation1_source = 'user';
            $extractedAnnotation1_prop = 'atrybut annotacji 1';
        $this->addExtractorReturnedData('annotations',$extractedAnnotation1_id,$extractedAnnotation1_report_id,$extractedAnnotation1_type_id,$extractedAnnotation1_type,$extractedAnnotation1_from,$extractedAnnotation1_to,$extractedAnnotation1_text,$extractedAnnotation1_user_id,$extractedAnnotation1_creation_time,$extractedAnnotation1_stage,$extractedAnnotation1_source,$extractedAnnotation1_prop);
 
        // dane jakie powinny zawierać tabele bazy danych dla przeprowadzenia
        // testu
        $dbEmu = new DatabaseEmulator();
        // flagi korpusu, odpowiadającego korpusowi dokumentu
        $documentCorpusFlagDBData = array( 
            "FlagName"      => $extractorParameters["FlagName"],
            "FlagValues"    => $extractorParameters["FlagValues"]
        );
        $documentDBData = array(
            "date"          => '2022-12-16',
            "title"         => 'tytuł',
            "source"        => 'source',
            "author"        => 'author',
            "tokenization"  => 'tokenization',
            "content"       => 'tekst dokumentu',
            // kolumna ext z wiersza w corpora dla id = corpora dokumentu
            "corpora_ext"   => null
        );
        $tokensDBData = array(
            array( "from"=>0, "to"=>4 )
        );
        $this->addReportsDB($dbEmu,$report_id,$documentCorpusFlagDBData,$documentDBData,$tokensDBData); 

        // do test...
        global $db;
        $db = $dbEmu;
        $ce = new CorpusExporter();
        // $extractors is var parameter, but shouldn't change
        $expectedExtractors = $extractors;
        $ce->export_document($report_id,$extractors,$disamb_only,$extractor_stats,$lists,$output_folder,$subcorpora,$tagging_method);
        // check results in variables and files
        $this->assertEquals($expectedExtractors,$extractors);
        $expectedLists = array();
        $this->assertEquals($expectedLists,$lists);
        $expectedStats = array(
            $this->createStatisticsName($extractorParameters["FlagName"],
                                        $extractorParameters["FlagValues"],
                                        $extractorParameters["Name"],
                                        $extractorParameters["Parameters"]
                                       ) => array(
                "annotations"=>1,
                "relations"=>0,
                "lemmas"=>0,
                "attributes"=>0
            )
        );
        $this->assertEquals($expectedStats,$extractor_stats);
        $this->checkFiles($report_id,
            $documentDBData["content"],
            __FUNCTION__,
            $extractedAnnotation1_id,$extractedAnnotation1_report_id,$extractedAnnotation1_type_id,$extractedAnnotation1_type,$extractedAnnotation1_from,$extractedAnnotation1_to,$extractedAnnotation1_text,$extractedAnnotation1_user_id,$extractedAnnotation1_creation_time,$extractedAnnotation1_stage,$extractedAnnotation1_source,$extractedAnnotation1_prop,
            $documentDBData
        ); 

    }

    private function checkFiles($report_id,$content,$methodName,$idAnnotacji1,$report_idAnnotacji1,$type_idAnnotacji1,$typAnnotacji1,$fromAnnotacji1,$toAnnotacji1,$textAnnotacji1,$user_idAnnotacji1,$creation_timeAnnotacji1,$stageAnnotacji1,$sourceAnnotacji1,$atrybutAnnotacji1,$reportData) {

        $this->checkConllFile();
        $this->checkIniFile($report_id,$reportData);
        $this->checkJsonFile($idAnnotacji1,$report_idAnnotacji1,$type_idAnnotacji1,$typAnnotacji1,$fromAnnotacji1,$toAnnotacji1,$textAnnotacji1,$user_idAnnotacji1,$creation_timeAnnotacji1,$stageAnnotacji1,$sourceAnnotacji1,$atrybutAnnotacji1);
        $this->checkTxtFile($content);
        $this->checkRelXmlFile();
        $this->checkXmlFile();

        // remove all files and directories created
        $this->removeWorkDir($methodName,$report_id);

    } // checkFiles()

    private function checkConllFile() {

        $expectedConllContent = "ORDER_ID\tTOKEN_ID\tORTH\tCTAG\tFROM\tTO\tANN_TAGS\tANN_IDS\tREL_IDS\tREL_TARGET_ANN_IDS\n0\t0\t\t\t\t\tB-\t1\t_\t_\n\n";
        $resultConllFile = file_get_contents($this->output_file_basename.'.conll');
        $this->assertEquals($expectedConllContent,$resultConllFile);

    } // checkConllFile()

    private function checkIniFile($report_id,$reportData) {

        $id = $report_id;
        $date = $reportData["date"];
        $title = $reportData["title"];
        $source = $reportData["source"];
        $author = $reportData["author"];
        $tokenization = $reportData["tokenization"];        
        
        $expectedIniContent = "[document]\nid = $id\ndate = $date\ntitle = $title\nsource = $source\nauthor = $author\ntokenization = $tokenization\nsubcorpus = ";
        $resultIniFile = file_get_contents($this->output_file_basename.'.ini');
        $this->assertEquals($expectedIniContent,$resultIniFile);

    } // checkIniFile()

    private function createJsonContent($id,$report_id,$type_id,$type,$from,$to,$text,$user_id,$creation_time,$stage,$source,$prop) {

        $JsonContent =
"{\n    \"chunks\": [\n        [\n            [\n                {\n                    \"order_id\": 0,\n                    \"token_id\": 0,\n                    \"orth\": \"\",\n                    \"ctag\": null,\n                    \"from\": null,\n                    \"to\": null,\n                    \"annotations\": [\n                        1\n                    ],\n                    \"relations\": []\n                }\n            ]\n        ]\n    ],\n    \"relations\": [],\n    \"annotations\": [\n        {\n            \"id\": $id,\n            \"report_id\": $report_id,\n            \"type_id\": $type_id,\n            \"type\": \"$type\",\n            \"from\": $from,\n            \"to\": $to,\n            \"text\": \"$text\",\n            \"user_id\": $user_id,\n            \"creation_time\": \"$creation_time\",\n            \"stage\": \"$stage\",\n            \"source\": \"$source\",\n            \"prop\": \"$prop\"\n        }\n    ]\n}";
 
/* to co do semantyki jest ok, ale nie chce się sformatować do postaci
    z pliku .json

        //var_export(json_decode($JsonContent));

        $JsonTable = array (
            'chunks' => array (
                0 => array (
                    0 => array (
                        0 => array(
                            'order_id' => 0,
                            'token_id' => 0,
                            'orth' => '',
                            'ctag' => NULL,
                            'from' => NULL,
                            'to' => NULL,
                            'annotations' =>
                                array (
                                    0 => 1,
                                ),
                            'relations' =>
                                array (),
                        )
                    ),
                ),
            ), // chunks
            'relations' =>  array (),
            'annotations' => array (
                0 =>array(
                    'id' => 1,
                    'report_id' => 1,
                    'type_id' => 1,
                    'type' => 'typ annotacji 1',
                    'from' => 0,
                    'to' => 4,
                    'text' => 'tekst',
                    'user_id' => 1,
                    'creation_time' => '2022-11-11 11:11:11',
                    'stage' => 'final',
                    'source' => 'user',
                    'prop' => 'atrybut annotacji 1',
                )
            ), // annotations
        );

        return json_encode($JsonTable);
*/
        return $JsonContent;

    } // createJsonContent()

    private function checkJsonFile($idAnnotacji1,$report_idAnnotacji1,$type_idAnnotacji1,$typAnnotacji1,$fromAnnotacji1,$toAnnotacji1,$textAnnotacji1,$user_idAnnotacji1,$creation_timeAnnotacji1,$stageAnnotacji1,$sourceAnnotacji1,$atrybutAnnotacji1) {

        $expectedJsonContent = $this->createJsonContent($idAnnotacji1,$report_idAnnotacji1,$type_idAnnotacji1,$typAnnotacji1,$fromAnnotacji1,$toAnnotacji1,$textAnnotacji1,$user_idAnnotacji1,$creation_timeAnnotacji1,$stageAnnotacji1,$sourceAnnotacji1,$atrybutAnnotacji1);
        $resultJsonFile = file_get_contents($this->output_file_basename.'.json');
        $this->assertEquals($expectedJsonContent,$resultJsonFile);

    } // checkJsonFile()

    private function checkTxtFile($content) {

        $expectedTxtContent = $content;
        $resultTxtFile = file_get_contents($this->output_file_basename.'.txt');
        $this->assertEquals($expectedTxtContent,$resultTxtFile);

    } // checkTxtFile()

    private function checkRelXmlFile() {

        $expectedRelxmlContent = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE chunkList SYSTEM \"ccl.dtd\">\n<relations>\n</relations>\n";
        $resultRelxmlFile = file_get_contents($this->output_file_basename.'.rel.xml');
        $this->assertEquals($expectedRelxmlContent,$resultRelxmlFile);

    } // checkRelXmlFile()

    private function checkXmlFile() {

        $expectedXmlContent = 
"<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE chunkList SYSTEM \"ccl.dtd\">\n<chunkList>\n <chunk id=\"ch1\" type=\"\">\n  <sentence id=\"sent1\">\n   <tok>\n    <orth></orth>\n   </tok>\n   <ns/>\n  </sentence>\n </chunk>\n</chunkList>\n";
        $resultXmlFile = file_get_contents($this->output_file_basename.'.xml');
        $this->assertEquals($expectedXmlContent,$resultXmlFile);

    } // checkXmlFile()

} // class

?>

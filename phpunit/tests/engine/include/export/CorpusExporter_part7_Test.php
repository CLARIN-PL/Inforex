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

    private function setExtractorReturnedData($extractorDataType,$extractorDBDatas) {

        $this->extractorReturnedData[$extractorDataType] = $extractorDBDatas;

    } // setExtractorReturnedData()

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
            $reportFlags[] =
                array( "short"=>$short, "flag_id"=>$flag_id );
        }
        $allReturnedDataRows = $reportFlags;
        $dbEmu->setResponse("fetch_rows",
'SELECT cf.short, rf.flag_id FROM reports_flags rf  JOIN corpora_flags cf USING (corpora_flag_id) WHERE rf.report_id = ?',
            $allReturnedDataRows );

    } // addReportFlagDB()

    private function addTagsDB(DatabaseEmulator $dbEmu, $report_id, $token_ids, $tagsData ) {

        // te dane nie mają wpływu na wynik exportu
        $id = 1;                           // numeracja id od 1
        $token_id = 1; // powinien być zgodny z którymś z $token_ids
                       // ale nie występuje w wynikach eksportu
        $tto_ctag_id = 1;  // te 2 są z warunku JOIN zawsze równe
        $ctag_id = $tto_ctag_id;
        $tagset_id = 1; 
        $base_id = 1; 

        $tags = array();
        foreach($tagsData as $tagRow) {
            $tags[] = array( 'token_tag_id' => $id, 'token_id' => $token_id, 'disamb' => $tagRow["disamb"], 'tto.ctag_id' => $tto_ctag_id, 'ctag_id' => $ctag_id, 'ctag'=>$tagRow["ctag"], 'tagset_id' => $tagset_id, 'base_id' => $base_id, 'base_text' => $tagRow["base_text"] );
            $id++;
        }

        $allReturnedDataRows = $tags;
        $dbEmu->setResponse("fetch_rows",
'SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, ttc.tagset_id, b.id as base_id, b.text as base_text FROM `tokens_tags_optimized` as tto JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id JOIN bases as b on tto.base_id = b.id WHERE tto.user_id IS NULL  AND token_id IN ('.implode(',',$token_ids).');',
            $allReturnedDataRows );

    } // addTagsDB()

    private function addTokenDB(DatabaseEmulator $dbEmu, $report_id, $tokensDBData) {

        // te dane nie mają wpływu na wynik exportu
        $eos = 1;   //  to może mieć znaczenie przy większej ilości zdań
        $orth_id = 1;   // tekst orth jest wycinany z report["content"] 
                        // a nie z tabeli orths na podstawie tego indeksu

        $tokens = array();
        $token_id = 1;
        $token_ids = array();
        $tagsData = array();
        foreach($tokensDBData as $tokenDBData) {
            $tokens[] = array( "token_id" => $token_id, "report_id" => $report_id, "from" =>  $tokenDBData["from"], "to" => $tokenDBData["to"], "eos" => $eos, "orth_id" => $orth_id );
            $token_ids[] = $token_id;
            $tagsData = array_merge($tagsData,$tokenDBData["tags"]);
            $token_id++;
        }
        $allReturnedDataRows = $tokens;
        $dbEmu->setResponse("fetch_rows",
' SELECT  *  FROM tokens  LEFT JOIN orths USING (orth_id) WHERE report_id = ? ORDER BY `from`',
            $allReturnedDataRows );

        // to jest robione jednym wspólnym zapytaniem dla wszystkich
        // token_ids na raz

        $this->addTagsDB( $dbEmu, $report_id, $token_ids, $tagsData );

    } // setTokenDB
 
    private function addReportsDB(DatabaseEmulator $dbEmu, $report_id, $documentDBData ) {

        // kolumna ext z wiersza w corpora dla id = corpora dokumentu
        $corpora_ext = $documentDBData["corpora_ext"]; 
        // pola poza $report_id i $documentDBData nie mają znaczenia 
        // dla wyników generowanych podczas testu
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

        $this->addReportFlagDB( $dbEmu, $report_id, $documentDBData['flags'] );
        $this->addTokenDB( $dbEmu, $report_id, $documentDBData['tokens'] );
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
        $annotationsDBData = array(
            array( "id"=>1, "report_id"=>$report_id, "type_id"=>1, "type"=>'typ annotacji 1', "from"=>0, "to"=>4, "text"=>'tekst', "user_id"=>1, "creation_time"=>'2022-11-11 11:11:11', "stage"=>'final', "source"=>'user', "prop"=>'atrybut annotacji 1' ),
            array( "id"=>100, "report_id"=>$report_id, "type_id"=>2, "type"=>'typ annotacji 2', "from"=>6, "to"=>14, "text"=>'dokumentu', "user_id"=>2, "creation_time"=>'2022-11-11 11:11:12', "stage"=>'agreement', "source"=>'user', "prop"=>'atrybut annotacji 2' )
        );
        $this->setExtractorReturnedData('annotations',$annotationsDBData); 

        // dane jakie powinny zawierać tabele bazy danych dla przeprowadzenia
        // testu
        $dbEmu = new DatabaseEmulator();
        $documentDBData = array(
            "date"          => '2022-12-16',
            "title"         => 'tytuł',
            "source"        => 'source',
            "author"        => 'author',
            "tokenization"  => 'tokenization',
            "content"       => 'tekst dokumentu',
            // kolumna ext z wiersza w corpora dla id = corpora dokumentu
            "corpora_ext"   => null,
            // flagi korpusu, odpowiadającego korpusowi dokumentu 
            "flags"         => array (
                "FlagName"      => $extractorParameters["FlagName"],
                "FlagValues"    => $extractorParameters["FlagValues"]
            ),
            // tablica tokenów dokumentu
            "tokens"        => array(
                array(  "from"=>0, "to"=>4,
                        "tags"=>array(
                            // tags rows for token
                            array( "disamb" => 0, "ctag" => 'ctag', "base_text" => 'text'),
                            array( "disamb" => 0, "ctag" => 'ctag2', "base_text" => 'base_text_taga_2'),
                        ) // tags
                )
 
            ) // tokens
        );
        $this->addReportsDB($dbEmu,$report_id,$documentDBData); 

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
                "annotations"=>count($annotationsDBData),
                "relations"=>0,
                "lemmas"=>0,
                "attributes"=>0
            )
        );
        $this->assertEquals($expectedStats,$extractor_stats);

        $this->checkFiles($report_id,__FUNCTION__,$disamb_only,$documentDBData,$annotationsDBData); 

    }

    private function checkFiles($report_id,$methodName,$disambOnly,$reportData,$extractorData) {

        // group tags from all tokens together
        $tagsData = array();
        foreach($reportData["tokens"] as $token) {
            $tagsData = array_merge($tagsData,$token["tags"]);
        }

        $this->checkConllFile($disambOnly,$reportData,$tagsData);
        $this->checkIniFile($report_id,$reportData);
        $this->checkJsonFile($disambOnly,$extractorData,$tagsData);
        $this->checkTxtFile($reportData["content"]);
        $this->checkRelXmlFile();
        $this->checkXmlFile($disambOnly,$extractorData,$tagsData);

        // remove all files and directories created
        $this->removeWorkDir($methodName,$report_id);

    } // checkFiles()

    private function selectExpectedTags($disambOnly,$tagsData) {
        // z tablicy tagów $tagsData wybiera:
        // wszystkie, jeśli $disambOnly jest False,
        // te które mają "disamb"=1 w przeciwnym wypadku.
        if(!$disambOnly) {
            return $tagsData;
        }
        $selectedTagsData = array();
        foreach($tagsData as $tagRow) {
            if($tagRow["disamb"]){
                $selectedTagsData[] = $tagRow;
            }
        }
        return $selectedTagsData;
 
    } // selectExpectedTags()

    private function checkConllFile($disambOnly,$reportData,$tagsData) {
        // ctag jest wybierany tylko z pierwszego wiersza z tagami dla danego 
        // tokenu, przy czym jeśli $disamb_only jest true liczą się tylko
        // wiersze z "disamb"=1. Jeśli żadnego takiego wiersza nie ma
        // jest pustym napisem
        $selectedTagsData = $this->selectExpectedTags($disambOnly,$tagsData);
        $ctag = count($selectedTagsData)>0 ? $selectedTagsData[0]["ctag"] : "";

        $from = $reportData["tokens"][0]["from"];
        $to = $reportData["tokens"][0]["to"];
        $orth = substr($reportData["content"],$from,$to-$from+1);


        $expectedConllContent = "ORDER_ID\tTOKEN_ID\tORTH\tCTAG\tFROM\tTO\tANN_TAGS\tANN_IDS\tREL_IDS\tREL_TARGET_ANN_IDS\n0\t0\t$orth\t$ctag\t$from\t$to\tB-\t1\t_\t_\n\n";
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

    private function createJsonContent($extractorData,$ctag) {

        $from = $extractorData[0]["from"];
        $to = $extractorData[0]["to"];
        $text = $extractorData[0]["text"];

        $annStrRows = array();
        foreach($extractorData as $annotationData) {
            $annStrRows[] = 
                        "\n        {\n            \"id\": ".
                        $annotationData["id"].
                        ",\n            \"report_id\": ".
                        $annotationData["report_id"].
                        ",\n            \"type_id\": ".
                        $annotationData["type_id"].
                        ",\n            \"type\": \"".
                        $annotationData["type"].
                        "\",\n            \"from\": ".
                        $annotationData["from"].
                        ",\n            \"to\": ".
                        $annotationData["to"].
                        ",\n            \"text\": \"".
                        $annotationData["text"].
                        "\",\n            \"user_id\": ".
                        $annotationData["user_id"].
                        ",\n            \"creation_time\": \"".
                        $annotationData["creation_time"].
                        "\",\n            \"stage\": \"".
                        $annotationData["stage"].
                        "\",\n            \"source\": \"".
                        $annotationData["source"].
                        "\",\n            \"prop\": \"".
                        $annotationData["prop"].
                        "\"\n        }";         
        }
        $annStr = implode(',',$annStrRows);

        $JsonContent =
"{\n    \"chunks\": [\n        [\n            [\n                {\n                    \"order_id\": 0,\n                    \"token_id\": 0,\n                    \"orth\": \"$text\",\n                    \"ctag\": $ctag,\n                    \"from\": $from,\n                    \"to\": $to,\n                    \"annotations\": [\n                        1\n                    ],\n                    \"relations\": []\n                }\n            ]\n        ]\n    ],\n    \"relations\": [],\n    \"annotations\": [$annStr\n    ]\n}";

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

    private function checkJsonFile($disambOnly,$extractorData,$tagsData) {

        // ctag jest wybierany tylko z pierwszego wiersza z tagami dla danego
        // tokenu, przy czym jeśli $disamb_only jest true liczą się tylko
        // wiersze z "disamb"=1. Jeśli żadnego takiego wiersza nie ma
        // jest napisem 'null', a jeśli jest wstawiany jest w cudzysłów.
        $selectedTagsData = $this->selectExpectedTags($disambOnly,$tagsData);
        $ctag = count($selectedTagsData)>0 ? '"'.$selectedTagsData[0]["ctag"].'"' : "null";

        $expectedJsonContent = $this->createJsonContent($extractorData,$ctag);
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

    private function checkXmlFile($disambOnly,$extractorData,$tagsData) {

        $orth = $extractorData[0]["text"];
        $annTypeName = $extractorData[0]["type"];
        $annTypeId = $extractorData[0]["type_id"];

        // dla każdego taga generowany jest wiersz
        //'     <lex disamb=\"$tagDisamb\"><base>$tagBaseText</base><ctag>$tagCtag</ctag></lex>\n'
        $tagsRows = '';  
        foreach($tagsData as $tagData) {
            if( (!$disambOnly) || ($tagData["disamb"]) ) {
            $tagsRows .=    '    <lex'.
                            ($tagData["disamb"]?' disamb="'.$tagData["disamb"].'"':"").
                            '><base>'.
                            $tagData["base_text"].
                            '</base><ctag>'.
                            $tagData["ctag"].
                            '</ctag></lex>'.
                            "\n";
            } // if disamb
        }


        $expectedXmlContent = 
"<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE chunkList SYSTEM \"ccl.dtd\">\n<chunkList>\n <chunk id=\"ch1\" type=\"\">\n  <sentence id=\"sent1\">\n   <tok>\n    <orth>$orth</orth>\n$tagsRows    <ann chan=\"$annTypeName\">$annTypeId</ann>\n   </tok>\n  </sentence>\n </chunk>\n</chunkList>\n";
        $resultXmlFile = file_get_contents($this->output_file_basename.'.xml');
        $this->assertEquals($expectedXmlContent,$resultXmlFile);

    } // checkXmlFile()

} // class

?>

<?php

mb_internal_encoding("UTF-8");

class CorpusExporter_part7_Test extends PHPUnit_Framework_TestCase
{
    private $dir = null; // temp dir for export class 

    public function test_export_document_withDisambNotReturnsTags() {

        foreach(['tagger', 'final', 'final_or_tagger', 'user:1'] as $tagging_method) {
            $this->export_document(True,$tagging_method);
        }

    } // test_export_document_withDisambNotReturnsTags()

    public function test_export_document_withoutDisambReturnsTags() {

        foreach(['tagger', 'final', 'final_or_tagger', 'user:1'] as $tagging_method) {
            $this->export_document(False,$tagging_method);
        }

    } // test_export_document_withoutDisambReturnsTags()

// function export_document($report_id, &$extractors, $disamb_only, &$extractor_stats, &$lists, $output_folder, $subcorpora, $tagging_method){
    private function export_document($disamb_only,$tagging_method)
    {
        // dokument do eksportu - z parametru
        $report_id = 1;
        // katalog do generowania plików z eksportem
        $this->dir = new ExportTempDirManager($report_id,__FUNCTION__);
        //   F=3:annotation_subset_id=1
        // flaga o skrócie 'F' w stanie 3; podtyp annotacji = 1
        // przeparsowanie ręczne do parametrów ekstraktora:
        $extractorParameters = array(
            "FlagName" => 'f',   // parse_extractor tu robi zawsze małą literę
            "FlagValues" => array(3),
            "Name" => 'annotation_subset_id',
            "Parameters" => array(1)
        );
        //$disamb_only = true;
        $lists = array();
        $subcorpora = array();
        //String tagging method from ['tagger', 'final', 'final_or_tagger', 'user:{id}']
        //$tagging_method = 'tagger';
        //$tagging_method = 'final_or_tagger';

        // wykreowanie elementów ekstraktora
        $extractorObj = new MockedExtractor($extractorParameters["FlagName"],$extractorParameters["FlagValues"],$extractorParameters["Name"],$extractorParameters["Parameters"]);
        //$extractors = $extractorObj->getExtractorsTable();
        $annotationsDBData = array(
            array( "id"=>1, "report_id"=>$report_id, "type_id"=>1, "type"=>'typ annotacji 1', "from"=>0, "to"=>4, "text"=>'tekst', "user_id"=>1, "creation_time"=>'2022-11-11 11:11:11', "stage"=>'final', "source"=>'user', "prop"=>'atrybut annotacji 1' ),
            array( "id"=>100, "report_id"=>$report_id, "type_id"=>2, "type"=>'typ annotacji 2', "from"=>5, "to"=>13, "text"=>'dokumentu', "user_id"=>2, "creation_time"=>'2022-11-11 11:11:12', "stage"=>'agreement', "source"=>'user', "prop"=>'atrybut annotacji 2' )
        );
        $extractorObj->setExtractorReturnedData('annotations',$annotationsDBData);

        // dane jakie powinny zawierać tabele bazy danych dla przeprowadzenia
        // testu
        $dbEmu = new DocumentExportDatabaseEmulator();
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
                array(  "from"=>0, "to"=>4, "eos"=>0,
                        "tags"=>array(
                            // tags rows for token
                            array( "disamb" => 0, "ctag" => 'ctag', "base_text" => 'text', 'stage'=>'final'),
                            array( "disamb" => 0, "ctag" => 'ctag2', "base_text" => 'base_text_taga_2', "stage"=>'final'),
                            array( "disamb" => 0, "ctag" => 'ctag2', "base_text" => 'base_text_taga_2', "stage"=>'agreement', "user_id"=>1 ),
                            array( "disamb" => 0, "ctag" => 'ctag3', "base_text" => 'base_text_taga_2', "stage"=>'agreement', "user_id"=>1 ),
                        ), // tags
                        // to poniżej tylko dla ustalania expected, nie jest
                        // wykorzystane przy generowaniu danych z bazy
                        "in_ann_ids"=>[1]
                ),
                array(  "from"=>5, "to"=>13, "in_ann_ids"=>[100], "eos"=>1 ) // no tags
 
            ) // tokens
        );
        $dbEmu->addReportsDB($report_id,$documentDBData); 

        // do test...
        global $db;
        $db = $dbEmu;

        $ce = new CorpusExporter();
        $output_folder = $this->dir->getWorkDirName();
        $extractor_stats = array(); // this will change
        // $extractors is var parameter, but shouldn't change
        $extractors = $extractorObj->getExtractorsTable();
        $expectedExtractors = $extractors;

        $ce->export_document($report_id,$extractors,$disamb_only,$extractor_stats,$lists,$output_folder,$subcorpora,$tagging_method);
            
        // check results in variables and files
        $this->assertEquals($expectedExtractors,$extractors);
        $expectedLists = array();
        $this->assertEquals($expectedLists,$lists);
        $expectedStats = array(
                $extractorObj->getStatisticsName() => array(
                    "annotations"=>count($annotationsDBData),
                    "relations"=>0,
                    "lemmas"=>0,
                    "attributes"=>0
                )
        );
        $this->assertEquals($expectedStats,$extractor_stats);

        $this->checkFiles($report_id,__FUNCTION__,$disamb_only,$tagging_method,$documentDBData,$annotationsDBData); 

    }

    private function checkFiles($report_id,$methodName,$disambOnly,$tagging_method,$reportData,$extractorData) {

        $this->checkConllFile($disambOnly,$tagging_method,$reportData);
        $this->checkIniFile($report_id,$reportData);
        $this->checkJsonFile($disambOnly,$tagging_method,$extractorData,$reportData["tokens"]);
        $this->checkTxtFile($reportData["content"]);
        $this->checkRelXmlFile();
        $this->checkXmlFile($disambOnly,$tagging_method,$extractorData,$reportData["tokens"],$reportData["content"]);

        // destructor removes all files and directories created
        unset($this->dir);

    } // checkFiles()

    private function selectExpectedTags($disambOnly,$tagging_method,$tagsData) {
        // jeśli tagging_method jest 'user:<userId>', wybiera tylko tagi
        // tego usera, ale takie, które nie mają odpowiadajacego mu taga
        // ze statusem 'final'
        // z tablicy tagów $tagsData wybiera:
        // wszystkie, jeśli $disambOnly jest False,
        // te które mają "disamb"=1 w przeciwnym wypadku.
        $userId = null;
        $tParts = explode(':',$tagging_method);
        if($tParts[0]=='user'){
            $userId = $tParts[1]; 
            $selectedTagsData = array();
            foreach($tagsData as $tagRow) {
                if ( (!isset($userId)) || ($tagRow["user_id"]==$userId) ) {
                    $isFinalIdenticalTag = false;
                    foreach($tagsData as $finalCandidate) {
                        // w oryginale jest zgodność 'ctag_id' i 'base_id'
                        if( ($finalCandidate['stage']!='agreement')
                           && ($tagRow['ctag'] == $finalCandidate['ctag'])
                           && ($tagRow['base_text'] == $finalCandidate['base_text'])
                        ) {
                            $isFinalIdenticalTag = true;
                        }
                    }
                    if(!$isFinalIdenticalTag) {
                        $selectedTagsData[] = $tagRow;
                    }
                }
            }
            $tagsData = $selectedTagsData;
        }

        if(!$disambOnly) {
            return $tagsData;
        }
        $selectedTagsData = array();
        foreach($tagsData as $tagRow) {
            if($tagRow["disamb"]
               && ( (!isset($userId)) || ($tagRow["user_id"]==$userId) )
                ){
                    $selectedTagsData[] = $tagRow;
            }
        }
        return $selectedTagsData;
 
    } // selectExpectedTags()

    private function annsDescForToken($token){
        // zwraca tablicę asocjacyjną z 2 polami:
        //  "tagStr" - tagi dla CONLL 
        //  "idsStr" - lista identyfikatorów annotacji
        // odpowiadajacą annotacjom związanym z danym tokenem,
        // których numery przekazano w $token["in_ann_ids"]
        $tagStr=""; $idsStr="";
        if(isset($token["in_ann_ids"])) {
            $idsStr=implode(":",$token["in_ann_ids"]);
            if(is_array($token["in_ann_ids"]) 
               && count($token["in_ann_ids"])>0 ) {
                $tagStr = "B-";
                $tagStr .= str_repeat(":I-",count($token["in_ann_ids"])-1);
            } else {
                $tagStr = '0'; // brak annotacji pasujących do tokena
            } 
        }
        return array( "tagStr"=>$tagStr, "idsStr"=>$idsStr );

    } // annsDescForToken()

    private function checkConllFile($disambOnly,$tagging_method,$reportData) {

        $expectedConllContent = "ORDER_ID\tTOKEN_ID\tORTH\tCTAG\tFROM\tTO\tANN_TAGS\tANN_IDS\tREL_IDS\tREL_TARGET_ANN_IDS\n";
        // ostatni token musi zawsze być końcem sentencji
        if(count($reportData["tokens"])>0)
            $reportData["tokens"][count($reportData["tokens"])-1]["eos"] = 1;
        $order_id = 0;
        $token_in_sentence = 0;
        foreach($reportData["tokens"] as $token){

            $from = $token["from"];
            $to = $token["to"];
            $visibleCharOnlyContent = preg_replace("/\n+|\r+|\s+/","",$reportData["content"]);
            $orth = substr($visibleCharOnlyContent,$from,$to-$from+1);
            // ctag jest wybierany tylko z pierwszego wiersza z tagami dla 
            // danego tokena, przy czym jeśli $disamb_only jest true liczą 
            // się tylko wiersze z "disamb"=1. Jeśli żadnego takiego wiersza 
            // nie ma jest pustym napisem
            $ctag = "";
            if(isset($token["tags"])){
                $selectedTagsData = $this->selectExpectedTags($disambOnly,$tagging_method,$token["tags"]);
                $ctag = count($selectedTagsData)>0 
                        ? $selectedTagsData[0]["ctag"] : "";
            }
            //$ann_tag = "B-"; // token wpada w całości do tylko 1-nej annotacji
            $a4t = $this->annsDescForToken($token);
            $ann_tag = $a4t["tagStr"]; $ann_ids = $a4t["idsStr"];

            $expectedConllContent .= "$order_id\t$token_in_sentence\t$orth\t$ctag\t$from\t$to\t$ann_tag\t$ann_ids\t_\t_\n";
            // na poczatku kolejnej sekwencji dodaje pustą linię
            $token_in_sentence++;
            if($token["eos"]) {
                $expectedConllContent .= "\n";
                $token_in_sentence = 0;
            } 
            $order_id++;
        }

        $resultConllFile = file_get_contents($this->dir->getBaseFilename().'.conll');
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
        $resultIniFile = file_get_contents($this->dir->getBaseFilename().'.ini');
        $this->assertEquals($expectedIniContent,$resultIniFile);

    } // checkIniFile()

    private function createJsonContent($disambOnly,$tagging_method,$extractorData,$tokensData) {

        $spc0   = "\n";
        $spc4   = "\n    ";
        $spc8   = "\n        ";
        $spc12  = "\n            ";
        $spc16  = "\n                ";
        $spc20  = "\n                    ";

        
        $chunkStrRows = array();
        $tokenStrRows = array();
        $extractorIndex = 0;
        foreach($tokensData as $token) {

            $from = $extractorData[$extractorIndex]["from"];
            $to = $extractorData[$extractorIndex]["to"];
            $text = $extractorData[$extractorIndex]["text"];
            $annId = $extractorData[$extractorIndex]["id"];
            // ctag jest wybierany tylko z pierwszego wiersza z tagami dla
            // danego tokena, przy czym jeśli $disamb_only jest true liczą
            // się tylko wiersze z "disamb"=1. Jeśli żadnego takiego wiersza
            // nie ma jest pustym napisem
            $ctag = 'null';
            if(isset($token["tags"])){
                $selectedTagsData = $this->selectExpectedTags($disambOnly,$tagging_method,$token["tags"]);
                $ctag = count($selectedTagsData)>0
                        ? '"'.$selectedTagsData[0]["ctag"].'"' : 'null';
            }

            $tokenAnnotationStr =   "[".$spc20
                                    ."    ".$annId  
                                    .$spc20."]";
            $tokenRelationStr   =   "[]";
            $tokenStr           =    $spc20."\"order_id\": $extractorIndex,"
                                    .$spc20."\"token_id\": $extractorIndex,"
                                    .$spc20."\"orth\": \"".$text."\","
                                    .$spc20."\"ctag\": ".$ctag.","
                                    .$spc20."\"from\": ".$from.","
                                    .$spc20."\"to\": ".$to.","
                                    .$spc20."\"annotations\": ".$tokenAnnotationStr.","
                                    .$spc20."\"relations\": ".$tokenRelationStr;
            $tokenStr           =   $spc16."{".$tokenStr.$spc16."}";
            $tokenStrRows[]     =   $tokenStr;

            $extractorIndex++;
        }
        $tokensStr  = implode(',',$tokenStrRows);
        

        $sentenceStr      = $spc12."[".$tokensStr.$spc12."]";
        $sentencesStr =     $spc8."[".$sentenceStr.$spc8."]";
        $chunkStr = $sentencesStr;
        $chunkStrRows[] = $chunkStr;
        
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

        $chunksStr          = "[".implode(',',$chunkStrRows).$spc4."]";
        $relationsStr       = "[]";
        $annotationsStr     = "[".implode(',',$annStrRows).$spc4."]";


        $JsonContent =
            "{"
            .$spc4."\"chunks\": $chunksStr,"
            .$spc4."\"relations\": $relationsStr,"
            .$spc4."\"annotations\": $annotationsStr"
            .$spc0."}";

        return $JsonContent;

    } // createJsonContent()

    private function checkJsonFile($disambOnly,$tagging_method,$extractorData,$tokensData) {

        $expectedJsonContent = $this->createJsonContent($disambOnly,$tagging_method,$extractorData,$tokensData);
        $resultJsonFile = file_get_contents($this->dir->getBaseFilename().'.json');
        $this->assertEquals($expectedJsonContent,$resultJsonFile);

    } // checkJsonFile()

    private function checkTxtFile($content) {

        $expectedTxtContent = $content;
        $resultTxtFile = file_get_contents($this->dir->getBaseFilename().'.txt');
        $this->assertEquals($expectedTxtContent,$resultTxtFile);

    } // checkTxtFile()

    private function checkRelXmlFile() {

        $expectedRelxmlContent = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE chunkList SYSTEM \"ccl.dtd\">\n<relations>\n</relations>\n";
        $resultRelxmlFile = file_get_contents($this->dir->getBaseFilename().'.rel.xml');
        $this->assertEquals($expectedRelxmlContent,$resultRelxmlFile);

    } // checkRelXmlFile()

    private function checkXmlFile($disambOnly,$tagging_method,$extractorData,$tokensData,$content) {
        $scl=new SimpleCcl($content);
        foreach($extractorData as $extractorRow) {
            $scl->addAnnotation($extractorRow["id"],$extractorRow["type"],$extractorRow["from"],$extractorRow["to"],$extractorRow["text"]);
        }
        // dodanie tagów do tokenów - tylko jak $disambOnly = false
        // lub "disamb" w tagu jest true
        $scl->addTagsForTokens($tokensData,$tagging_method,$disambOnly);
        $chunkList = $scl->toXML();        

        $expectedXmlContent = 
        "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
        ."<!DOCTYPE chunkList SYSTEM \"ccl.dtd\">\n"
        .$chunkList."\n";
        $resultXmlFile = file_get_contents($this->dir->getBaseFilename().'.xml');
        $this->assertEquals($expectedXmlContent,$resultXmlFile);

    } // checkXmlFile()

} // class

?>

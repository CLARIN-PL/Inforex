<?php


class DocumentExportDatabaseEmulator extends DatabaseEmulator {

    private function addReportCorporaExtDB($report_id, $corpora_ext=null){

        //  DbReport::getReportExtById($report_id)
        //
        // exCorpus - corpora.ext empty here
        // corpora[reports[$report_id]["corpora"]]["ext]
        // zawsze sprawdza rekord korpusu wskazywany przez
        // reports[$report_id]["corpora"] wywołaniem:
        //  DbCorpora::getCorporaById() która 1 wiersz lub null
        // aby ustalić wartość ext dla tego korpusu ( to pole musi być )

        $corporaRows = array(
                            array( 'ext'=>$corpora_ext )
                        );
        $this->setResponse("fetch_rows",
            'SELECT * FROM corpora WHERE id = ?',
            $corporaRows );

    } // addReportCorporaExtDB()

    private function addReportFlagDB($report_id, $flagDBData ) {
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
        $this->setResponse("fetch_rows",
'SELECT cf.short, rf.flag_id FROM reports_flags rf  JOIN corpora_flags cf USING (corpora_flag_id) WHERE rf.report_id = ?',
            $allReturnedDataRows );

    } // addReportFlagDB()

    private function addTagsDB($report_id, $token_ids, $tagsData ) {

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
            // tylko bez ustawionego user_id
            if( !isset($tagRow["user_id"])
                || $tagRow["user_id"]==null) {
                $tags[] = array( 'token_tag_id' => $id, 'token_id' => $token_id, 'disamb' => $tagRow["disamb"], 'tto.ctag_id' => $tto_ctag_id, 'ctag_id' => $ctag_id, 'ctag'=>$tagRow["ctag"], 'tagset_id' => $tagset_id, 'base_id' => $base_id, 'base_text' => $tagRow["base_text"] );
                $id++;
            }
        }
        $allReturnedDataRows = $tags;
        // for tagger_method = 'tagger' i 'final_or_tagger'
        $this->setResponse("fetch_rows",
'SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, ttc.tagset_id, b.id as base_id, b.text as base_text FROM `tokens_tags_optimized` as tto JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id JOIN bases as b on tto.base_id = b.id WHERE tto.user_id IS NULL  AND token_id IN ('.implode(',',$token_ids).');',
            $allReturnedDataRows );
        // for tagger_method = 'final' i 'final_or_tagger'
        //   dodane pole user_id
        $tags = array(); $id=1;
        foreach($tagsData as $tagRow) {
            // tylko ze stage='final'
            if($tagRow["stage"]=='final') {
                $tags[] = array( 'token_tag_id' => $id, 'token_id' => $token_id, 'disamb' => $tagRow["disamb"], 'tto.ctag_id' => $tto_ctag_id, 'ctag_id' => $ctag_id, 'ctag'=>$tagRow["ctag"], 'tagset_id' => $tagset_id, 'base_id' => $base_id, 'base_text' => $tagRow["base_text"], 
    'user_id' => isset($tagRow["user_id"]) ? $tagRow["user_id"] : '' );
                $id++;
            }
        }
        $allReturnedDataRows = $tags; 
        $this->setResponse("fetch_rows",
"SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, ttc.tagset_id, b.id as base_id, b.text as base_text, tto.user_id              FROM `tokens_tags_optimized` as tto              JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id             JOIN bases as b on tto.base_id = b.id  JOIN tokens tok on tok.token_id = tto.token_id  WHERE tto.stage = 'final'   AND report_id in (".$report_id.") ;",
            $allReturnedDataRows );
        // for tagger_method = 'user:{id}' 
        //  brak w wynikach pola "tagset_id"
        $tags = array(); $id=1;
        foreach($tagsData as $tagRow) {
            // tylko ze stage='agreement' i 'user_id'=1
            if($tagRow["stage"]=='agreement' and $tagRow["user_id"]=1) {
                $tags[] = array( 'token_tag_id' => $id, 'token_id' => $token_id, 'disamb' => $tagRow["disamb"], 'tto.ctag_id' => $tto_ctag_id, 'ctag_id' => $ctag_id, 'ctag'=>$tagRow["ctag"], 'base_id' => $base_id, 'base_text' => $tagRow["base_text"], 'user_id' => $tagRow["user_id"] );
                $id++;
            }
        }
        $allReturnedDataRows = $tags;
        $this->setResponse("fetch_rows",
"SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, b.id as base_id, b.text as base_text, tto.user_id FROM `tokens_tags_optimized` as tto JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id JOIN bases as b on tto.base_id = b.id WHERE (tto.user_id = "."1".") AND (tto.stage = 'agreement') AND token_id IN (".implode(',',$token_ids).");",
            $allReturnedDataRows );

    } // addTagsDB()

    private function addTokenDB($report_id, $tokensDBData) {

        // te dane nie mają wpływu na wynik exportu
        $orth_id = 1;   // tekst orth jest wycinany z report["content"]
                        // a nie z tabeli orths na podstawie tego indeksu

        $tokens = array();
        $token_id = 1;
        $token_ids = array();
        $tagsData = array();
        foreach($tokensDBData as $tokenDBData) {
            $tokens[] = array( "token_id" => $token_id, "report_id" => $report_id, "from" =>  $tokenDBData["from"], "to" => $tokenDBData["to"], "eos" => $tokenDBData["eos"], "orth_id" => $orth_id );
            $token_ids[] = $token_id;
            if(isset($tokenDBData["tags"])){
                $tagsData = array_merge($tagsData,$tokenDBData["tags"]);
            }
            $token_id++;
        }
 
        $allReturnedDataRows = $tokens;
        $this->setResponse("fetch_rows",
' SELECT  *  FROM tokens  LEFT JOIN orths USING (orth_id) WHERE report_id = ? ORDER BY `from`',
            $allReturnedDataRows );

        // to jest robione jednym wspólnym zapytaniem dla wszystkich
        // token_ids na raz

        $this->addTagsDB($report_id, $token_ids, $tagsData);

    } // addTokenDB()

    public function addReportsDB($report_id, $documentDBData ) {

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
        $this->setResponse("fetch_rows",
            'SELECT * FROM reports WHERE id = ?',
            $allReturnedDataRows );

        $formatName = 'xml'; // for format_id=1
        $this->setResponse("fetch_one",
            'SELECT format FROM reports_formats WHERE id = ?',
            $formatName   // fetch_one - only string without array packing
            );

        $this->addReportFlagDB( $report_id, $documentDBData['flags'] );
        $this->addTokenDB( $report_id, $documentDBData['tokens'] );
        $this->addReportCorporaExtDB( $report_id, $corpora_ext );

    } // addReportsDB()
   
} // DocumentExportDatabaseEmulator class

?>

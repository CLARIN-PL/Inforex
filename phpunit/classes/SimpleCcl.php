<?php

class SimpleCcl {

    const CHUNK_SEPARATOR = '<\chunk>';
    const SENTENCE_SEPARATOR = '.';
    const WHITE_CHARS_PATTERN = "/^(\n|\r|\s)+/s";

    private $content = '';
    private $chunks = array();

    private $tokensCount = 0; 

    public function __construct($reportData,$tagging_method,$disambOnly) {

        $this->content = $reportData["content"];
        $this->tokensCount = 0;
        $this->chunks = $this->parse($this->content);
        $this->addTagsForTokens($reportData["tokens"],$tagging_method,$disambOnly);

    }

    private function getText($from,$to) {

        return mb_substr($this->content,$from,$to-$from+1);

    } // getText

    public function getChunks() {

        $chunkss = array();
        foreach($this->chunks as $chunk){
            $chunks[] = $chunk;
        }
        return $chunks;

    } // getChunks()

    public function getSentences() {

        $sentences = array();
        foreach($this->chunks as $chunk){
            if(isset($chunk["sentences"])){
                foreach($chunk["sentences"] as $sentence){
                    $sentences[] = $sentence;
                }
            }
        }
        return $sentences;

    } // getSentences()


    public function getTokens() {

        $tokens = array();
        foreach($this->chunks as $chunk){
            if(isset($chunk["sentences"])){
                foreach($chunk["sentences"] as $sentence){
                    if(isset($sentence["tokens"])){
                        foreach($sentence["tokens"] as $token){
                            $tokens[] = $token;
                        }
                    }
                }
            }
        }
        return $tokens;

    } // getTokens()

    public function getAnnotations($withoutChannelAnnotations=true) {

        $annotations = array();
        foreach($this->getTokens() as $token){
            $annotations = array_merge($annotations,self::annotationsForToken($token,$withoutChannelAnnotations)); 
        }
        return $annotations;

    } // getAnnotations()

    private function recognizeToken($token,&$sentence) {

        if(isset($token["to"])) {
            // token niepusty - uzupełnij tekst
            $token["text"] = $this->getText($token["from"],$token["to"]);
            // for tokens global index
            $token["idx"] = ++$this->tokensCount;
            $token["annotations"] = array(); // initial empty
            $token["tags"] = array(); // initial empty
            // dopisz do sekwencji nadrzędnej
            $sentence["tokens"][] = $token;
        }
        
    } // recognizeToken

    private function recognizeSentence($sentence,&$chunk) {

        if(isset($sentence["to"])) {
            // sentencja niepusta - uzupełnij tekst
            $sentence["text"] = $this->getText($sentence["from"],$sentence["to"]);
            $sentence["idx"] = count($chunk["sentences"])+1;
            // dopisz do chunka nadrzędnego
            $chunk["sentences"][] = $sentence;
        }

    } // recognizeSentence

    private function recognizeChunk($chunk,&$chunks) {

        if(isset($chunk["to"])) {
            // chunk niepusty - uzupełnij tekst
            $chunk["text"] = $this->getText($chunk["from"],$chunk["to"]);
            $chunk["idx"] = count($chunks)+1; 
            // dopisz do tablicy chunków
            $chunks[] = $chunk;
        }

    } // recognizeChunk

    private function parse($contentText) {
        // zwraca drzewo rozkładu tekstu $contentText na elementy CCL
        // w postaci tablicy zgnieżdżonej: chunk, sentence, token
        
        // podział na chunki:
        //  jest jeden chunk, chyba, że są w tekście elementy rozdzielające
        // <\chunk>
        //$chunkList = explode('<\\chunk>', $contentText);
        $chunkList = explode(self::CHUNK_SEPARATOR, $contentText);

        $chunks = array(); $sentences = array(); $tokens = array();
        $contentLength = mb_strlen($contentText);
        $nonSpaceIdx=0; // indeks numerujący z pominięciem znaków
                        // niedrukowalnych
        $chunk=["from"=>0, "nsiFrom"=>$nonSpaceIdx]; 
        $sentence=["from"=>0, "nsiFrom"=>$nonSpaceIdx]; 
        $token=["from"=>0, "nsiFrom"=>$nonSpaceIdx];
        for($cIx=0;$cIx<$contentLength;) {
            // koniec tokena - białe znaki na początku pozostałego tekstu
            if(preg_match(self::WHITE_CHARS_PATTERN,
                            mb_substr($contentText,$cIx),
                            $whiteChars)) {
                $token["eos"] = 0;
                $this->recognizeToken($token,$sentence);
                $cIx += mb_strlen($whiteChars[0]);
                $token=["from"=>$cIx, "nsiFrom"=>$nonSpaceIdx];
            }            
            // koniec sentencji
            else if(mb_substr($contentText,$cIx,mb_strlen(self::SENTENCE_SEPARATOR))
                    == self::SENTENCE_SEPARATOR) {
                $token["eos"] = 1;
                $this->recognizeToken($token,$sentence);
                $this->recognizeSentence($sentence,$chunk);
                $cIx += mb_strlen(self::SENTENCE_SEPARATOR);
                $sentence=["from"=>$cIx, "nsiFrom"=>$nonSpaceIdx];
                $token=["from"=>$cIx, "nsiFrom"=>$nonSpaceIdx];
            }
            // koniec chunka
            else if(mb_substr($contentText,$cIx,mb_strlen(self::CHUNK_SEPARATOR))
                    == self::CHUNK_SEPARATOR) {
                $token["eos"] = 1;
                $this->recognizeToken($token,$sentence);
                $this->recognizeSentence($sentence,$chunk);
                $this->recognizeChunk($chunk,$chunks);
                $cIx += mb_strlen(self::CHUNK_SEPARATOR);   
                $chunk=["from"=>$cIx, "nsiFrom"=>$nonSpaceIdx];
                $sentence=["from"=>$cIx, "nsiFrom"=>$nonSpaceIdx];
                $token=["from"=>$cIx, "nsiFrom"=>$nonSpaceIdx];
            // zwykły znak tekstu
            } else {
                $chunk["to"]=$cIx; $sentence["to"]=$cIx; $token["to"]=$cIx;
                $chunk["nsiTo"]=$nonSpaceIdx; 
                $sentence["nsiTo"]=$nonSpaceIdx; 
                $token["nsiTo"]=$nonSpaceIdx;
                $nonSpaceIdx++;
                $cIx++;
            }
        }
        // ostatni chunk dodajemy, jeśli koniec tekstu
        $token["eos"] = 1;
        $this->recognizeToken($token,$sentence);
        $this->recognizeSentence($sentence,$chunk);
        $this->recognizeChunk($chunk,$chunks);

        return $chunks;

    } // parse

    public function addAnnotations($annotationsList) {

        foreach($annotationsList as $annotation) {
            $this->addAnnotation($annotation);
        }

    } // addAnnotations()

    public function addAnnotation($annotation) {

        $id = $annotation["id"];
        $type = $annotation["type"];
        $from = $annotation["from"];
        $to = $annotation["to"];
        $text = $annotation["text"];
        // annotacja jest dodawana do tokenów należących do sentencji
        // do których wpada zakresem [$from,$to] liczonym bez spacji
        foreach($this->chunks as &$chunk){
            foreach($chunk["sentences"] as &$sentence){
                //if(($from < $sentence["nsiTo"])) {
                if(($from >= $sentence["nsiFrom"])
                    && ($to <= $sentence["nsiTo"])
                    ) {
                    foreach($sentence["tokens"] as &$token){
                        if(($from == $token["nsiFrom"])
                            && ($to <= $token["nsiTo"])
                        ){
                            $token["annotations"][] = $annotation;
                        } else { // not matched - add id 0 for channel
                            $token["annotations"][] = array("id"=>0,"type"=>$type);
                        }
                    }
                }
            }
        }

    } // addAnnotation()

    public function addAttributes($annotationsList) {

        if(!is_array($annotationsList))
            return;
        foreach($annotationsList as $annotation) {
            $this->addAttribute($annotation);
        }

    } // addAttributes()

    public function addAttribute($annotation) {

        $from = $annotation["from"];
        $to = $annotation["to"];
        // atrybut jest dodawana do tokenów należących do sentencji
        // do których wpada zakresem [$from,$to] liczonym bez spacji
        foreach($this->chunks as &$chunk){
            foreach($chunk["sentences"] as &$sentence){
                if(($from >= $sentence["nsiFrom"])
                    && ($to <= $sentence["nsiTo"])
                    ) {
                    foreach($sentence["tokens"] as &$token){
                        if(($from == $token["nsiFrom"])
                            && ($to <= $token["nsiTo"])
                        ){
                            $token["attributes"][] = $annotation;
                        }
                    }
                }
            }
        }

    } // addAttribute() 

    public function addTag($tokenFrom,$tokenTo,$tagDisamb,$tagBasetext,$tagCtag,$tagStage,$tagUserId) {
        // dla tokena o zgodnych wartościach [from,to] dodaje tag o parametrach
        // $tagDisamb,$tagBasetext,$tagCtag
        foreach($this->chunks as &$chunk){
            foreach($chunk["sentences"] as &$sentence){
                foreach($sentence["tokens"] as &$token){
                    if( ($tokenFrom==$token["nsiFrom"])
                        && ($tokenTo==$token["nsiTo"]) ) {
                        $token["tags"][] = array("disamb"=>$tagDisamb, "base_text"=>$tagBasetext, "ctag"=>$tagCtag, "stage"=>$tagStage, "user_id"=>$tagUserId);
                    }
                }
            }
        }         

    } // addTag()

    private function tagsForUserOnly($Tags,$userId) {

        // wybiera tylko tagi, które mają pole "user_id" = $userId
        $result = array();
        foreach($Tags as $tag) {
            if($tag["user_id"]==$userId) {
                $result[] = $tag;
            }
        }
        return $result;

    } // tagsForUserOnly()

    private function isTagsEqual($tag1,$tag2) {

        // są równe gdy mają identyczne 'ctag' i 'base_text'
        // ( w oryginale 'ctag_id' i 'base_id' )
        return (    ($tag1['ctag']==$tag2['ctag'] )
                &&  ($tag1['base_text']==$tag2['base_text'] ) );

    } // isTagEqual()

    private function isTagMaskedByFinal($agreementTag,$allTags){

        foreach($allTags as $tag) {
            if($tag["stage"]!='agreement') { // żeby sie nie maskował sam sobą
                if($this->isTagsEqual($agreementTag,$tag))
                    return True; // found identical
            }
        }
        return False; // not found

    } // isTagMaskedByFinal()

    private function onlyNonUserTags($tags) {

        $result = array();
        foreach($tags as $tag){
            if((!isset($tag['user_id'])) || ($tag['user_id']==null)) {
                $result[] = $tag;
            }
        }
        return $result;

    } // onlyNonUsertags()

    private function removeNonUserAndMaskedTags($Tags,$tagging_method){

        $userId = null;
        $tParts = explode(':',$tagging_method);
        if($tParts[0]=='user')
            $userId = $tParts[1];
        else
            return $this->onlyNonUserTags($Tags); // dla innych tagging_method
        // tu mamy tagging_method user:$userId

        // odrzuć wszystkie tagi, które nie mają właściwego "user_id"
        // i nie są maskowane identycznym tagiem 'final'
        $selected = array();
        foreach($Tags as $tag){
            if($tag["user_id"]==$userId) {
                if(!$this->isTagMaskedByFinal($tag,$Tags)) {
                    $selected[] = $tag;
                }
            }
        }
        return $selected;
        
    } // removeNonUserAndMaskedTags

    private static function annotationsForToken($token,$withoutChannelAnnotations=true){

        // zwraca listę annotacji związanych z tokenem
        // z uwzględnieniem lub nie, sztucznych annotacji z id=0
        // utworzonych na potrzeby kanałów
        $annsForToken = array();
        if(is_array($token["annotations"])){
            foreach($token["annotations"] as $annotation) {
                if((!$withoutChannelAnnotations) || $annotation["id"]) {
                    // id=0 dummy annotation for channel
                    $annsForToken[] = $annotation;
                }
            } // foreach
        } // if
        return $annsForToken;

    } // annotationsForToken()
 
    private static function annotationIdsForToken($token,$withoutChannelAnnotations=true){

        // zwraca listę ID annotacji związanych z tokenem
        // z uwzględnieniem lub nie, sztucznych annotacji z id=0
        // utworzonych na potrzeby kanałów
        $annIdsForToken = array();
        foreach(self::annotationsForToken($token,$withoutChannelAnnotations) as $annotation) {
            $annIdsForToken[] = $annotation["id"];
        }
        return $annIdsForToken;

    } // annotationIdsForToken()

    public static function tagStrForToken($token) {

        //  tagi dla CONLL odpowiadajace annotacjom związanym 
        // z danym tokenem,

        $annotationCount = count(self::annotationIdsForToken($token,True));
        if($annotationCount>0) {
            $tagStr = "B-";
            $tagStr .= str_repeat(":I-",$annotationCount-1);
        } else {
            $tagStr = 'O'; // brak annotacji pasujących do tokena
        }
        return $tagStr;

    } // tagStrForToken()

    public static function annIdsStrForToken($token,$strSeparator = ':') {

        // zwraca napis z listą ID annotacji związanych z tokenem
        // rozdzieloną separatorem $strSeparator, lub "_" jeśli pusta
        // lista
        $lista = self::annotationIdsForToken($token,True);
        return 
            count($lista)>0 
            ? implode($strSeparator,self::annotationIdsForToken($token,True))
            : "_" ;

    } // annIdsStrForToken()

    public function addTagsForTokens($tokensData,$tagging_method,$disambOnly){

        // $tokensData lista tokenów, każdy ma pola "from","to" i "tags"
        // "tags" to tablica tagów opisanych przez pola 
        //      ["disamb","base_text","ctag"]
        // Dodaje te wszystkie tagi do tokenów odszukanych w $this
        // na podstawie zgodności bezspacjowych ["from","to"]

        // jeśli $tagging_method='user:<userId>' to odrzuca wszystkie
        // tagi, ktore nie mają user_id=<userId> oraz te, które mimo
        // user_id=<userId>, mają identyczne tagi ze statusem 'final'

        // dodanie tagów do tokenów - tylko jak $disambOnly = false
        // lub "disamb" w tagu jest true
        foreach($tokensData as $token) {
            if(isset($token["tags"])){
                $tags = $this->removeNonUserAndMaskedTags($token["tags"],$tagging_method);
                foreach($tags as $tag){
                    if( (!$disambOnly) || ($tag["disamb"]) ) {
                        $this->addTag($token["from"],$token["to"],$tag["disamb"],$tag["base_text"],$tag[ctag],
                        isset($tag["stage"]) ? $tag["stage"] : null,
                        isset($tag["user_id"]) ? $tag["user_id"] : null);
                    }
                }
            }
        } 

    } // addTagsForTokens()

    private function annotationsToChannels($annArray) {

        $channels = array();
        foreach($annArray as $annotation){
            if($annotation["type"]) {
                if(isset($channels[$annotation["type"]])){
                    $channels[$annotation["type"]][] = $annotation["id"];
                } else {
                    $channels[$annotation["type"]] = array($annotation["id"]);
                }
            }
        }
        return $channels;

    } // annotationsToChannels

    private function attributesToChannels($annArray) {

        $channels = array();
        foreach($annArray as $annotation){
            if($annotation["name"]) {
                $key = $annotation["type"].":".$annotation["name"];
                if(isset($channels[$key])){
                    $channels[$key][] = $annotation["value"];
                } else {
                    $channels[$key] = array($annotation["value"]);
                }
            }
        }
        return $channels;

    } // attributesToChannels

    private function spc($count) {

        return str_repeat(' ',$count);

    } // spc()

    public function toCONLL() {

        $conll = "ORDER_ID\tTOKEN_ID\tORTH\tCTAG\tFROM\tTO\tANN_TAGS\tANN_IDS\tREL_IDS\tREL_TARGET_ANN_IDS\n";
        $order_id = 0;
        $token_in_sentence = 0;
        foreach($this->getTokens() as $token){
            $from = $token["nsiFrom"];
            $to = $token["nsiTo"];
            $orth = $token["text"];
            // ctag jest wybierany tylko z pierwszego wiersza z tagami dla
            // danego tokena. Jeśli żadnego takiego wiersza nie ma jest 
            // pustym napisem
            $ctag = count($token["tags"])>0 ? $token["tags"][0]["ctag"] : "";

            $conll .= "$order_id\t$token_in_sentence\t$orth\t$ctag\t$from\t$to\t".self::tagStrForToken($token)."\t".self::annIdsStrForToken($token)."\t_\t_\n";

            // na poczatku kolejnej sekwencji dodaje pustą linię
            $token_in_sentence++;
            if($token["eos"]) {
                $conll .= "\n";
                $token_in_sentence = 0;
            }
            $order_id++;
        }
        return $conll;
  
    } // toCONLL()

    private static function tagFamilyToJSON($elements) {

        $spc4   = "\n    ";
        $spc8   = "\n        ";
        $spc12  = "\n            ";

        $elementStrRows = array();
        if((is_array($elements)) && (count($elements)>0)){
            foreach($elements as $element) {
                if(is_array($element)) {
                    $elementRows = [];
                    foreach($element as $key=>$value){
                        if( ($key=="text")
                            || ($key=="creation_time")
                            || ($key=="stage")
                            || ($key=="source")
                            || ($key=="type")
                            || ($key=="lemma")
                            || ($key=="login")
                            || ($key=="screename")
                            || ($key=="prop")
                            || ($key=="name")
                            || ($key=="rsname")
                           ){
                            $value = '"'.$value.'"';
                        }
                        $elementRows[] = $spc12.'"'.$key.'": '.$value;
                    }
                    $elementStr =  $spc8."{".
                                    implode(',',$elementRows).
                                    $spc8."}";
                }
                $elementStrRows[] = $elementStr;
            }
            $elementsStr       = "[".implode(',',$elementStrRows).$spc4."]";
        } else {  // empty $elements array
            $elementsStr   = "[]";
        }

        return $elementsStr;

    } // tagFamilyToJSON()

    public function toJSON($annotations = null, $relations = null) {

        $spc0   = "\n";
        $spc4   = "\n    ";
        $spc8   = "\n        ";
        $spc12  = "\n            ";
        $spc16  = "\n                ";
        $spc20  = "\n                    ";

        $chunkStrRows = array();
        $tokenStrRows = array();
        $extractorIndex = 0;
        $extractorData = $this->getAnnotations();
        foreach($this->getTokens() as $token) {

            $from = $token["nsiFrom"];
            $to = $token["nsiTo"];
            $text = $token["text"];
            $annId = $extractorData[$extractorIndex]["id"];
            // ctag jest wybierany tylko z pierwszego wiersza z tagami dla
            // danego tokena, przy czym jeśli $disamb_only jest true liczą
            // się tylko wiersze z "disamb"=1. Jeśli żadnego takiego wiersza
            // nie ma jest pustym napisem
            $ctag = count($token["tags"])>0 ? '"'.$token["tags"][0]["ctag"].'"' : 'null';

            $tokenAnnotationStr =   isset($annId)
                                    ? "[".$spc20."    ".$annId.$spc20."]"
                                    : "[]";
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
        $chunksStr          = "[".implode(',',$chunkStrRows).$spc4."]";

        $relationsStr = self::tagFamilyToJSON($relations);
        $annotationsStr = self::tagFamilyToJSON($annotations);
 
        $JsonContent =
            "{"
            .$spc4."\"chunks\": $chunksStr,"
            .$spc4."\"relations\": $relationsStr,"
            .$spc4."\"annotations\": $annotationsStr"
            .$spc0."}";

        return $JsonContent;    

    } // toJSON()

    public function toXML() {

        $indent = 0;
        $xml = $this->spc($indent++)."<chunkList>"; 
        foreach($this->chunks as $chunk){
            $xml .= "\n".$this->spc($indent++)."<chunk id=\"ch".$chunk["idx"]."\" type=\"p\">";
            foreach($chunk["sentences"] as $sentence){
                $xml .= "\n".$this->spc($indent++)."<sentence id=\"sent".$chunk["idx"]."\">"; 
                foreach($sentence["tokens"] as $token){
                    $xml .= "\n".$this->spc($indent++)."<tok>";
                    $xml .= "\n".$this->spc($indent)."<orth>".$token["text"]."</orth>";
                    // tags 4 token
                    foreach($token["tags"] as $tag) {
                        $xml .= "\n".$this->spc($indent)."<lex".
                                ($tag["disamb"]?' disamb="'.$tag["disamb"].'"':"").
                                '><base>'.$tag["base_text"].'</base><ctag>'.$tag["ctag"].'</ctag></lex>';
                    }
                    // annotations 4 token
                    $channels = $this->annotationsToChannels($token["annotations"]);
                    foreach($channels as $type=>$annIds){
                        $annIdsStr = implode(",",$annIds);
                        $xml .= "\n".$this->spc($indent)."<ann chan=\"".$type."\">".$annIdsStr."</ann>";    
                    }
                    // attributes 4 token
                    if(is_array($token["attributes"])) {
                        foreach($token["attributes"] as $attr) {
                            $xml .= "\n".$this->spc($indent)."<prop key=\"".$attr["type"].":".$attr["name"]."\">".$attr["value"]."</prop>";
                        }       
                    } // is_array()
                    $xml .= "\n".$this->spc(--$indent)."</tok>";
                }
                $xml .= "\n".$this->spc(--$indent)."</sentence>";
            }
            $xml .= "\n".$this->spc(--$indent)."</chunk>";
        }
        $xml .= "\n".$this->spc(--$indent)."</chunkList>";

        $xml =
        "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
        ."<!DOCTYPE chunkList SYSTEM \"ccl.dtd\">\n"
        .$xml."\n";
 
        return $xml;

    } // toXML()
}

?>

<?php

class SimpleCcl {

    const CHUNK_SEPARATOR = '<\chunk>';
    const SENTENCE_SEPARATOR = '.';
    const WHITE_CHARS_PATTERN = "/^(\n|\r|\s)+/s";

    private $content = '';
    private $chunks = array();

    public function __construct($contentText) {

        $this->content = $contentText;
        $this->chunks = $this->parse($contentText);

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

    private function recognizeToken($token,&$sentence) {

        if(isset($token["to"])) {
            // token niepusty - uzupełnij tekst
            $token["text"] = $this->getText($token["from"],$token["to"]);
            // for tokens global index
            $token["idx"] = count($this->getTokens())+1;
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

    public function addAnnotation($id,$type,$from,$to,$text) {
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
                            $token["annotations"][] = array("id"=>$id,"type"=>$type,"text"=>$text);
                        } else { // not matched - add id 0 for channel
                            $token["annotations"][] = array("id"=>0,"type"=>$type);
                        }
                    }
                }
            }
        }

    } // addAnnotation()

    public function addTag($tokenFrom,$tokenTo,$tagDisamb,$tagBasetext,$tagCtag) {
        // dla tokena o zgodnych wartościach [from,to] dodaje tag o parametrach
        // $tagDisamb,$tagBasetext,$tagCtag
        foreach($this->chunks as &$chunk){
            foreach($chunk["sentences"] as &$sentence){
                foreach($sentence["tokens"] as &$token){
                    if( ($tokenFrom==$token["nsiFrom"])
                        && ($tokenTo==$token["nsiTo"]) ) {
                        $token["tags"][] = array("disamb"=>$tagDisamb, "base_text"=>$tagBasetext, "ctag"=>$tagCtag);
                    }
                }
            }
        }         

    } // addTag()

    public function addTagsForTokens($tokensData,$disambOnly){

        // $tokensData lista tokenów, każdy ma pola "from","to" i "tags"
        // "tags" to tablica tagów opisanych przez pola 
        //      ["disamb","base_text","ctag"]
        // Dodaje te wszystkie tagi do tokenów odszukanych w $this
        // na podstawie zgodności bezspacjowych ["from","to"]

        // dodanie tagów do tokenów - tylko jak $disambOnly = false
        // lub "disamb" w tagu jest true
        foreach($tokensData as $token) {
            if(isset($token["tags"])){
                foreach($token["tags"] as $tag){
                    if( (!$disambOnly) || ($tag["disamb"]) ) {
                        $this->addTag($token["from"],$token["to"],$tag["disamb"],$tag["base_text"],$tag[ctag]);
                    }
                }
            }
        } 

    } // addTagsForTokens()

    private function annotationsToChannels($annArray) {

        $channels = array();
        foreach($annArray as $annotation){
            if(isset($channels[$annotation["type"]])){
                $channels[$annotation["type"]][] = $annotation["id"];
            } else {
                $channels[$annotation["type"]] = array($annotation["id"]);
            }
        }
        return $channels;

    } // annotationsToChannels

    private function spc($count) {

        return str_repeat(' ',$count);

    } // spc()

    public function toXML() {

        $indent = 0;
        $xml = $this->spc($indent++)."<chunkList>"; 
        foreach($this->chunks as $chunk){
            $xml .= "\n".$this->spc($indent++)."<chunk id=\"ch".$chunk["idx"]."\" type=\"\">";
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
                    $xml .= "\n".$this->spc(--$indent)."</tok>";
                }
                $xml .= "\n".$this->spc(--$indent)."</sentence>";
            }
            $xml .= "\n".$this->spc(--$indent)."</chunk>";
        }
        $xml .= "\n".$this->spc(--$indent)."</chunkList>";

        return $xml;

    }
}

?>

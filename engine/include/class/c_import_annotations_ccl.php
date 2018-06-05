<?php
class Import_Annotations_CCL{

    var $reader;
    var $path;
    var $document;
    var $annotationMap;
    var $document_id;
    var $user_id;
    var $stage;
    var $source;
    var $annotation_set_id;
    var $ignore_duplicates;
    var $ignore_unknown_types;

    function __construct($path, $document_id, $user_id, $stage, $source, $annotation_set_id, $ignore_duplicates, $ignore_unknown_types){
        $this->reader = new TakipiReader();
        $this->path = $path;
        $this->document = array();
        $this->document_id = $document_id;
        $this->user_id = $user_id;
        $this->stage = $stage;
        $this->source = $source;
        $this->annotation_set_id = $annotation_set_id;
        $this->ignore_duplicates = $ignore_duplicates;
        $this->ignore_unknown_types = $ignore_unknown_types;
    }

    function read(){
        $this->reader->loadFile($this->path);

        $s = 0;
        $t = 0;
        $chunks = array();
        while ($this->reader->nextChunk()){
            $chunks[] = $this->reader->readChunk();
        }
        $chunkNum = 0;
        foreach ($chunks as $chunk){
            $s += count($chunk->sentences);
            foreach ($chunk->sentences as $sentence){
                $tokensNum = count($sentence->tokens);
                $t += $tokensNum;
            }
        }

        /* Poskładaj dokument */
        foreach ($chunks as $chunk){
            $document_part = $chunk->id;
            $this->document[$document_part] = $chunk;
        }

        /* Sprawdź kolejność części */
        $i=1;
        foreach ($this->document as $no=>$part){
            if ( $no != "ch".$i++)
                throw new Exception("Missing part for document $this->document_id");
        }


    }

    function processAnnotationns(){
        $annotationMap = array();
        $sentenceNum = 0;
        $takipiText = "";

        // Iteruj po częściach dokumentu
        foreach ($this->document as $chunk){
            // Iteruj po zdaniach w każdej części
            foreach ($chunk->sentences as $sentence){
                // Utwórz tablicę annotacji dla bieżacego zdania
                $annotationMap[$sentenceNum]=array();
                // Iteruj po tokenach w zdaniu
                foreach ($sentence->tokens as $token){
                    // Iteruj po typach annotacji dla tokena
                    foreach ($token->channels as $channel=>$value){
                        if(strpos($channel, "head") > 0)
                            var_dump($channel);
                        // Lemat bieżącej annotacji
                        $lemma = array_key_exists($channel,$token->lemmas)?$token->lemmas[$channel]:"";
                        // Identyfikator annotacji dla kanału(typu) w zdaniu
                        $intvalue = intval($value);

                        // Jeśli identyfikator jest dodatni - przetwarzamy annotację
                        if ($intvalue>0){

                            // Jeśli jest to pierwsza annotacja danego typu w zdaniu - zainicjuj tablicę annotacji
                            // danego typu dla bieżącego zdania
                            if (!array_key_exists($channel, $annotationMap[$sentenceNum])){
                                $annotationMap[$sentenceNum][$channel] = array();
                                // Ostatnio odwiedzona annotacja
                                $annotationMap[$sentenceNum][$channel]['lastval'] = $intvalue;
                                // Informacje o annotacji
                                $annotationMap[$sentenceNum][$channel][$intvalue][] = array("from"=>mb_strlen($takipiText, 'utf-8'), "text"=>$token->orth, "lemma" => $lemma);
                            }
                            // Jeśli jest to pierwszy token z danym identyfikatorem annotacji w kanale(typie) w zdaniu
                            else if (!array_key_exists($intvalue, $annotationMap[$sentenceNum][$channel])){
                                // Ostatnio odwiedzona annotacja
                                $annotationMap[$sentenceNum][$channel]['lastval']=$intvalue;
                                // Informacje o annotacji
                                $annotationMap[$sentenceNum][$channel][$intvalue][] = array("from"=>mb_strlen($takipiText, 'utf-8'), "text"=>$token->orth, "lemma" => $lemma);
                            }
                            // Jeśli jest to annotacja o identyfikatorze spotkanym wcześniej dla danego kanały(typu) w bieżącym zdaniu - część większej annotacji
                            else if (array_key_exists($channel, $annotationMap[$sentenceNum]) && array_key_exists($intvalue, $annotationMap[$sentenceNum][$channel])){
                                // Ostatnio odwiedzona annotacja w bieżącym kanale
                                $lastVal = $annotationMap[$sentenceNum][$channel]['lastval'];
                                // Jeśli ostatnio odwiedzona annotacja jest taka sama - mamy ciągłą annotację na kilku kolejnych tokenach
                                if ($intvalue == $lastVal){
                                    // Ostatnia annotacja
                                    $lastElem = array_pop($annotationMap[$sentenceNum][$channel][$lastVal]);
                                    // Dołącz tekst bieżącego tokena do tekstu całej annotacji
                                    if ($token->ns) {
                                        $lastElem["text"].=$token->orth;
                                    }
                                    else {
                                        $lastElem["text"].= " ".$token->orth;
                                    }
                                    array_push($annotationMap[$sentenceNum][$channel][$lastVal], $lastElem);
                                }
                                // Jeśli ostatnio odwiedzona annotacja jest inna - dołącz jako osobny fragment
                                else{
                                    array_push($annotationMap[$sentenceNum][$channel][$intvalue], array("from"=>mb_strlen($takipiText, 'utf-8'), "text"=>$token->orth, "lemma" => $lemma));
                                }
                                $annotationMap[$sentenceNum][$channel]['lastval']=$intvalue;
                            }
                        }
                        // Jeśli identyfikator nie jest dodatni - dla danego tokena w bieżącym kanale(typie)
                        // nie ma annotacji - zaznaczamy, że w ostatnim tokenie nie było annotacji w tym kanale(typie)
                        else {
                            if (array_key_exists($channel, $annotationMap[$sentenceNum])){
                                $annotationMap[$sentenceNum][$channel]['lastval']=0;
                            }
                        }
                    }
                    $takipiText .= $token->orth;
                }
                $sentenceNum++;
            }
        }
        $this->annotationMap = $annotationMap;
    }

    function importAnnotations(){
        $annId = array();
        foreach ($this->annotationMap as $sentence){
            foreach ($sentence as $channelId=>$channel){
                foreach ($channel as $annotations){
                    if (is_array($annotations)){
                        foreach ($annotations as $annotation){
                            $raoIndex = DbAnnotation::saveAnnotation($this->document_id, $channelId, $annotation['from'], $annotation['text'], $this->user_id, $this->stage, $this->source, $this->annotation_set_id, $this->ignore_duplicates, $this->ignore_unknown_types);
                            if($raoIndex['error']){
                                foreach($annId as $annotation_id){
                                    DbAnnotation::deleteReportAnnotation($this->document_id, $annotation_id);
                                }
                                return $raoIndex;
                            } else{
                                $annId[] = $raoIndex;
                            }
                        }
                    }
                }
            }
        }
        return "ok";
    }
}
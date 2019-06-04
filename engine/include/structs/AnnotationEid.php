<?php

class AnnotationEid{

    var $text = null;
    var $lemma = null;
    var $type = null;
    var $eid = null;

    function __construct($text, $lemma, $type, $eid){
        $this->text = $text;
        $this->lemma = $lemma;
        $this->type = $type;
        $this->eid = $eid;
    }

    public function getText(){
        return $this->text;
    }

    public function getLemma(){
        return $this->lemma;
    }

    public function getType(){
        return $this->type;
    }

    public function getEid(){
        return $this->eid;
    }

}
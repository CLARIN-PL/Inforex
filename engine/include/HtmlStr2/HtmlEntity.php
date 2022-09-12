<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class HtmlEntity extends HtmlChar {

    public function __construct($c){
        $len = strlen($c);
        if( ($len>0) && $c[0]=='&' && $c[$len-1]==';') {
            $this->c = substr($c,1,$len-2);
        } else {
            throw new Exception("Improper string '".$c."' to HtmlEntity object creation");
        }
    }

    public function toString(){
        return '&'.$this->c.';';
    }
}

?>

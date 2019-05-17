<?php


class CliAction{

    var $opt = null;

    function __construct(){
        $this->opt = $this->getCliopt();
    }

    function printHelp(){
        $this->opt->printHelp();
    }

}
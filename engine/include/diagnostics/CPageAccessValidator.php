<?php

class PageAccessValidator{

    var $enginePath;
    var $items = array();
    var $type;

    function __construct($enginePath, $type){
        $this->enginePath = $enginePath;
        $this->type = $type;
    }

    function process(){
        foreach (scandir($this->enginePath . DIRECTORY_SEPARATOR . $this->type) as $path){
            if (substr($path, 0, strlen($this->type)+1) == "{$this->type}_") {
                $this->analyze($path);
            }
        }
    }

    function analyze($ajaxFilename){
        //global $config;
        $path = $this->type . DIRECTORY_SEPARATOR . $ajaxFilename;
        require_once($path);
        $className = ucfirst(substr($ajaxFilename, 0, strlen($ajaxFilename)-4));
        $ajax = new $className();
        //$any = $ajax->checkPermission() ? "true" : "-";
        //$loggedin = $ajax->checkPermission() ? "true" : "-";
        //$isSecure = $ajax->isSecure ? "-" : "PUBLIC";

        $body = "";
        $reflector = new ReflectionClass($className);
        $func = $reflector->getMethod('checkPermission');
        if ($func->getDeclaringClass()->getName() == get_class($ajax)) {
            $start_line = $func->getStartLine() - 1; // it's actually - 1, otherwise you wont get the function() block
            $end_line = $func->getEndLine();
            $length = $end_line - $start_line;
            $source = file($this->enginePath . DIRECTORY_SEPARATOR . $path);
            $body = implode("", array_slice($source, $start_line, $length));
        }

        $item = new PageAccessValidatorItem();
        $item->setAnySystemRole($ajax->anySystemRole);
        $item->setAnyCorpusRole($ajax->anyCorpusRole);
        $item->setClassName($className);
        $item->setCheckPermissionBody($body);
        $item->setDescription($ajax->getDescription());
        $item->setName($ajax->getName());
        $item->setParentClassName($reflector->getParentClass()->getName());

        $this->items[] = $item;
    }
}
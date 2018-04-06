<?php

class PageAccessValidatorItem{

    var $className;
    var $checkPermissionBody;
    var $anySystemRole;
    var $anyCorpusRole;
    var $name;
    var $description;

    function setAnySystemRole($roles){
        $this->anySystemRole = $roles;
    }

    function setAnyCorpusRole($roles){
        $this->anyCorpusRole = $roles;
    }

    function setClassName($className){
        $this->className = $className;
    }

    function setCheckPermissionBody($checkPermissionBody){
        $this->checkPermissionBody = $checkPermissionBody;
    }

    function setName($name){
        $this->name = $name;
    }

    function setDescription($description){
        $this->description = $description;
    }

}
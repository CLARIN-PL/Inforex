<?php

class PageAccessValidatorItem{

    var $className;
    var $parentClassName;
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

    function setParentClassName($className){
        $this->parentClassName = $className;
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
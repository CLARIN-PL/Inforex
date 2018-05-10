<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

/**
 * Class CPagePublic represent any page which can be accessed only by system administrator.
 */
class CPageAdministration extends CPage {

    function __construct(){
        parent::__construct();
        $this->set("pages", $this->getAdministrationPages());
    }

    function getAdministrationPages(){
        $pages = array();
        $pages[] = array("name" => "administration_users", "title"=>"Users");
        $pages[] = array("name" => "administration_annotation_schema", "title"=>"Annotation schema");
        $pages[] = array("name" => "administration_annotation_shared_attributes", "title"=>"Annotation shared attributes");
        $pages[] = array("name" => "administration_relation_schema", "title"=>"Relation schema");
        $pages[] = array("name" => "administration_frame_schema", "title"=>"Frame schema");
        $pages[] = array("name" => "administration_wsd_schema", "title"=>"WSD schema");
        $pages[] = array("name" => "administration_activities", "title"=>"User activities — registered");
        $pages[] = array("name" => "administration_activities_anonymous", "title"=>"User activities — anonymous");
        $pages[] = array("name" => "administration_diagnostic_access", "title"=>"Diagnostic — ajax and page access");
        $pages[] = array("name" => "administration_diagnostic_ajax", "title"=>"Diagnostic — ajax usage");
        $pages[] = array("name" => "administration_diagnostic_db", "title"=>"Diagnostic — database");
        return $pages;
    }

}
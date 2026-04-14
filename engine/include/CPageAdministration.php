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
        $pages[] = array("name" => "administration_users", "title"=>"Users", "icon"=>"fa-users");
        $pages[] = array("name" => "administration_annotation_schema", "title"=>"Annotation schema", "icon"=>"fa-tags");
        $pages[] = array("name" => "administration_annotation_shared_attributes", "title"=>"Annotation shared attributes", "icon"=>"fa-share-alt");
        $pages[] = array("name" => "administration_relation_schema", "title"=>"Relation schema", "icon"=>"fa-random");
        $pages[] = array("name" => "administration_frame_schema", "title"=>"Frame schema", "icon"=>"fa-object-group");
        $pages[] = array("name" => "administration_wsd_schema", "title"=>"WSD schema", "icon"=>"fa-sitemap");
        $pages[] = array("name" => "administration_activities", "title"=>"User activities — registered", "icon"=>"fa-line-chart");
        $pages[] = array("name" => "administration_activities_anonymous", "title"=>"User activities — anonymous", "icon"=>"fa-user-secret");
        $pages[] = array("name" => "administration_diagnostic_access", "title"=>"Diagnostic — ajax and page access", "icon"=>"fa-stethoscope");
        $pages[] = array("name" => "administration_diagnostic_ajax", "title"=>"Diagnostic — ajax usage", "icon"=>"fa-exchange");
        $pages[] = array("name" => "administration_diagnostic_db", "title"=>"Diagnostic — database", "icon"=>"fa-database");
        return $pages;
    }

}

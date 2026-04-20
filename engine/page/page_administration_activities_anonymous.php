<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Page_administration_activities_anonymous extends CPageAdministration {

    function execute(){
        $this->set("activities_years", array());
        $this->set("activities_years_months", array());
    }
}

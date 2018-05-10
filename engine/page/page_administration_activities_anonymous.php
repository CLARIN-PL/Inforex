<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Page_administration_activities_anonymous extends CPageAdministration {

    function execute(){
        $activities_years = DbUser::getAnonymousActivitiesByYear(true);
        $activities_years_months = DbUser::getAnonymousActivitiesByYearMonth();

        $this->set("activities_years", $activities_years);
        $this->set("activities_years_months", $activities_years_months);
    }
}
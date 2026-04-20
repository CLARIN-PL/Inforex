/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(document).ready(function(){
    $("#administration-diagnostic-access-filter").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        var visibleRows = 0;

        $("#administration-diagnostic-access-table tbody tr").filter(function() {
            var isVisible = $(this).text().toLowerCase().indexOf(value) > -1;
            $(this).toggle(isVisible);

            if (isVisible) {
                visibleRows++;
            }
        });

        $("#administration-diagnostic-access-visible-count").text(visibleRows);
    });

});

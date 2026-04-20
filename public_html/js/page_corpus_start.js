/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var documentFlagsPage = 1;
var documentFlagsPageSize = 10;
var corpusDashboardChartColors = ["#1f6f93", "#7fb8cf", "#d7a23a", "#6f8f4d", "#b84a4a", "#607d8b"];

google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

$(function(){
    renderDocumentFlagsPage();

    $("#document_flags_filter").on("keyup", function() {
        documentFlagsPage = 1;
        renderDocumentFlagsPage();
    });
});


function drawChart() {
	// Zmienna chartDataSubcorpora jest generowana w szablonie tpl
      if (typeof chartDataSubcorpora === "undefined" || !$("#piechart").length) {
        return;
      }

      var gdata = google.visualization.arrayToDataTable(chartDataSubcorpora);
      var $chart = $("#piechart");
      var chartWidth = Math.max(280, $chart.width());

      var options = {
        backgroundColor: "transparent",
        chartArea: {left: 8, top: 12, width: "94%", height: "88%"},
        colors: corpusDashboardChartColors,
        fontName: "system-ui",
        height: 320,
        legend: {position: "none"},
        pieHole: 0.45,
        width: chartWidth
      };

      var chart = new google.visualization.PieChart(document.getElementById('piechart'));

      chart.draw(gdata, options);
      drawCorpusDashboardLegend();
    	
}

function drawCorpusDashboardLegend() {
    var $legend = $("#piechart_legend");
    var html = "";

    if (!$legend.length || typeof chartDataSubcorpora === "undefined") {
        return;
    }

    for (var i = 1; i < chartDataSubcorpora.length; i++) {
        html += '<span class="corpus-dashboard-chart-legend-item">' +
            '<span class="corpus-dashboard-chart-legend-color" style="background-color: ' + corpusDashboardChartColors[(i - 1) % corpusDashboardChartColors.length] + '"></span>' +
            '<span class="corpus-dashboard-chart-legend-label">' + escapeCorpusDashboardLegendText(chartDataSubcorpora[i][0]) + '</span>' +
            '<span class="corpus-dashboard-chart-legend-value">' + escapeCorpusDashboardLegendText(chartDataSubcorpora[i][1]) + '</span>' +
        '</span>';
    }

    $legend.html(html);
}

function escapeCorpusDashboardLegendText(value) {
    return $("<div>").text(value == null ? "" : value).html();
}

$(window).on("resize", function() {
    clearTimeout(window.corpusDashboardChartResizeTimer);
    window.corpusDashboardChartResizeTimer = setTimeout(drawChart, 150);
});

function drawChartFlags(data, title){
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);

    function drawChart() {
	    var gdata = google.visualization.arrayToDataTable(data);
	    var options = {
	      width: 550,
	      height: 600,
	      legend: { position: 'top', maxLines: 3 },
	      bars : 'horizontal',
	      isStacked: true,
	    };
	    var chart = new google.visualization.BarChart(document.getElementById("columnchart_values"));
	    chart.draw(gdata, options);
    }    
}

function renderDocumentFlagsPage() {
    var $rows = $(".corpus-dashboard-table tbody tr").not(":has(.corpus-dashboard-empty-row)");
    var $info = $("#document_flags_pagination_info");
    var $controls = $("#document_flags_pagination_controls");
    var filter = ($("#document_flags_filter").val() || "").toLowerCase();

    if (!$rows.length || !$controls.length) {
        $("#document_flags_pagination").hide();
        return;
    }

    var $filteredRows = $rows.filter(function() {
        return filter == "" || getDocumentFlagsFilterText($(this)).indexOf(filter) >= 0;
    });
    var totalRows = $filteredRows.length;
    var totalPages = Math.max(1, Math.ceil(totalRows / documentFlagsPageSize));
    var start = (documentFlagsPage - 1) * documentFlagsPageSize;
    var end = start + documentFlagsPageSize;

    documentFlagsPage = Math.min(documentFlagsPage, totalPages);
    start = (documentFlagsPage - 1) * documentFlagsPageSize;
    end = start + documentFlagsPageSize;

    $rows.hide();
    $filteredRows.slice(start, end).show();

    if (totalRows === 0) {
        $info.text("No matching flags");
    } else {
        $info.text("Showing " + (start + 1) + " to " + Math.min(end, totalRows) + " of " + totalRows + " flags");
    }
    renderDocumentFlagsPaginationControls(totalPages);
}

function getDocumentFlagsFilterText($row) {
    var flag = $row.find("td:nth-child(1)").text();
    var short = $row.find("td:nth-child(2)").text();
    var description = $row.find("td:nth-child(10)").text();

    return (flag + " " + short + " " + description).toLowerCase();
}

function renderDocumentFlagsPaginationControls(totalPages) {
    var $controls = $("#document_flags_pagination_controls");
    var html = "";
    var pageWindow = 5;
    var firstPage = Math.max(1, documentFlagsPage - Math.floor(pageWindow / 2));
    var lastPage = Math.min(totalPages, firstPage + pageWindow - 1);

    firstPage = Math.max(1, lastPage - pageWindow + 1);

    html += buildDocumentFlagsPageButton("Previous", documentFlagsPage - 1, documentFlagsPage == 1);

    for (var page = firstPage; page <= lastPage; page++) {
        html += buildDocumentFlagsPageButton(page, page, false, page == documentFlagsPage);
    }

    html += buildDocumentFlagsPageButton("Next", documentFlagsPage + 1, documentFlagsPage == totalPages);
    $controls.html(html);

    $controls.find("button").click(function() {
        if ($(this).prop("disabled")) {
            return;
        }
        documentFlagsPage = parseInt($(this).attr("data-page"), 10);
        renderDocumentFlagsPage();
    });
}

function buildDocumentFlagsPageButton(label, page, disabled, active) {
    return '<button type="button" class="corpus-dashboard-page-button' + (active ? ' active' : '') + '" data-page="' + page + '"' + (disabled ? ' disabled="disabled"' : '') + '>' + label + '</button>';
}

/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

function drawChartSubcorpora(data, title){
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);

    function drawChart() {

      var gdata = google.visualization.arrayToDataTable(data);

      var options = {
        title: title
      };

      var chart = new google.visualization.PieChart(document.getElementById('piechart'));

      chart.draw(gdata, options);
    }	
}

function drawChartFlags(data, title){
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);

    function drawChart() {
	    var gdata = google.visualization.arrayToDataTable(data);
	    var options = {
	      width: 900,
	      height: 600,
	      legend: { position: 'top', maxLines: 3 },
	      bars : 'horizontal',
	      isStacked: true,
	    };
	    var chart = new google.visualization.BarChart(document.getElementById("columnchart_values"));
	    chart.draw(gdata, options);
    }    
}

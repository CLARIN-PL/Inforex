/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

$(function(){
});


function drawChart() {
	// Zmienna chartDataSubcorpora jest generowana w szablonie tpl
      var gdata = google.visualization.arrayToDataTable(chartDataSubcorpora);

      var options = {
        title: "Corpus structure",
	width: 460,
	height: 400,
	chartArea:{left:20,top:20,width:450,height:"100%"}
      };

      var chart = new google.visualization.PieChart(document.getElementById('piechart'));

      chart.draw(gdata, options);
    	
}

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

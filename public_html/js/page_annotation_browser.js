/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){

    // Bieżąca wysokość okna
    var windowH = window.innerHeight;
    // Ustaw wysokość panelu filtrów
    //resizeFilterPanel(windowH - headerH - footerH);
    // Przyjęta do obliczeń wysokość wiersza
    var rowH = $("#table-annotations tr:last").outerHeight() + 2;
    rowH = Math.max(rowH, 16);
    // Wysokość FlexiGrida
    //var flexiHeight = windowH - headerH - 2*paginateH - footerH - 30;
    var flexiTotalHeight = calculateTableHeight();
    var flexiContentHeight = flexiTotalHeight - 70;
    // Liczba wyświetlanych wierszy
    var elems = Math.floor((flexiContentHeight - 15) / rowH);
    // Wyświetl obliczoną liczbę wierszy, ale nie mniej niż 10
    var tableElementsPerPage = Math.max(10, elems); 	
	
    var init_from = 0;

    var colModel = [
            {display: "Document", name : "report_id", width : 60, sortable : false, align: 'center'},
            {display: "Left", name : "left", width : 400, sortable : false, align: 'right'},
            {display: "Annotation", name : "annotation", width : 140, sortable : false, align: 'center'},
            {display: "Right", name : "right", width : 400, sortable : false, align: 'left'},
    ];      

    var annotation_type_id = $.url(window.location.href).param("annotation_type_id");
    var corpus_id = $.url(window.location.href).param("corpus");
    
    var flex = $("#table-annotations").flexigrid({
        url: 'index.php',
        params: [
            { "name":"corpus_id", "value": corpus_id },
            { "name":"annotation_type_id", "value": annotation_type_id },
            { "name":"ajax", "value": "annotation_browser" },
        ],
        dataType: 'json',
        colModel : colModel,
        colResize: false,
        sortname: "id",
        sortorder: "asc",
        usepager: true,
        title: false,
        useRp: false,
        rp: tableElementsPerPage,
        showTableToggleBtn: false,
        showToggleBtn: false,
        width: "100%",
        height: flexiContentHeight,
        newp: 1,
        resizable: false
    });    
    
    $("#annotation_types").css("height", (flexiTotalHeight - 16) + "px");
});

/**
 * Calculate flexigrid table height.
 */
function calculateTableHeight(){
	var bodyHeight = $("body").outerHeight();
	var tableHeight = $(".flexigrid").outerHeight();
	var boilerplateHeight = bodyHeight - tableHeight;
	var windowHeight = $(window).height();
	return windowHeight - boilerplateHeight;
}

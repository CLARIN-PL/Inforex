/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){

    function adjustContextsGridHeight() {
        var $grid = $(".annotation-contexts-table-shell .flexigrid");
        var $body = $grid.find("div.bDiv");
        var $rows = $body.find("tbody tr:visible");
        var visibleRows = $rows.length;
        var rowHeight = Math.max(18, Math.ceil($rows.first().outerHeight() || 22));
        var bodyHeight = (Math.max(1, Math.min(tableElementsPerPage, visibleRows || tableElementsPerPage)) * rowHeight) + 2;

        $body.css("height", bodyHeight + "px");
    }

	var content_height = 
		$(window).height() - $("body").outerHeight(true) + $("#page_content").height() - 10 - $("#export").outerHeight(true);
	
    // Bieżąca wysokość okna
    var windowH = window.innerHeight;
    // Ustaw wysokość panelu filtrów
    //resizeFilterPanel(windowH - headerH - footerH);
    // Przyjęta do obliczeń wysokość wiersza
    var rowH = $("#table-annotations tr:last").outerHeight() + 5;
    rowH = Math.max(rowH, 16);
    // Wysokość FlexiGrida
    var flexiTotalHeight = content_height - 25 - 70;
    var flexiContentHeight = flexiTotalHeight - 70;
    // Liczba wyświetlanych wierszy
    var elems = Math.floor((flexiContentHeight - 15) / rowH);
    var tableElementsPerPage = 15;
    var flexiContentHeight = (rowH * tableElementsPerPage) + 12;
	
    var init_from = 0;

    var colModel = [
            {display: "Document", name : "report_id", width : 48, sortable : false, align: 'center'},
            {display: "Left", name : "left", width : 380, sortable : false, align: 'right'},
            {display: "Annotation", name : "annotation", width : 96, sortable : false, align: 'center'},
            {display: "Right", name : "right", width : 380, sortable : false, align: 'left'},
            {display: "Source", name : "source", width : 56, sortable : false, align: 'center'},
            {display: "Stage", name : "stage", width : 56, sortable : false, align: 'center'},
    ];      

    var annotation_type_id = $.url(window.location.href).param("annotation_type_id");
    var annotation_orth = $.url(window.location.href).param("annotation_orth");
    var annotation_lemma = $.url(window.location.href).param("annotation_lemma");
    var annotation_stage = $.url(window.location.href).param("annotation_stage");
    var corpus_id = $.url(window.location.href).param("corpus");
    
    var flex = $("#table-annotations").flexigrid({
        url: 'index.php',
        params: [
            { "name":"corpus_id", "value": corpus_id },
            { "name":"annotation_type_id", "value": annotation_type_id },
            { "name":"annotation_orth", "value": annotation_orth ? annotation_orth : "" },
            { "name":"annotation_lemma", "value": annotation_lemma ? annotation_lemma : ""  },
            { "name":"annotation_stage", "value": annotation_stage ? annotation_stage : ""  },
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
        resizable: false,
        onSuccess: function () {
            adjustContextsGridHeight();
        }
    });

    setTimeout(adjustContextsGridHeight, 0);

    $("#export_all").click(function(){
    	window.location.href="index.php?page=corpus_annotation_contexts_export&corpus=" + corpus_id;
    });
    $("#export_selected").click(function(){
    	window.location.href=window.location.href.replace("page=corpus_annotation_contexts", "page=corpus_annotation_contexts_export");
    });

    if ( $("#annotation-types tr.selected") ) {
        $('#annotation-types').animate({
            scrollTop: $("#annotation-types tr.selected").offset().top - $("#annotation-types").offset().top
        }, 1000);
    }

    if ( $("#annotation_orths tr.selected").length > 0 ) {
        $('#annotation_orths').animate({
            scrollTop: $("#annotation_orths tr.selected").offset().top - $("#annotation_orths").offset().top
        }, 1000);
    }

    if ( $("#annotation_lemmas tr.selected").length > 0 ) {
        $('#annotation_lemmas').animate({
            scrollTop: $("#annotation_lemmas tr.selected").offset().top - $("#annotation_lemmas").offset().top
        }, 1000);
    }

});

/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var vars = [];

function subsetRow(name, subset) {
    var subsetRow = '<tr class="subsetGroup expandable" name="' + subset['id'] + '">';
    subsetRow += '<td class="empty"></td>';
    subsetRow += '<td colspan="3">' + name + '</td>';
    subsetRow += '<td style="text-align:right">' + subset["unique"] + '</td>';
    subsetRow += '<td style="text-align:right">' + subset["count"] + '</td>';
    subsetRow += '</tr>';
    return subsetRow;
}

function typesRow(name, type) {
    var typesRow = '<tr class="annotation_type">';
    typesRow += '<td colspan="2" class="empty"></td>';
    typesRow += '<td>';
    style = "";

    if (type['count'] > 0)
        typesRow += '<a href="." class="toggle_simple" label="' + type['name'] + '"><b>' + type['name'] + '</b></a>';
    else {
        typesRow += '<span style="color: grey">' + type['name'] + '</span>';
        style = "; color: grey";
    }

    typesRow += '</td>';
    typesRow += '<td style="text-align:right' + style + '">' + type['docs'] + '</td>';
    typesRow += '<td style="text-align:right' + style + '">' + type['unique'] + '</td>';
    typesRow += '<td style="text-align:right' + style + '">' + type['count'] + '</td>';
    typesRow += '</tr>';
    return typesRow;
}

function tagsElement(name, tag) {
    var element = '<li class="annotation_item">';
    element += '<span style="float: right;">' + tag['count'] + '</span>';
    element += '<span style="margin-right: 50px">' + tag['text'] + '</span>';
    element += '<div class="annotationItemLinks"></div>';
    element += '</li>';

    return element;
}

function displayAnnotationSubsets(data, currentRow) {
    var rows = "";
    $.each(data, function (name, subset) {
        rows += subsetRow(name, subset);
    });
    currentRow.nextUntil(".setGroup").remove();
    currentRow.after(rows);

    currentRow.nextUntil(".setGroup", "tr").click(function () {
        var firstNonEmpty = $(this).children().not('.empty').first();
        if ($(this).hasClass("showItem")) {
            if (!firstNonEmpty.hasClass("loading")) {
                $(this).removeClass("showItem");
                $(this).nextUntil(".subsetGroup,.setGroup").remove();
            }
        } else {
            var url = $.url(window.location.href);
            var corpus_id = url.param('corpus');
            var subcorpus = url.param('subcorpus');
            var status = url.param('status');
            var subset_id = parseInt($(this).attr('name'));
            loadAnnotationTypes(corpus_id, subset_id, status, subcorpus, $(this), firstNonEmpty);
            $(this).addClass("showItem");
        }
    });
}

function displayAnnotationTypes(data, currentRow) {
    var rows = "";
    $.each(data, function (name, subset) {
        rows += typesRow(name, subset);
    });

    currentRow.nextUntil(".subsetGroup,.setGroup").remove();
    currentRow.after(rows);

    currentRow.nextUntil(".setGroup, .setSubgroup", "tr").each(function () {
        $(this).find("a").click(function () {
            if ($(this).hasClass("showItem")) {
                if (!$(this).parent().hasClass("loading")) {
                    $(this).removeClass("showItem");
                    $(this).parent().parent().nextUntil(".subsetGroup,.setGroup,.annotation_type").remove();
                }
            } else {
                var url = $.url(window.location.href);
                var corpus_id = url.param('corpus');
                var subcorpus = url.param('subcorpus');
                var status = url.param('status');
                var annotation_type = $(this).attr("label");
                loadAnnotationTags(corpus_id, annotation_type, status, subcorpus, $(this).parent().parent(), $(this).parent());
                $(this).addClass("showItem");
            }
            return false;
        })
    });
}

function displayAnnotationTags(data, currentRow, annotation_type) {
    var row = '<tr class="annotation_type_' + annotation_type + ' annotation_type_names expandable">';
    row += '<td colspan="2" class="empty2"></td>';
    row += '<td colspan="4">';
    row += '<ol>';

    $.each(data, function (name, tag) {
        row += tagsElement(name, tag);
    });

    row += '</ol>';
    row += '</td>';
    row += '</tr>';

    currentRow.nextUntil(".subsetGroup,.setGroup,.annotation_type").remove();
    currentRow.after(row);

    currentRow.next("tr").find("li.annotation_item").click(function () {
        var $links = $(this).children(".annotationItemLinks");
        if ($links.hasClass("showItem")) {
            $links.removeClass("showItem").empty();
        } else {
            corpusId = vars['corpus'];
            annotationText = $(this).children("span:last").text();
            annotationType = $(this).parents("tr").prev().find("a.toggle_simple").text();
            $links.addClass("showItem");
            var params = {
                corpus_id: corpusId,
                type: annotationType,
                text: annotationText
            };
            var success = function (data) {
                displayAnnotationLinks(data, $links);
            }
            doAjax('annmap_get_report_links', params, success, null, null, null);

        }
    });
}

function displayAnnotationLinks(data, links) {
    if (links.hasClass("showItem")) {
        links.empty();
        var str = "<ul>";
        $.each(data, function (index, value) {
            str += '<li><a href="index.php?page=report&corpus=' + corpusId + '&id=' + value.id + '" target="_blank">' + value.title + '</li>';
        });
        str += "<ul>";
        links.append(str);
    }
}

function loadAnnotationSubset(corpus_id, set_id, status, subcorpus, currentRow, cell) {
    var params = getFilterData();
    params.set_id = set_id;

    var success = function (data) {
        displayAnnotationSubsets(data, currentRow)
    };
    doAjax('annmap_load_subset', params, success, null, null, cell);
}

function getFilterData() {
    var url = $.url(window.location.href);
    var corpus_id = url.param('corpus');
    var subcorpus = url.param('subcorpus');
    var status = url.param('status');
    var flag = url.param('flag');
    var flag_status = url.param('flag_status');
    var stage = url.param('stage');
    var user_id = url.param('user_id');

    var params = {
        corpus_id: corpus_id,
        subcorpus: subcorpus,
        status: status,
        flag: flag,
        flag_status: flag_status,
        stage: stage,
        user_id: user_id
    };

    return params;
}

function loadAnnotationTypes(corpus_id, subset_id, status, subcorpus, currentRow, cell) {
    var params = {
        corpus_id: corpus_id,
        status: status,
        subset_id: subset_id,
        subcorpus: subcorpus
    };
    var success = function (data) {
        displayAnnotationTypes(data, currentRow);
    };
    doAjax('annmap_load_type', params, success, null, null, cell);
}

function loadAnnotationTags(corpus_id, annotation_type, status, subcorpus, currentRow, cell) {
    var params = {
        corpus_id: corpus_id,
        annotation_type: annotation_type,
        status: status,
        subcorpus: subcorpus
    };
    var success = function (data) {
        displayAnnotationTags(data, currentRow, annotation_type);
    };
    doAjax("annmap_load_tags", params, success, null, null, cell);
}

function updateUrlParams(metadata, value) {
    var flag_val = $(".corpus_flag_id").val();
    var flag_status = $(".flag_type").val();
    var status = $(".selected_status_id").attr('id');
    var stage = $(".annotation_stage").val();
    var user = $(".annotation_user").val();

    let url = [];
    url.push("index.php?page=corpus_annotation_statistics&corpus=" + corpus_id);
    url.push("&status=" + status);

    if(metadata != null && value!=null){
        url.push("&metadata=" + metadata);
        url.push("&value=" + value);
    }

    if (flag_status !== "-" && flag_val !== "-") {
        url.push("&flag=" + flag_val);
        url.push("&flag_status=" + flag_status);
    }

    if (stage !== "-") {
        url.push("&stage=" + stage);
    }

    if (user !== "-") {
        url.push("&user_id=" + user);
    }
    window.location.href = url.join("");
}

$(function () {
    var url = $.url(window.location.href);
    var corpus_id = url.param('corpus');
    var subcorpus = url.param('subcorpus');
    vars = [];
    var hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }

    $("tr.setGroup").click(function () {
        var firstNonEmpty = $(this).children().not('.empty').first();
        if ($(this).hasClass("showItem")) {
            if (!firstNonEmpty.hasClass("loading")) {
                $(this).removeClass("showItem").nextUntil(".setGroup").remove();
            }
        } else {
            var status = url.param('status');
            var set_id = parseInt($(this).attr('name'));
            loadAnnotationSubset(corpus_id, set_id, status, subcorpus, $(this), firstNonEmpty);
            $(this).addClass("showItem");
        }
    });

    $(".corpus_flag_id, .flag_type").change(function () {
        let flag_val = $(".corpus_flag_id").val();
        let flag_status = $(".flag_type").val();
        if (flag_status !== "-" && flag_val !== "-") {
            updateUrlParams();
        }
    });

    $(".annotation_stage").change(function () {
        updateUrlParams();
    });

    $(".annotation_user").change(function () {
        updateUrlParams();
    });

    $(".cancel_flags").click(function () {
        $(".corpus_flag_id").val("-");
        $(".flag_type").val("-");
        updateUrlParams();
    });

    $(".cancel_stage").click(function () {
        $(".annotation_stage").val("-");
        updateUrlParams();
    });

    $(".cancel_user").click(function () {
        $(".annotation_user").val("-");
        updateUrlParams();
    });

    $(".status_link").click(function (event) {
        event.preventDefault();
        $(".selected_status_id").attr('id', event.target.id);
        updateUrlParams();
    });

    $(".metadata_link").click(function (event) {
        event.preventDefault();
        let meta = event.target.id;
        let value = event.target.text === "all" ? 0 : event.target.text;
        updateUrlParams(meta,value);
    });

    $("#copy_url").click(function () {
        var copy_url = generateCopyURL();
        $("#url_input").text(copy_url);
    });

    $("#copy_clipboard").click(function () {
        var text = $("#url_input");
        text.select();
        document.execCommand("Copy");
    });

    $("#reset_filters").click(function () {
        window.location.href = "index.php?page=corpus_annotation_statistics&corpus=" + corpus_id + "&status=0";
    });
});

function generateCopyURL() {

    var base_url = $(location).attr('host') + window.location.pathname;
    var url = base_url + "?page=corpus_annotation_statistics&corpus=" + corpus_id + "&use_url=1";

    var flag_val = $(".corpus_flag_id").val();
    var flag_status = $(".flag_type").val();
    var stage = $(".annotation_stage").val();
    var user = $(".annotation_user").val();

    if (flag_val !== "-" && flag_status !== "-") {
        url += "&flag=" + flag_val + "&flag_status=" + flag_status;
    }
    if (stage !== '-') {
        url += "&stage=" + stage;
    }

    if (user !== '-') {
        url += "&user=" + user;
    }

    var status = $(".selected_status_id").attr('id');
    if (status !== 0) {
        url += "&status=" + status;
    }

    $(".selected_metadata").each(function (index) {
        var metadata_field = $(this).attr('id');
        var metadata_value = $(this).text();
        url += "&metadata_" + index + "=" + metadata_field + "&value_" + index + "=" + metadata_value;
    });

    return encodeURI(url);
}
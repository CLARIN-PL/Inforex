{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<table class = "admin_tables">
    <tr>
        <td style="width: 30%; vertical-align: top; padding-right: 10px; " id="annotationSetsContainer" class="tableContainer">
                <div class="panel panel-primary scrollingWrapper" style="margin: 5px">
                    <div class="panel-heading">Sets</div>
                    <div class="panel-body scrolling">
                        <div class="tableContent bigTableContent    ">
                            <table id = "annotationSetsTable" class="tablesorter table table-striped" id="public" cellspacing="1">
                                <thead>
                                <tr>
                                    <th style = "width: 10%">id</th>
                                    <th>name</th>
                                </tr>
                                </thead>
                                <tbody>
                                {foreach from=$annotationSets item=set}
                                    <tr visibility = "{$set.public}">
                                        <td class = "column_id">{$set.id}</td>
                                        <td>{$set.description}</td>
                                    </tr>
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                        <div class="tableOptions" element="annotation_set" style = "margin-top: 10px; text-align:center;">
                            <button type = "button" class = "btn btn-primary create adminPanelButton">Create</button>
                            <button style = "display: none;" type = "button" class = "btn btn-primary edit adminPanelButton">Edit</button>
                            <button style = "display: none;" type = "button" class = "btn btn-primary delete adminPanelButton">Delete</button>
                        </div>
                    </div>
                </div>
        </td>
        <td>
            <table style="width: 100%">
                <tr>
                    <td style="width: 50%; vertical-align: top">
                        <div id="annotationSubsetsContainer" class="tableContainer panel panel-primary scrollingWrapper" style="margin: 5px; display: none;">
                            <div class="panel-heading">Subsets</div>
                            <div class="panel-body scrolling">
                                <div class="tableContent">
                                    <table id="annotationSubsetsTable" class="tablesorter table table-striped" cellspacing="1">
                                        <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>name</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tableOptions" element="annotation_subset" parent="annotationSetsContainer" style = "margin-top: 10px; text-align:center;">
                                    <button style = "display: none;" type = "button" class = "btn btn-primary create adminPanelButton">Create</button>
                                    <button style = "display: none;" type = "button" class = "btn btn-primary edit adminPanelButton">Edit</button>
                                    <button style = "display: none;" type = "button" class = "btn btn-primary delete adminPanelButton">Delete</button>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="width: 50%; vertical-align: top">
                        <div id = "annotationTypesContainer" class="tableContainer panel panel-primary scrollingWrapper" style="margin: 5px; display: none;">
                            <div class="panel-heading">Categories</div>
                            <div class="panel-body scrolling">
                                <div class="tableContent">
                                    <table id="annotationTypesTable" class="tablesorter table table-striped" cellspacing="1">
                                        <thead>
                                        <tr>
                                            <th>name</th>
                                            <th title="short description">short</th>
                                            <th>description</th>
                                            <th>visibility</th>
                                            <th style="display:none">css</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tableOptions" element="annotation_type" parent="annotationSubsetsContainer" style = "margin-top: 10px; text-align:center;">
                                    <button style = "display: none;" type = "button" class = "btn btn-primary create adminPanelButton">Create</button>
                                    <button style = "display: none;" type = "button" class = "btn btn-primary edit adminPanelButton">Edit</button>
                                    <button style = "display: none;" type = "button" class = "btn btn-primary delete adminPanelButton">Delete</button>
                                </div>
                            </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; vertical-align: top">
                        <div id="annotationSetsCorporaContainer" class="tableContainer panel panel-primary scrollingWrapper" style="margin: 5px; display: none;">
                            <div class="panel-heading">The set is attached to the following corpora</div>
                            <div class="panel-body scrolling">
                                <div class="tableContent">
                                    <table id="annotationSetsCorporaTable" class="tablesorter table table-striped" cellspacing="1">
                                        <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>name</th>
                                            <th>description</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tableOptions" style="text-align:center; margin-top: 5px;">
                                    <button type = "button" class = "btn btn-primary move unassign"> >>> </button>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="width: 25%; vertical-align: top">
                        <div id="corpusContainer" class="tableContainer panel panel-primary scrollingWrapper" style="margin: 5px; display: none;">
                            <div class="panel-heading">Other corpora</div>
                            <div class="panel-body scrolling">
                                <div class="tableContent">
                                    <div class="tableContent">
                                        <table id="corpusTable" class="tablesorter table table-striped" cellspacing="1">
                                            <thead>
                                            <tr>
                                                <th>id</th>
                                                <th>name</th>
                                                <th>description</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tableOptions" style="text-align:center; margin-top: 5px;">
                                    <button type = "button" class = "btn btn-primary move assign"> <<< </button>
                                </div>
                            </div>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

<br style="clear: both"/>

{include file="inc_footer.tpl"}

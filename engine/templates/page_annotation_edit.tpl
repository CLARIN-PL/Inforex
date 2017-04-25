{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<div class="container-fluid admin_tables">
    <div class="row">
        <div class="col-md-4 tableContainer" id = "annotationSetsContainer" style="padding: 0">
            <div class="panel panel-primary scrollingWrapper" style="margin: 5px;">
                <div class="panel-heading">Annotation sets</div>
                <div class="tableContent panel-body scrolling" style="">
                    <table class="tablesorter table table-striped" id="annotationSetsTable" cellspacing="1">
                        <thead>
                        <tr>
                            <th style = "width: 10%">id</th>
                            <th>name</th>
                            <th>user</th>
                            <th>access</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$annotationSets item=set}
                            <tr visibility = "{$set.public}">
                                <td class = "column_id">{$set.id}</td>
                                <td>{$set.description}</td>
                                <td>{$set.screename}</td>
                                <td>{if $set.public == 1} public {else} private {/if}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer" element="annotation_set" >
                    <button type = "button" class = "btn btn-primary create ">Create</button>
                    <button style = "display: none;" type = "button" class = "btn btn-primary edit ">Edit</button>
                    <button style = "display: none;" type = "button" class = "btn btn-primary delete ">Delete</button>
                </div>
            </div>
        </div>

        <div class="col-md-4 scrollingWrapper" style="padding: 0">
            <div class="panel panel-primary tableContainer" id="annotationSubsetsContainer" style="margin: 5px; visibility: hidden;">
                <div class="panel-heading">Annotation subsets</div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
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
                </div>
                <div class="panel-footer" element="annotation_subset" parent="annotationSetsContainer">
                    <button type = "button" class = "btn btn-primary create adminPanelButton">Create</button>
                    <button style = "display: none;" type = "button" class = "btn btn-primary edit adminPanelButton">Edit</button>
                    <button style = "display: none;" type = "button" class = "btn btn-primary delete adminPanelButton">Delete</button>
                </div>
            </div>
            <div class="panel panel-primary tableContainer" id="annotationSetsCorporaContainer" style="margin: 5px; visibility: hidden;">
                <div class="panel-heading">The set is attached to the following corpora</div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
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
                </div>
                <div class="panel-footer">
                    <button type = "button" class = "btn btn-primary move unassign"> >>> </button>
                </div>
            </div>
        </div>

        <div class="col-md-4 scrollingWrapper" style="padding: 0">
            <div class="panel panel-primary tableContainer" id="annotationTypesContainer" style="margin: 5px; visibility: hidden;">
                <div class="panel-heading">Categories</div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
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
                </div>
                <div class="panel-footer" element="annotation_type" parent="annotationSubsetsContainer">
                    <button type = "button" class = "btn btn-primary create adminPanelButton">Create</button>
                    <button style = "display: none;" type = "button" class = "btn btn-primary edit adminPanelButton">Edit</button>
                    <button style = "display: none;" type = "button" class = "btn btn-primary delete adminPanelButton">Delete</button>
                </div>
            </div>
            <div class="panel panel-primary tableContainer" id="corpusContainer" style="margin: 5px; visibility: hidden;">
                <div class="panel-heading">Other corpora</div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
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
                <div class="panel-footer">
                    <button type = "button" class = "btn btn-primary move assign"> <<< </button>
                </div>
            </div>
        </div>
    </div>
</div>
{include file="inc_footer.tpl"}

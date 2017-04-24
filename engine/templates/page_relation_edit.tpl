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
        <div class="col-md-6 tableContainer" id = "annotationSetsContainer" style="padding: 0">
                <div class="panel panel-primary scrollingWrapper" style="margin: 5px;">
                    <div class="panel-heading">Annotation sets</div>
                    <div class="tableContent panel-body scrolling" style="">
                        <table class="tablesorter table table-striped" id="annotationSetsTable" cellspacing="1">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>description</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach from=$annotationSets item=set}
                                <tr>
                                    <td class = "column_id">{$set.id}</td>
                                    <td>{$set.description}</td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
        </div>
        <div class="col-md-6" style="padding: 0">
            <div class="panel panel-primary scrollingWrapper tableContainer" id="relationTypesContainer" style="margin: 5px; display: none;">
                <div class="panel-heading">Relation types</div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
                        <table id="relationTypesTable" class="tablesorter table table-striped" cellspacing="1">
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
                <div class="panel-footer" element="relation_type" parent="annotationSetsContainer">
                    <button type = "button" class = "btn btn-primary create adminPanelButton">Create</button>
                    <button style = "display: none;" type = "button" class = "btn btn-primary edit adminPanelButton">Edit</button>
                    <button style = "display: none;" type = "button" class = "btn btn-primary delete adminPanelButton">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
{include file="inc_footer.tpl"}

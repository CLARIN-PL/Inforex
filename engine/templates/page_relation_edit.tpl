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
                <div class="panel-heading">Annotation sets</div>
                <div class="panel-body scrolling">
                    <div class="tableContent bigTableContent">
                        <table id = "annotationSetsTable" class="tablesorter table table-striped" id="public" cellspacing="1">
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
        </td>
        <td style="width: 50%; vertical-align: top">
            <div id="relationTypesContainer" class="tableContainer panel panel-primary scrollingWrapper" style="margin: 5px; display: none;">
                <div class="panel-heading">Relation types</div>
                <div class="panel-body scrolling">
                    <div class="tableContent">
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
                    <div class="tableOptions" element="relation_type" parent="annotationSetsContainer">
                        <button style = "display: none;" type = "button" class = "btn btn-primary create adminPanelButton">Create</button>
                        <button style = "display: none;" type = "button" class = "btn btn-primary edit adminPanelButton">Edit</button>
                        <button style = "display: none;" type = "button" class = "btn btn-primary delete adminPanelButton">Delete</button>
                    </div>
                </div>
            </div>
        </td>
    </tr>
</table>

{include file="inc_administration_bottom.tpl"}         
{include file="inc_footer.tpl"}

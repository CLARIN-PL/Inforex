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
        <div class="col-md-6 tableContainer" id = "sensContainer" style="padding: 0">
            <div class="panel panel-primary scrollingWrapper" style="margin: 5px;">
                <div class="panel-heading">Lemma</div>
                <div class="tableContent panel-body scrolling" style="">
                    <table class="tablesorter table table-striped" id="sensTable" cellspacing="1">
                        <thead>
                        <tr>
                            <th style="width:25px;">Lp.</th>
                            <th>Lemma</th>
                        </tr>
                        </thead>
                        <tbody id="sensTableItems">
                        {foreach from=$sensList key=key item=sens}
                            <tr class="sensName" id={$sens.id}>
                                <td>{$key+1}</td>
                                <td class="sens_name">{$sens.annotation_type}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer" element="event_group">
                    <button type = "button" class = "btn btn-primary sensCreate">Add lemma</button>
                    <button type = "button" class = "btn btn-primary sensEdit">Edit</button>
                    <button type = "button" class = "btn btn-primary sensDelete">Delete</button>
                </div>
            </div>
        </div>
        <div class="col-md-6" style="padding: 0" id="senses_options">
            <div class="panel panel-primary scrollingWrapper tableContainer" id="sense_panel" style="margin: 5px; display: none;">
                <div class="sensTableHeader panel-heading">Senses of lemma</div>
                <div class="panel-body">
                    <div id ="sensDescriptionList"  class="scrolling">
                    </div>
                </div>
                <div class="panel-footer" element="event_type" parent="eventGroupsContainer">
                    <button type = "button" class = "sensDescriptionCreate btn btn-primary create">New sense</button>
                </div>
            </div>
        </div>
    </div>
</div>
{include file="inc_footer.tpl"}

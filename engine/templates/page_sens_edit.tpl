{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<div class="alert alert-success ajax_status_text centered alert-dismissable" style="display:none; margin: 20px;">
</div>

<table class = "admin_tables">
    <tr>
        <td style="width: 30%; vertical-align: top; padding-right: 10px; " id="sensContainer" class="tableContainer">
            <div class="panel panel-primary scrollingWrapper" style="margin: 5px">
                <div class="panel-heading">Lemma</div>
                <div class="panel-body scrolling">
                    <div class="tableContent bigTableContent">
                        <table id = "sensTable" class="tablesorter table table-striped" id="public" cellspacing="1">
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
                    <div class="tableOptions" style = "margin-top: 10px; text-align:center;">
                        <button type = "button" class = "sensCreate btn btn-primary create adminPanelButton" style = "width: 120px;">Add lemma</button>
                        <button style = "display: none;" type = "button" class = "sensEdit btn btn-primary edit adminPanelButton">Edit</button>
                        <button style = "display: none;" type = "button" class = "sensDelete btn btn-primary delete adminPanelButton">Delete</button>
                    </div>
                </div>
            </div>
        </td>
        <td id = "senses_options" style="width: 50%; vertical-align: top">
            <div id = "sense_panel" class="panel panel-primary scrollingWrapper" style="margin: 5px; display:none;">
                <div class="sensTableHeader panel-heading">Senses of lemma</div>
                <div id='sensDescriptionList' class="panel-body scrolling sensList">
                </div>
                <div class="tableOptions" style = "margin: 10px; text-align:center;">
                    <div class='senses_actions descriptionTableOptions' element='relation_type'>
                        <button type = "button" class = "sensDescriptionCreate sensDelete btn btn-primary delete adminPanelButton" style = 'width: 150px;'>New sense</button>
                    </div>
                </div>
            </div>
            <div style="clear:both"></div>
        </td>
    </tr>
</table>

{include file="inc_administration_bottom.tpl"}         
{include file="inc_footer.tpl"}
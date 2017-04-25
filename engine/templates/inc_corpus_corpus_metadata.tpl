{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="container-fluid admin_tables">
    <div class="row">
        <div class="panel panel-primary scrollingWrapper" style="margin: 5px; width:40%;">
            <div class="panel-heading">Metadata</div>
            <div class="tableContent panel-body scrolling" style="">
                <table class="tablesorter table table-striped" id="extListContainer" cellspacing="1">
                    <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Null</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$extList item=set}
                        <tr>
                            <td>{$set.field}</td>
                            <td>{$set.type}</td>
                            <td>{$set.null}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
            <div class="panel-footer tableOptions" element="ext" parent="extListContainer">
                <button type = "button" class = "ext_edit btn btn-primary" action="add">Create</button>
                <button style = "display: none;" type = "button" class = "ext_edit btn btn-primary" action="edit">Edit</button>
            </div>
        </div>
    </div>
</div>

<button type="button" class="ext_edit btn btn-primary" action="add_table" style="{if $extList}display:none{/if}">Add custom metadata</button>
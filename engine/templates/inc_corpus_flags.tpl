{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="container-fluid admin_tables">
    <div class="row">
        <div class="panel panel-primary scrollingWrapper" style="margin: 5px; width: 60%;">
            <div class="panel-heading">Flags</div>
            <div class="tableContent panel-body scrolling" style="">
                <table class="tablesorter table table-striped" id="flagsListContainer" cellspacing="1">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Short name</th>
                        <th>Description</th>
                            <th style="width: 10px; text-align: right">Sort</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$flagsList item=set}
                        <tr>
                            <td>{$set.id}</td>
                            <td class="name">{$set.name}</td>
                            <td class="short">{$set.short}</td>
                            <td class="description">{$set.description}</td>
                            <td class="sort">{$set.sort}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
            <div class="panel-footer tableOptions" element="flag" parent="flagsListContainer">
                <button type = "button" class = "btn btn-primary create "  action="corpus_add_flag">New</button>
                <button style = "display: none;" type = "button" class = "btn btn-primary edit ">Edit</button>
                <button style = "display: none;" type = "button" class = "btn btn-danger delete ">Delete</button>
            </div>
        </div>
    </div>
</div>



{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="container-fluid admin_tables">
    <div class="row">
            <div class="panel panel-primary scrollingWrapper" style="margin: 5px; width: 40%;">
                <div class="panel-heading">Subcorpora</div>
                <div class="tableContent panel-body scrolling" style="">
                    <table class="tablesorter table table-striped" id="subcorpusListContainer" cellspacing="1">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$subcorpusList item=set}
                            <tr>
                                <td>{$set.id}</td>
                                <td>{$set.name}</td>
                                <td>{$set.description}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer tableOptions" element="subcorpus" parent="subcorpusListContainer" >
                    <button type = "button" class = "btn btn-primary create ">Create</button>
                    <button style = "display: none;" type = "button" class = "btn btn-primary edit ">Edit</button>
                    <button style = "display: none;" type = "button" class = "btn btn-danger delete ">Delete</button>
                </div>
            </div>
    </div>
</div>
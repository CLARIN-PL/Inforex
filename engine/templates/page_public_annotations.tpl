{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

{include file="inc_header2.tpl"}
<div class="container-fluid admin_tables">
    <div class="row">
        <div class="col-md-5 tableContainer" id = "annotationSetsContainer" style="padding: 0">
            <div class="panel panel-primary scrollingWrapper" style="margin: 5px;">
                <div class="panel-heading">Annotation sets</div>
                <div class="tableContent panel-body scrolling" style="">
                    <table class="table table-striped" id="annotationSetsTable" cellspacing="1">
                        <thead>
                        <tr>
                            <th style="width: 10%" class="td-right" >Id</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th class="td-center">Owner</th>
                            <th>Public corpora</th>
                            <th>All corpora</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$annotationSets item=set}
                            {if $set.public == 1}
                                <tr>
                                    <td class = "column_id td-right">{$set.id}</td>
                                    <td>{$set.name}</td>
                                    <td>{$set.description}</td>
                                    <td class="td-center">{$set.screename}</td>
                                    <td>
                                        <select class = "form-control">
                                            {if !empty($set.corpora) }
                                                {foreach from=$set.corpora item = corpora}
                                                    <option>{$corpora.name}</option>
                                                {/foreach}
                                            {else}
                                                <option>-</option>

                                            {/if}
                                        </select>
                                    </td>
                                    <td class = "text-center"><span class="badge">{$set.count_ann}</span></td>
                                </tr>
                            {/if}
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-3" style="padding: 0">
            <div class="panel panel-primary tableContainer scrollingWrapper" id="annotationSubsetsContainer" style="margin: 5px; visibility: hidden;">
                <div class="panel-heading">Annotation subsets</div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
                        <table id="annotationSubsetsTable" class="table table-striped" cellspacing="1">
                            <thead>
                            <tr>
                                <th style="width: 50px;" class="td-right">Id</th>
                                <th>Name</th>
                                <th>Description</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="padding: 0">
            <div class="panel panel-primary tableContainer scrollingWrapper" id="annotationTypesContainer" style="margin: 5px; visibility: hidden;">
                <div class="panel-heading">Categories</div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
                        <table id="annotationTypesTable" class="table table-striped" cellspacing="1">
                            <thead>
                            <tr>
                                <th style="width: 150px">Symbolic name</th>
                                <th title="short description" style="width: 150px">Display name</th>
                                <th>Description</th>
                                <th>Default visibility</th>
                                <th>Used</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

{include file="inc_footer.tpl"}

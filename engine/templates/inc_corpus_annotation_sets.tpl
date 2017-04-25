{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="container-fluid admin_tables">
    <div class="row">
        <div class="panel panel-primary scrollingWrapper" style="margin: 5px; width: 60%;">
            <div class="panel-heading">Annotation sets</div>
            <div class="tableContent panel-body scrolling" style="">
                <table class="tablesorter table table-striped" id="corpus_set_annotation_sets_corpora" cellspacing="1">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Description</th>
                        <th>Count</th>
                        <th>Assign</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$annotationsList item=set}
                        <tr>
                            <td>{$set.id}</td>
                            <td>{$set.description}</td>
                            <td>{$set.count_ann}</td>
                            <td><input class="annotationSet" type="checkbox" annotation_set_id="{$set.id}" {if $set.cid} checked="checked" {/if}/></td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



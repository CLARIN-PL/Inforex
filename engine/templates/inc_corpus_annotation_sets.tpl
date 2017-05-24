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
                <table class="table table-striped" id="corpus_set_annotation_sets_corpora" cellspacing="1">
                    <thead>
                    <tr>
                        <th style="width: 40px">Id</th>
                        <th>Name</th>
                        <th style="text-align: right" title="Number of annotations in the corpus">Count</th>
                        <th style="text-align: center">Use</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$annotationsList item=set}
                        <tr>
                            <td>{$set.id}</td>
                            <td>{$set.description}</td>
                            <td style="text-align: right"><span class="badge">{$set.count_ann}</span></td>
                            <td style="text-align: center"><input class="annotationSet" type="checkbox" annotation_set_id="{$set.id}" {if $set.cid} checked="checked" {/if}/></td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



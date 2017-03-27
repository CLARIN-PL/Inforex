{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="panel panel-info">
    <div class="panel-heading" role="tab" id="headingAnnotations">
        <h4 class="panel-title">
            <a data-toggle="collapse" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseAnnotations" aria-expanded="false" aria-controls="collapseAnnotations">
                Annotations</a>
        </h4>
    </div>
    <div id="collapseAnnotations" class="panel-collapse collapse">
        <div style="border-bottom: 1px solid #aaa; padding-bottom: 2px; ">
            <label>Stage:</label>
            <span class="stageItem" stage="new">new</span> | <span class="stageItem" stage="final">final</span> | <span class="stageItem" stage="discarded">discarded</span>
        </div>
        <div id="annotationList" class="annotations scrolling">
            <table class="table table-striped">
                <thead>
                    <th>Id</th>
                    <th>Text</th>
                    <th>From-to</th>
                    <th>Type</th>
                </thead>
                {foreach from=$annotations item=an}
                    <tr>
                        <td>{$an.id}</td>
                        <td>{$an.text}</td>
                        <td>{$an.from}:{$an.to}</td>
                        <td>{$an.type}</td>
                    </tr>
                {/foreach}
            </table>
            {*{foreach from=$sets key=setName item=set}*}
                {*<h1>{$setName}</h1>*}
                {*{foreach from=$set key=subsetName item=subset}*}
                    {*{if $subsetName!="groupid"}*}
                        {*<div style="background: #bbb; padding: 2px; margin-top: 4px;">{$subsetName}</div>*}

                        {*<table class="tablesorter" cellspacing="1">*}
                           {*<thead>*}
                               {*<tr>*}
                                   {*<th>Id</th>*}
                                   {*<th>From</th>*}
                                   {*<th>To</th>*}
                                   {*<th>Type</th>*}
                                   {*<th>Source</th>*}
                                   {*{if $subpage == 'annotator'}<th>X</th>{/if}*}
                               {*</tr>*}
                           {*</thead>*}
                           {*<tbody>*}
                                {*{foreach from=$subset key=typeName item=type}*}
                                    {*{foreach from=$type key=annkey item=annotation}*}
                                        {*{if is_array($annotation) }*}
                                        {*<tr stage="{$annotation.stage}">*}
                                            {*<td colspan="{if $subpage == 'annotator'}6{else}5{/if}">*}
                                                {*<span class="{$annotation.type}" title="an#{$annotation.id}:{$annotation.type}">{$annotation.text}</span>*}
                                            {*</td>*}
                                        {*</tr>*}
                                        {*<tr stage="{$annotation.stage}">*}
                                            {*<td>{$annotation.id}</td>*}
                                            {*<td>{$annotation.from}</td>*}
                                            {*<td>{$annotation.to}</td>*}
                                            {*<td>{$annotation.type}</td>*}
                                            {*<td><small>{$annotation.source}</small></td>*}
                                            {*{if $subpage == 'annotator'}<td class="deleteAnnotation" style="text-align: center; cursor:pointer" annotation_id="{$annotation.id}">x</td>{/if}*}
                                        {*</tr>*}
                                        {*{/if}*}
                                    {*{/foreach}*}
                                {*{/foreach}*}
                           {*</tbody>*}
                        {*</table>*}
                    {*{/if}*}
                {*{/foreach}*}
            {*{/foreach}*}
        </div>
    </div>
</div>

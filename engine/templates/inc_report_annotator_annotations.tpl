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
    <div id="collapseAnnotations" class="panel-collapse collapse {if $active_accordion=="collapseAnnotations"}in{/if}">
        <div class="scrollingAccordion">
            <div id="annotationList" class="annotations scrolling">
                <table class="table table-striped">
                    <thead>
                        <th>Id</th>
                        <th>Type</th>
                        <th>Text</th>
                        <th>Attributes</th>
                        <th class="range">From:to</th>
                        <th></th>
                    </thead>
                    <tbody>
                    {foreach from=$annotations item=an}
                        <tr annotation_id="{$an.id}" title="Creator: {$an.screename} ({$an.login})">
                            <td>{$an.id}</td>
                            <td>{$an.type}</td>
                            <td>{$an.text}</td>
                            <td class="attributes">{if $an.attributes}{$an.attributes}{else}-{/if}</td>
                            <td class="range">{$an.from}:{$an.to}</td>
                            <td class="icons">
                                <div class="hoverIcons">
                                    <a href="#" class="annotationEdit" title="Edit annotation">
                                        <i class="fa fa-pencil" aria-hidden="true"></i></a>
                                    <a href="#" class="annotationDelete" title="Delete annotation">
                                        <i class="fa fa-trash" aria-hidden="true"></i></a>
                                </div>
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

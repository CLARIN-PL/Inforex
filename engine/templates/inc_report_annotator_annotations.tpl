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
            <!--
            <div style="border-bottom: 1px solid #aaa; padding-bottom: 2px; ">
                <label>Stage:</label>
                <span class="stageItem" stage="new">new</span> | <span class="stageItem" stage="final">final</span> | <span class="stageItem" stage="discarded">discarded</span>
            </div>
            -->
            <div id="annotationList" class="annotations scrolling">
                <table class="table table-striped">
                    <thead>
                        <th>Id</th>
                        <th>Type</th>
                        <th>Text</th>
                        <th>Attributes</th>
                        <th>From-to</th>
                    </thead>
                    <tbody>
                    {foreach from=$annotations item=an}
                        <tr annotation_id="{$an.id}">
                            <td>{$an.id}</td>
                            <td>{$an.type}</td>
                            <td>{$an.text}</td>
                            <td>{if $an.attributes}{$an.attributes}{else}-{/if}</td>
                            <td>{$an.from}:{$an.to}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

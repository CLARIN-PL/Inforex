{if $smarty.cookies.accordionActive=="cell_annotation_list_header"}
<h3 id="cell_annotation_list_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
    <span class="ui-icon ui-icon-triangle-1-s"></span>
    <a tabindex="-1" href="#">Annotation list</a>
</h3>
<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
{else}
<h3 id="cell_annotation_list_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
    <span class="ui-icon ui-icon-triangle-1-e"></span>
    <a tabindex="-1" href="#">Annotation list</a>
</h3>
<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
{/if}                   
    <div style="border-bottom: 1px solid #aaa; padding-bottom: 2px; ">
        <label>Stage:</label>
        <span class="stageItem" stage="new">new</span> | <span class="stageItem" stage="final">final</span> | <span class="stageItem" stage="discarded">discarded</span> 
    </div>
    <div id="annotationList" class="annotations scrolling" style="height: 200px">
        {* TODO do usunięcia <var id="annotationsCount">0</var> annotation(s) on disabled/hidden layers *}
        {foreach from=$sets key=setName item=set}
            <h1>{$setName}</h1>
            {foreach from=$set key=subsetName item=subset}
                {if $subsetName!="groupid"}
                    <div style="background: #bbb; padding: 2px; margin-top: 4px;">{$subsetName}</div>
                    
                    <table class="tablesorter" cellspacing="1">
                       <thead>
                           <tr>
                               <th>Annotation</th>
                               <th>Type</th>
                               <th>Source</th>
                               <th>X</th>
                           </tr>
                       </thead>
                       <tbody>
                            {foreach from=$subset key=typeName item=type}
                                {foreach from=$type key=annkey item=annotation}                                         
                                    {if is_array($annotation) }
                                    <tr stage="{$annotation.stage}">
                                        <td><span class="{$annotation.type}" title="an#{$annotation.id}:{$annotation.type}">{$annotation.text}</span></td>
                                        <td>{$annotation.type}</td>
                                        <td><small>{$annotation.source}</small></td>
                                        <td class="deleteAnnotation" style="text-align: center; cursor:pointer" annotation_id="{$annotation.id}">x</td>
                                    </tr>
                                    {/if}                                       
                                {/foreach}
                            {/foreach}
                       </tbody>
                    </table>
                {/if}
            {/foreach}
        {/foreach}
    </div>
</div>
{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{if $smarty.cookies.accordionActive=="cell_annotation_add_header"}
<h3 id="cell_annotation_add_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
    <span class="ui-icon ui-icon-triangle-1-s"></span>
    <a tabindex="-1" href="#">Annotation pad</a>
    
</h3>
<div id="cell_annotation_add" style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
{else}
<h3 id="cell_annotation_add_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
    <span class="ui-icon ui-icon-triangle-1-e"></span>
    <a tabindex="-1" href="#">Annotation pad</a>
    
</h3>
<div id="cell_annotation_add" style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
{/if}               
    <div class="column" id="widget_annotation">
        <div style="padding: 5px;" class="annotations scrolling">
            <button id="quick_add_cancel" style="display:none">Cancel quick add</button>
            <input type="radio" name="default_annotation" id="default_annotation_zero" style="display: none;" value="" checked="checked"/>
            {foreach from=$annotation_types item=set key=k name=groups}     
                <div>
                    &raquo; <a href="#" title="show/hide list" label="#gr{$smarty.foreach.groups.index}" class="toggle_cookie"><b>{$k}</b> <small style="color: #777">[show/hide]</small></a>                    
                </div>
                <div id="gr{$smarty.foreach.groups.index}" groupid="{$set.groupid}">
                    <ul style="margin: 0px; padding: 0 20px">
                        {foreach from=$set item=set key=set_name name=subsets}
                        {if $set_name != "groupid"}
                            {if $set_name != "none"}
                                <li subsetid="{$set.subsetid}">
                                <a href="#" class="toggle_cookie" label="#gr{$smarty.foreach.groups.index}s{$smarty.foreach.subsets.index}"><b>{$set_name}</b> <small style="color: #777">[short/hide]</small></a>
                                <ul style="padding: 0px 10px; margin: 0px" id="gr{$smarty.foreach.groups.index}s{$smarty.foreach.subsets.index}">
                                    {if $set.notcommon}
                                    <li>
                                        <a href="#" title="show/hide rare annotations" class="short_all"><small style="color: #777">[short/all]</small></a>                                    
                                    </li>
                                    {/if}
                            {/if}                   
                            {foreach from=$set item=type key=subsetname}
                                {if $subsetname!="subsetid" && $subsetname!='notcommon'}
                                <li {if !$type.common}class="notcommon hidden"{/if}>
                                    <div>
                                        <input type="radio" name="default_annotation" value="{$type.name}" style="vertical-align: text-bottom" title="quick annotation &mdash; adds annotation for every selected text"/>
                                        <span class="{$type.name}" groupid="{$type.groupid}">
                                            <a href="#" type="button" value="{$type.name}" class="an" style="color: #555" title="{$type.description}">
                                            {if $type.short_description==null}
                                                {$type.name}
                                            {else}
                                                {$type.short_description}
                                            {/if}
                                            </a>
                                        </span>
                                    </div>
                                </li>
                                {/if}
                            {/foreach}
                            {if $set_name != "none"}
                                </ul>
                                </li>
                            {/if}
                        {/if} 
                        {/foreach}
                    </ul>       
                </div>
            {/foreach}
            <span id="add_annotation_status"></span>
            <input type="hidden" id="report_id" value="{$row.id}"/>
        </div>
    </div>
</div>

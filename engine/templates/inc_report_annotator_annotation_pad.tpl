<div class="panel panel-info">
    <div class="panel-heading" role="tab" id="headingPad">
        <h4 class="panel-title">
            <a data-toggle="collapse" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapsePad" aria-expanded="false" aria-controls="collapsePad">
                Annotation types</a>
        </h4>
    </div>
    <div id="collapsePad" class="panel-collapse collapse {if $active_accordion=="collapsePad"}in{/if}" style="padding: 2px;">
        <div class="scrollingAccordion">
        <div class="column scrolling" id="widget_annotation">
        <div id="annotation-types" style="padding: 5px;" class="annotations scrolling">
            <button id="quick_add_cancel" style="display:none">Cancel quick add</button>
            <input type="radio" name="default_annotation" id="default_annotation_zero" style="display: none;" value="" checked="checked"/>
            <div class="tree">
            {foreach from=$annotation_types_tree item=set key=k name=groups}
                <div>
                    &raquo; <a href="#" title="show/hide list" label="#gr{$smarty.foreach.groups.index}" class="toggle_cookie"><b>{$k}</b></a>
                </div>
                <div id="gr{$smarty.foreach.groups.index}" groupid="{$set.groupid}">
                    <ul style="margin: 0px; padding: 0 0 0 20px">
                        {foreach from=$set item=set key=set_name name=subsets}
                        {if $set_name != "groupid"}
                            {if $set_name != "none"}
                                <li subsetid="{$set.subsetid}">
                                <a href="#" title="show/hide rare annotations" class="short_all" style="float: right"><small style="color: #777">[short/all]</small></a>
                                <a href="#" class="toggle_cookie" label="#gr{$smarty.foreach.groups.index}s{$smarty.foreach.subsets.index}"><b>{$set_name}</b></a>
                                <ul style="padding: 0px 10px; margin: 0px" id="gr{$smarty.foreach.groups.index}s{$smarty.foreach.subsets.index}">
                            {/if}
                            {foreach from=$set item=type key=subsetname}
                                {if $subsetname!="subsetid" && $subsetname!='notcommon'}
                                <li {if $type.common == 1}class="notcommon hidden"{/if}>
                                    <div>
                                        {*<input type="radio" name="default_annotation" value="{$type.name}" style="vertical-align: text-bottom" title="quick annotation &mdash; adds annotation for every selected text"/>*}
                                        <span class="{$type.name}" groupid="{$type.groupid}">
                                            <a href="#" annotation_type_id = {$type.annotation_type_id} type="button" value="{$type.name}" class="an" style="color: #555" title="{$type.description}">
                                            {if $type.short_description==null}
                                                {$type.name}
                                            {else}
                                                {$type.short_description}
                                            {/if}
                                            </a>
                                        </span>
                                        <div class="icons" style="float: right;">
                                            <i {if !$type.not_default} style = "display: none;" {/if} id = "default{$type.annotation_type_id}" class="refresh_default fa fa-refresh" aria-hidden="true">  </i>
                                            <i id = "eye{$type.annotation_type_id}" {if $type.common == 0}class="eye_hide fa fa-eye-slash"{else}class="eye_hide fa fa-eye"{/if} aria-hidden="true"></i>
                                        </div>
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
            </div>
            <input type="hidden" id="report_id" value="{$row.id}"/>
        </div>
        </div>
        </div>
    </div>
</div>

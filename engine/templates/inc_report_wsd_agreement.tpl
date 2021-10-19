{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
<div id="col-agreement"
     class="col-main col-md-{bootstrap_column_width default=4 flags=$flags_active config=$config_active} scrollingWrapper">
    <div class="panel panel-primary">
        <div class="panel-heading clearfix">
            <span style="float: left;">Resolve annotations agreement</span>
            {if !empty($errors)}
                <button class="btn btn-warning errors_button" disabled style="float: right;" data-toggle="modal"
                        data-target="#errors_modal">Errors
                </button>
            {/if}
        </div>
        <div class="panel-body" style="padding: 0">
            <div id="agreement" class="scrolling">
                <table class="table table-stripped">
                    <thead>
                    <tr>
                        <th style="display: none">From</th>
                        <th style="display: none">To</th>
                        <th>Text</th>
                        <th>User A</th>
                        <th>User B</th>
                        <th>Final</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$wsd_annotations item=ele name=wsd}
                        {assign var="agreed_a_b" value=$ele.user_A_value==$ele.user_B_value}
                        {assign var="has_final" value=empty($ele.user_final_value)}

                        <tr class="{if $smarty.foreach.wsd.index%2==1}odd{/if}" ann_id="{$ele.ann_id}">
                            <td class="from" style="display: none; text-align: right">{$ele.from}</td>
                            <td class="to" style="display: none; text-align: right">{$ele.to}</td>
                            <td>{$ele.text}</td>
                            <td>{if !empty($ele.user_A_value)} {$ele.user_A_value} {else}-{/if}</td>
                            <td>{if !empty($ele.user_B_value)} {$ele.user_B_value} {else}-{/if}</td>
                            <td>
                                <select class="selectpicker show-tick"
                                        data-style="btn-sm"
                                        data-width="auto"
                                        data-selected-text-format="values: {$ele.user_final_value}">
                                    <option value="">-</option>
                                    {assign var="options" value=";"|explode:$ele.options}
                                    {foreach from=$options item=op}
                                        {assign var=item value="|"|explode:$op}
                                        <option value="{$item[0]}"><span>{$item[0]} </br> {$item[1]}</span></option>
                                    {/foreach}
                                </select>
                            </td>
                            <td>
								<span class="hoverIcons" style="display: inline;">
									<a href="#" class="acceptFinalAnnotation" title=""
                                           data-original-title="Accept annotation">
										<i class="fa fa-2x fa-check-circle text-success" aria-hidden="true"></i>
									</a>
								</span>
                                <span class="hoverIcons" style="display: {if $has_final} none; {else} inline; {/if}">
									<a href="#" class="removeFinalAnnotation" title=""
                                          data-original-title="Remove final annotation">
								    	<i class="fa fa-2x fa-trash text-danger" aria-hidden="true"></i>
									</a>
                                </span>
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
            <div id="aDiv" style="display: none;">
                <select id="aSelect">
                    <option value="1"> 1</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div id="col-content" class="col-md-4 scrollingWrapper">
    <div class="panel panel-default">
        <div class="panel-heading">Document content</div>
        <div class="panel-body" style="padding: 0">
            <div id="content" class="scrolling">
                <div style="margin: 5px;" class="contentBox {$report.format}">{$content_inline}</div>
            </div>
        </div>
    </div>
</div>


<div id="col-config" class="col-md-3 scrollingWrapper" {if !$config_active}style="display: none"{/if}>
    <div class="panel panel-info">
        <div class="panel-heading">View configuration</div>
        <div class="panel-body" style="padding: 0">
            <div class="scrolling">
                {*{include file="inc_widget_annotation_type_tree.tpl"}*}
                <br/>
                {include file="inc_widget_user_selection_a_b.tpl"}
            </div>
        </div>
        <div class="panel-footer">
            <form method="GET" action="index.php">
                {* The information about selected annotation sets, subsets and types is passed through cookies *}
                {* The information about selected users is paseed through cookies *}
                <input type="hidden" name="page" value="report"/>
                <input type="hidden" name="corpus" value="{$corpus.id}"/>
                <input type="hidden" name="subpage" value="wsd_agreement"/>
                <input type="hidden" name="id" value="{$report.id}"/>
                <input class="btn btn-primary" type="submit" value="Apply configuration" id="apply"/>
            </form>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="errors_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Document display errors</h4>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow: auto;">
                <table class="table table-striped">
                    <thead>
                    <th>Message</th>
                    </thead>
                    <tbody>
                    {foreach from=$errors item = error}
                        <tr>
                            <td>{$error}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

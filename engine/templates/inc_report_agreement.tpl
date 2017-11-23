{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div id="col-agreement" class="col-main col-md-{bootstrap_column_width default=4 flags=$flags_active config=$config_active} scrollingWrapper">
	<div class="panel panel-primary">
		<div class="panel-heading">Resolve annotations agreement</div>
		<div class="panel-body" style="padding: 0">

			<form method="post">
				<div id="agreement" class="scrolling">
					<table class="table table-stripped" cellspacing="1">
					<thead>
					<tr>
						<th>From</th>
						<th>To</th>
						<th>Text</th>
						<th>User A</th>
						<th>User B</th>
						<th>Action for the <i>final</i> annotation</th>
					</tr>
					</thead>
					{assign var=keep value=0}
					{assign var=add value=0}
					{assign var=choose value=0}
					<tbody>
					{foreach from=$groups item=gr name=grs}
						<tr class="{if $smarty.foreach.grs.index%2==1}odd{/if}">
							<td class="from" style="text-align: right">{$gr.from}</td>
							<td class="to" style="text-align: right">{$gr.to}</td>
							<td>{$gr.text}</td>
							<td>
                                {if !empty($gr.all_annotations)}
                                    {foreach from = $gr.all_annotations item = annotation}
                                        {if ($annotation.agreement == 'only_a' || $annotation.agreement == 'a_and_b')}
                                            {$annotation.type}<br>
                                        {else}
                                            <i>-</i><br>
                                        {/if}
                                    {/foreach}
                                {else}
                                    <i>-</i>
                                {/if}
                            </td>
							<td>
                                {if !empty($gr.all_annotations)}
                                    {foreach from = $gr.all_annotations item = annotation}
                                        {if ($annotation.agreement == 'only_b' || $annotation.agreement == 'a_and_b')}
                                            {$annotation.type}<br>
                                        {else}
                                            <i>-</i><br>
                                        {/if}
                                    {/foreach}
                                {else}
                                <i>-</i>
                                {/if}
                            </td>
							{assign var=cl value=""}
							{capture assign=ff}
								{if $gr.final}
                                    <ul>
                                        {if $gr.all_final}
                                            <li>
                                                <input type="radio" name="{$gr.from}:{$gr.to}" value="nop" checked="checked">
                                                <span title="The final annotation with type {$gr.final.type} already exists">Keep as <b>
                                                {foreach from = $gr.final item = annotation name = annotation}
                                                    {$annotation.type}{if !$smarty.foreach.annotation.last}, {/if}
                                                {/foreach}</b></span>
                                            </li>
                                            <li>
                                                <input type="radio" name="{$gr.from}:{$gr.to}" value="add_full">
                                                Create relation of type:
                                                <div class = "agreement_list">
                                                    {foreach from=$gr.available_annotation_types item=available_type}
                                                        <div class = "col-sm-12 annotation_checkbox">
                                                            <input type = "checkbox" name = "{$gr.from}:{$gr.to}_{$available_type.annotation_type_id}_type_id/add_full" value = "{$available_type.annotation_type_id}">{$available_type.name}
                                                        </div>
                                                    {/foreach}
                                                </div>
                                            </li>
                                            {assign var=cl value="keep"}
                                            {assign var=keep value=$keep+1}
                                        {elseif $gr.a_and_b}
                                            <li>
                                                <input checked = "checked" type="radio" name="{$gr.from}:{$gr.to}" value="add_full">
                                                Create annotation of matching types
                                                <strong>
                                                    {foreach from = $gr.a_and_b item = agreed_annotation name = agreed_annotation}
                                                            {$agreed_annotation.type}{if !$smarty.foreach.agreed_annotation.last}, {/if}
                                                    {/foreach}
                                                </strong>
                                                <div class="agreement_list">
                                                    {foreach from=$gr.available_annotation_types item=type}
                                                        <div class = "col-sm-12 annotation_checkbox">
                                                            <input {if $type.checked}checked = "checked"{/if} type = "checkbox" value = "{$type.annotation_type_id}" name = "{$gr.from}:{$gr.to}_{$type.annotation_type_id}_type_id/add_full">{$type.name}
                                                        </div>
                                                    {/foreach}
                                                </div>
                                            </li>
                                            {assign var=cl value="add"}
                                            {assign var=add value=$add+1}
                                        {else}
                                            <li>
                                                <input type="radio" name="{$gr.from}:{$gr.to}" value="nop" checked="checked">
                                                <span title="The final annotation with type {$gr.final.type} already exists">Keep as <b>
                                                {foreach from = $gr.final item = annotation name = annotation}
                                                    {$annotation.type}{if !$smarty.foreach.annotation.last}, {/if}
                                                {/foreach}</b></span>
                                            </li>
                                            {assign var=cl value="keep"}
                                            {assign var=keep value=$keep+1}
                                        {/if}
                                        <li><input type="radio" name="{$gr.from}:{$gr.to}" value="delete">
                                            Delete existing final relation:
                                            <div class = "agreement_list">
                                                {foreach from = $gr.final item = final_annotation}
                                                    <div class = "col-sm-12 relation_checkbox">
                                                        <input type = "checkbox" value = "{$final_annotation.type_id}" name = "{$gr.from}:{$gr.to}_{$final_annotation.annotation_id}_type_id/delete"> {$final_annotation.type}
                                                    </div>
                                                {/foreach}
                                            </div>
                                        </li>
									</ul>
								{elseif $gr.a_and_b}
                                    <ul>
                                        <li>
                                            <input checked = "checked" type="radio" name="{$gr.from}:{$gr.to}" value="add_full">
                                            Create annotation of matching types
                                            <strong>
                                                {foreach from = $gr.a_and_b item = agreed_annotation name = agreed_annotation}
                                                    {$agreed_annotation.type}{if !$smarty.foreach.agreed_annotation.last}, {/if}
                                                {/foreach}
                                            </strong>
                                            <div class="agreement_list">
                                                {foreach from=$gr.available_annotation_types item=type}
                                                    <div class = "col-sm-12 annotation_checkbox">
                                                        <input {if $type.checked}checked = "checked"{/if} type = "checkbox" value = "{$type.annotation_type_id}" name = "{$gr.from}:{$gr.to}_{$type.annotation_type_id}_type_id/add_full">{$type.name}
                                                    </div>
                                                {/foreach}
                                            </div>
                                        </li>
                                        {assign var=cl value="add"}
                                        {assign var=add value=$add+1}
                                    </ul>
								{else}
									<ul>
										<li><input type="radio" name="{$gr.from}:{$gr.to}" value="nop" checked="checked">
											Do not create an annotation
										</li>
										<li><input type="radio" name="{$gr.from}:{$gr.to}" value="add_full">
											Add as
                                            <div class="agreement_list">
                                                {foreach from=$gr.available_annotation_types item=type}
                                                    <div class = "col-sm-12 annotation_checkbox">
                                                        <input {if $type.checked}checked = "checked"{/if} type = "checkbox" value = "{$type.annotation_type_id}" name = "{$gr.from}:{$gr.to}_{$type.annotation_type_id}_type_id/add_full">{$type.name}
                                                    </div>
                                                {/foreach}
                                            </div>
										</li>
									</ul>
									{assign var=cl value="choose"}
									{assign var=choose value=$choose+1}
								{/if}
							{/capture}
							<td style="width: 250px" class="{$cl} agreement_actions">
								<span style="float: right" class="toggle">(<a href="#" title="click to see more available options">more</a>)</span>
								{$ff}
							</td>
						</tr>
					{/foreach}
					</tbody>
					</table>
				</div>

				<div class="panel-footer legend">
					<input type="submit" value="Apply actions" class="btn btn-primary" name="submit"/>
					<div style="float: right">
						Filter annotations:
						<span class="all"><a href="#">All: <b>{$keep+$add+$choose}</b></a></span>
						<span class="keep"><a href="#">Final: <b>{$keep}</b></a></span>
						<span class="add"><a href="#">Agreed: <b>{$add}</b></a></span>
						<span class="choose"><a href="#">Choose: <b>{$choose}</b></a></span>
					</div>
					<br style="clear: both"/>
				</div>

			</form>
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
				{include file="inc_widget_annotation_type_tree.tpl"}
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
				<input type="hidden" name="subpage" value="agreement"/>
				<input type="hidden" name="id" value="{$report.id}"/>
				<input class="btn btn-primary" type="submit" value="Apply configuration" id="apply"/>
			</form>
		</div>
	</div>
</div>

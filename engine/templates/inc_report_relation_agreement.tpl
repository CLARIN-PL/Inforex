{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div id="col-agreement" class="col-main col-md-{bootstrap_column_width default=4 flags=$flags_active config=$config_active} scrollingWrapper">
	<div class="panel panel-primary">
		<div class="panel-heading">Resolve relations agreement</div>
		<div class="panel-body" style="padding: 0">
			<form method="post">
				<div id="agreement" class="scrolling">
					<table class="table table-striped" cellspacing="1">
					<thead>
					<tr>
						<th>Source</th>
						<th>Target</th>
						<th>User A</th>
						<th>User B</th>
						<th>Action for the <i>final</i> relation</th>
					</tr>
					</thead>
					{assign var=keep value=0}
					{assign var=add value=0}
					{assign var=choose value=0}
					<tbody>
					{foreach from=$relation_agreement item=relation}
                        <tr class = "{$relation.source_from}_{$relation.source_to}/{$relation.target_from}_{$relation.target_to}">
                            <td class="source" title = {$relation.annotation_source_name}>{$relation.source_text}</td>
							<td class="target" title = {$relation.annotation_target_name}>{$relation.target_text}</td>
							{if $relation.user_agreement == 'only_a' || $relation.user_agreement == 'a_and_b'}
                            <td>
                                {if $relation.user_agreement == 'only_a'}
                                    {if count($relation.user_relations) > 1}
                                        {foreach from=$relation.user_relations item=user_relation}
                                            {$user_relation.relation_name} <br>
                                        {/foreach}
                                    {else}
                                        {$relation.user_relations[0].relation_name}
                                    {/if}
                                {else}
                                    {foreach from=$relation.all_relations item=relation_details}
                                        {if $relation_details.agreement == 'only_b'}
                                            -
                                        {else}
                                            {$relation_details.relation_name}
                                        {/if}
                                        <br>
                                    {/foreach}
                                {/if}
                            </td>
                            {else}
                            <td><i>-</i></td>
                            {/if}
                            {if $relation.user_agreement == 'only_b' || $relation.user_agreement == 'a_and_b'}
                                <td>
                                    {if $relation.user_agreement == 'only_b'}
                                        {if count($relation.user_relations) > 1}
                                            {foreach from=$relation.user_relations item=user_relation}
                                                {$user_relation.relation_name} <br>
                                            {/foreach}
                                        {else}
                                            {$relation.user_relations[0].relation_name}
                                        {/if}
                                    {else}
                                        {foreach from=$relation.all_relations item=relation_details}
                                            {if $relation_details.agreement == 'only_a'}
                                                -
                                            {else}
                                                {$relation_details.relation_name}
                                            {/if}
                                            <br>
                                        {/foreach}
                                    {/if}
                                </td>
                            {else}
                                <td class = "text"><i>-</i></td>
                            {/if}

							{assign var=cl value=""}
							{capture assign=ff}
								{if $relation.final != null}
									<ul>
                                        {if !empty($relation.a_and_b_relations)}
                                            {if $relation.only_finals == true}
                                                <li>
                                                    <input type="radio" name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}" value="nop" checked="checked">
                                                    <span title="Some final relations already exist">Keep as <b>
                                                {foreach from = $relation.final.final_relations item = final_relation name = final_relation}
                                                    {$final_relation.relation_name}{if !$smarty.foreach.final_relation.last}, {/if}
                                                {/foreach}</b></span>
                                                </li>
                                                <li>
                                                    <input type="radio" name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}" value="add_full">
                                                    Create relation of type:
                                                    <div class = "agreement_list">
                                                        {foreach from=$relation.relation_types item=type}
                                                            <div class = "col-sm-12 relation_checkbox">
                                                                <input type = "checkbox" name = "range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}_{$type.relation_type_id}_type_id_add_full" value = "{$type.relation_type_id}">{$type.name}
                                                            </div>
                                                        {/foreach}
                                                    </div>
                                                </li>
                                                {assign var=cl value="keep"}
                                                {assign var=keep value=$add+1}
                                            {else}
                                                <li><input checked="checked" type="radio" name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}" value="add_full">
                                                        Create relation of matching types: <strong>
                                                        {foreach from = $relation.a_and_b_relations item = agreed_relation}
                                                            {if !$agreed_relation.final}{$agreed_relation.name}{/if}
                                                        {/foreach}</strong>
                                                    <div class = "agreement_list">
                                                        {foreach from=$relation.relation_types item=type}
                                                            <div class = "col-sm-12 relation_checkbox">
                                                                <input {if $type.agreement == 'a_and_b'}checked = "checked"{/if}type = "checkbox" name = "range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}_{$type.relation_type_id}_type_id_add_full" value = "{$type.relation_type_id}">{$type.name}
                                                            </div>
                                                        {/foreach}
                                                    </div>
                                                </li>
                                                {assign var=cl value="add"}
                                                {assign var=keep value=$add+1}
                                            {/if}
                                        {else}
                                            <li>
                                                <input type="radio" name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}" value="nop" checked="checked">
                                                <span title="Some final relations already exist">Keep as <b>
                                                {foreach from = $relation.final.final_relations item = final_relation name = final_relation}
                                                    {$final_relation.relation_name}{if !$smarty.foreach.final_relation.last}, {/if}
											    {/foreach}</b></span>
                                            </li>
                                            <li><input type="radio" name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}" value="add_full">
                                                Create relation of type:
                                                <div class = "agreement_list">
                                                    {foreach from=$relation.relation_types item=type}
                                                        <div class = "col-sm-12 relation_checkbox">
                                                            <input type = "checkbox" value = "{$type.relation_type_id}" name = "range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}_{$type.relation_type_id}_type_id_add_full">{$type.name}
                                                        </div>
                                                    {/foreach}
                                                </div>
                                            </li>
                                            {assign var=cl value="keep"}
                                            {assign var=keep value=$keep+1}
                                        {/if}
                                        <li><input type="radio" name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}" value="del_final">
                                            Delete existing final relation:
                                            <div class = "agreement_list">
                                                {foreach from = $relation.final.final_relations item = final_relation}
                                                    <div class = "col-sm-12 relation_checkbox">
                                                        <input type = "checkbox" value = "{$final_relation.relation_type_id}" name = "range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}_{$final_relation.relation_type_id}_type_id_delete"> {$final_relation.relation_name}
                                                    </div>
                                                {/foreach}
                                            </div>
                                        </li>
									</ul>
								{elseif !empty($relation.a_and_b_relations)}
									<ul>
										<li>
                                            <input checked = "checked" type="radio" name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}" value="add_full">
                                            Create relation of matching types
                                                <strong>
                                                {foreach from = $relation.a_and_b_relations item = agreed_relation name = agreed_relation}
                                                    {$agreed_relation.name}{if !$smarty.foreach.agreed_relation.last}, {/if}
                                                {/foreach}
                                                </strong>
                                            <div class="agreement_list">
												{foreach from=$relation.relation_types item=type}
                                                    <div class = "col-sm-12 relation_checkbox">
                                                        <input {if $type.agreement}checked = "checked"{/if} type = "checkbox" value = "{$type.relation_type_id}" name = "range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}_{$type.relation_type_id}_type_id_add_full">{$type.name}
                                                    </div>
												{/foreach}
											</div>
										</li>
									</ul>
									{assign var=cl value="add"}
									{assign var=add value=$add+1}
								{elseif $relation.user_a_relations && $relation.user_b_relations && $relation.a_and_b_relations == null}
									<ul>
										<li><input type="radio" name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}" value="add_full" checked="checked">
											Choose relations
                                            <div class = "agreement_list">
                                            {foreach from=$relation.relation_types item=type}
                                                <div class = "col-sm-12 relation_checkbox">
                                                    <input type = "checkbox" value = "{$type.relation_type_id}" name = "range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}_{$type.relation_type_id}_type_id_add_full">{$type.name}
                                                </div>
                                            {/foreach}
                                            </div>
										</li>
									</ul>
									{assign var=cl value="choose"}
									{assign var=choose value=$choose+1}
								{else}
									<ul>
										<li><input type="radio" name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}" value="nop" checked="checked"> Do not create an relation</li>
										{if $relation.user_agreement == "only_a"}
										<li><input type="radio" name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}" value="add_full">
                                            Add as
                                            <div class = "agreement_list">
                                            {foreach from=$relation.relation_types item=type}
                                                <div class = "col-sm-12 relation_checkbox">
                                                    <input type = "checkbox" value = "{$type.relation_type_id}" name = "range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}_{$type.relation_type_id}_type_id_add_full">{$type.name}
                                                </div>
                                            {/foreach}
                                            </div>
                                        </li>
										{/if}
										{if $relation.user_agreement == "only_b"}
										<li><input type="radio" name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}" value="add_full">
                                            Add as
                                            <div class = "agreement_list">
                                            {foreach from=$relation.relation_types item=type}
                                                <div class = "col-sm-12 relation_checkbox">
                                                    <input type = "checkbox" value = "{$type.relation_type_id}" name = "range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}_{$type.relation_type_id}_type_id_add_full">{$type.name}
                                                </div>
                                            {/foreach}
                                            </div>
                                        </li>
										{/if}
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
				<div style="margin: 5px;" class="contentBox {$report.format}">{$content_inline|format_annotations}</div>
			</div>
		</div>
	</div>
</div>


<div id="col-config" class="col-md-3 scrollingWrapper" {if !$config_active}style="display: none"{/if}>
	<div class="panel panel-info">
		<div class="panel-heading">View configuration</div>
		<div class="panel-body" style="padding: 0">
			<div class="scrolling">
				{include file="inc_widget_relation_type_tree.tpl"}
				<br/>
                {include file="inc_widget_annotation_type_tree.tpl"}
                <br>
                {include file="inc_widget_user_selection_a_b_relation_agreement.tpl"}
			</div>
		</div>
		<div class="panel-footer">
			<form method="GET" action="index.php">
                {* The information about selected annotation sets, subsets and types is passed through cookies *}
                {* The information about selected users is paseed through cookies *}
				<input type="hidden" name="page" value="report"/>
				<input type="hidden" name="corpus" value="{$corpus.id}"/>
				<input type="hidden" name="subpage" value="relation_agreement"/>
				<input type="hidden" name="id" value="{$report.id}"/>
				<input class="btn btn-primary" type="submit" value="Apply configuration" id="apply"/>
			</form>
		</div>
	</div>
</div>

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
                                        {foreach from=$relation.user_relations item=user_relation}
                                            {$user_relation.relation_name}
                                            {if count($relation.user_relations) > 1}
                                                <br>
                                            {/if}
                                        {/foreach}
                                    {else}
                                        {foreach from=$relation.user_a_relations item=user_relation}
                                            {$user_relation.relation_name}
                                            {if count($relation.user_a_relations) > 1}
                                                <br>
                                            {/if}
                                        {/foreach}
                                    {/if}
                                </td>
                            {else}
                                <td><i>-</i></td>
                            {/if}
                            {if $relation.user_agreement == 'only_b' || $relation.user_agreement == 'a_and_b'}
                                <td>
                                    {if $relation.user_agreement == 'only_b'}
                                        {foreach from=$relation.user_relations item=user_relation}
                                            {$user_relation.relation_name}
                                            {if count($relation.user_relations) > 1}
                                                <br>
                                            {/if}
                                        {/foreach}
                                    {else}
                                        {foreach from=$relation.user_b_relations item=user_relation}
                                            {$user_relation.relation_name}
                                            {if count($relation.user_b_relations) > 1}
                                                <br>
                                            {/if}
                                        {/foreach}
                                    {/if}
                                </td>
                            {else}
                                <td><i>-</i></td>
                            {/if}

							{assign var=cl value=""}
							{capture assign=ff}
								{if $relation.final != null}
									<ul>
										<li>
                                            <input type="radio" name="relation_id_{$relation.final.relation_id}" value="nop" checked="checked">
											<span title="The final relation with type {$relation.final.relation_name} already exists">Keep as <b>{$relation.final.relation_name}</b></span>
										</li>
										{if ($relation.user_agreement == 'only_a' || $relation.user_agreement == 'only_b') && $relation.user_relations[0].relation_type_id != $relation.final.relation_type_id}
										<li>
											<input type="radio" name="relation_id_{$relation.final.relation_id}" value="change_{$relation.user_relations[0].relation_type_id}"> Change to <b>{$relation.user_relations[0].relation_name}</b>
										</li>
										{/if}

										{if $relation.user_agreement == 'a_and_b' && $relation.user_a_relations[0].relation_type_id != $relation.final.relation_type_id && $relation.user_b_relations[0].relation_type_id != $relation.final.relation_type_id}
										<li>
											<input type="radio" name="relation_id_{$relation.final.relation_id}" value="change_{$relation.user_a_relations[0].relation_type_id}"> Change to <b>{$relation.user_a_relations[0].relation_name}</b></li>
										{/if}
										<li>
											<input type="radio" name="relation_id_{$relation.final.relation_id}" value="change_select"> Change to
											<select name="relation_id_{$relation.final.relation_id}_select">
                                                <option><i>choose type</i></option>
                                                {foreach from=$relation.relation_types item=type}
												    <option value="{$type.relation_type_id}">{$type.name}</option>
											    {/foreach}
											</select>
										</li>
										<li>
											<input type="radio" name="relation_id_{$relation.final.relation_id}" value="delete"> <span style="color: red">Delete</span>
										</li>
									</ul>
									{assign var=cl value="keep"}
									{assign var=keep value=$keep+1}
								{elseif $relation.user_a_relations && $relation.user_b_relations && $relation.user_a_relations[0].relation_type_id == $relation.user_b_relations[0].relation_type_id}
									<ul>
										<li><input type="radio" name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}" value="add_{$relation.user_a_relations[0].relation_type_id}" checked="checked"> Add as <b>{$relation.user_a_relations[0].relation_name}</b></li>
										<li><input type="radio" name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}" value="add_full">
											Add as
											<select name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}_type_id_full">
												<option><i>choose type</i></option>
												{foreach from=$relation.relation_types item=type}
													<option value="{$type.relation_type_id}">{$type.name}</option>
												{/foreach}
											</select>
										</li>
									</ul>
									{assign var=cl value="add"}
									{assign var=add value=$add+1}
								{elseif $relation.user_a_relations && $relation.user_b_relations  && $relation.user_a_relations[0].relation_type_id != $relation.user_b_relations[0].relation_type_id}
									<ul>
										<li><input type="radio" name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}" value="add_short" checked="checked">
											Add as
											<select name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}_type_id_short">
												<option><i>choose type</i></option>
												<option value="{$relation.user_a_relations[0].relation_type_id}">{$relation.user_a_relations[0].relation_name}</option>
												<option value="{$relation.user_b_relations[0].relation_type_id}">{$relation.user_b_relations[0].relation_name}</option>
											</select>
										</li>
										<li><input type="radio" name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}" value="add_full">
											Add as
											<select name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}_type_id_full">
												<option><i>choose type</i></option>
                                                {foreach from=$relation.relation_types item=type}
													<option value="{$type.relation_type_id}">{$type.name}</option>
												{/foreach}
											</select>
										</li>
									</ul>
									{assign var=cl value="choose"}
									{assign var=choose value=$choose+1}
								{else}
									<ul>
										<li><input type="radio" name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}" value="nop" checked="checked"> Do not create an relation</li>
										{if $relation.user_agreement == "only_a"}
										<li><input type="radio" name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}" value="add_{$relation.user_relations[0].relation_type_id}"> Add as <b>{$relation.user_relations[0].relation_name}</b></li>
										{/if}
										{if $relation.user_agreement == "only_b"}
										<li><input type="radio" name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}" value="add_{$relation.user_relations[0].relation_type_id}"> Add as <b>{$relation.user_relations[0].relation_name}</b></li>
										{/if}
										<li><input type="radio" name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}" value="add_full">
											Add as
											<select name="range_{$relation.source_from}_{$relation.source_to}_{$relation.annotation_source_id}/{$relation.target_from}_{$relation.target_to}_{$relation.annotation_target_id}_type_id_full">
												<option><i>choose type</i></option>
												{foreach from=$relation.relation_types item=type}
													<option value="{$type.relation_type_id}">{$type.name}</option>
												{/foreach}
											</select>
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
                {include file="inc_widget_user_selection_a_b.tpl"}
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

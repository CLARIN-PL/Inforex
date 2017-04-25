{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}

<div class="row">

<div class="col-md-9 scrollingWrapper">
	<div class="panel panel-primary">
		<div class="panel-heading">Annotation statistics <small>(click the row to expand/roll-back)</small></div>
		<div class="panel-body scrolling" style="padding: 5px">

			<table cellspacing="1" class="table table-striped" id="annmap">
				<thead>

				<tr>
					<th colspan="3">Annotation</th>
					<th colspan="3">Count</th>
				</tr>

				<tr>
					<th rowspan="2" style="width: 150px">Group</th>
					<th rowspan="2" style="width: 150px">Subgroup</th>
					<th rowspan="2">Category/Value</th>
					<th style="text-align: right; width: 60px" title="Number of documents containing the annotation">docs</th>
					<th style="text-align: right; width: 60px" title="Number of unique annotation values">unique</th>
					<th style="text-align: right; width: 60px" title="Number of annotation instances">all</th>
				</tr>
				</thead>
				<tbody>
				{foreach from=$sets key=setId item=set}
					<tr class="setGroup expandable" name="{$setId}">
						{if $set.name eq ''}
							<td colspan="4"><span style="color:grey;font-style: italic;">HIDDEN::{$set.inc_name}</span></td>
						{else}
							<td colspan="4">{$set.name}</td>
						{/if}
						<td style="text-align:right">{$set.unique}</td>
						<td style="text-align:right">{$set.count}</td>
					</tr>
					{*
					{foreach from=$set.rows key=subsetName item=subset}
						<tr class="subsetGroup expandable"  style="display:none">
							<td class="empty"></td>
							<td colspan="3">{$subsetName}</td>
							<td style="text-align:right">{$subset.meta.unique}</td>
							<td style="text-align:right">{$subset.meta.count}</td>
						</tr>
						{foreach from=$subset.rows key=typeName item=tag}
							{if isset($tag) and is_array($tag)}
								<tr class="annotation_type" style="display:none">
									<td colspan="2" class="empty"></td>
									<td>
										 {if $tag.count > 0}
											 <a href="." class="toggle_simple" label=".annotation_type_{$tag.type}"><b>{$tag.type}</b></a>
										 {else}
											 <span style="color: grey">{$tag.type}</span>
										 {/if}
									</td>
									<td style="text-align:right">{$tag.docs}</td>
									<td style="text-align:right">{$tag.unique}</td>
									<td style="text-align:right">{$tag.count}</td>
								</tr>
								<tr class="annotation_type_{$tag.type} annotation_type_names expandable" style="display: none">
									<td colspan="2" class="empty2"></td>
									<td colspan="4">
										<ol>
										{foreach from=$tag.details item=detail}
											<li class="annotation_item">
												<span style="float: right;">{$detail.count}</span>
												<span style="margin-right: 50px">{$detail.text}</span>
												<div class="annotationItemLinks"></div>
											</li>
										{/foreach}
										</ol>
									</td>
								</tr>
							{/if}
						{/foreach}
					{/foreach}
					*}
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</div>

	<div class="col-md-3 scrollingWrapper">
        {include file="inc_document_filter.tpl"}
	</div>

</div>

{include file="inc_footer.tpl"}
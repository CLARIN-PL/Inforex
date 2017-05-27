{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}
<div class="row">
	<div class="col-md-3 scrollingWrapper">
		<div class="panel panel-primary">
			<div class="panel-heading">Relation sets and types</div>
			<div class="panel-body scrolling" style="padding: 0">
				<table cellspacing="1" class="table table-striped" id="relationlist">
					<thead>
						<tr>
							<th style="width: 100px">Relation set</th>
							<th>Relation type</th>
							<th class="td-right" style="width: 100px">Relation count</th>
						</tr>
					</thead>
					<tbody>
					{foreach from=$relations_type key=key1 item=item}
						<tr class="setGroup">
							<td colspan="2" style="vertical-align: middle">{$item.relation_name}</td>
							<td style="vertical-align: middle" class="relationNameCount td-right"><span class="badge">{$item.relation_count}</span></td>
						</tr>
						{foreach from=$item.types key=key2 item=types}
							<tr class="subsetGroup{if $key2 eq '0' && $key1 eq '0'} selected{/if}" style="display:none" id={$item.relation_id}>
								<td class="empty"></td>
								<td class="relationName">{$types.relation_type}</td>
								<td class="relationCount td-right">{$types.relation_count}</td>
							</tr>
						{/foreach}
					{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="col-md-9 scrollingWrapper">
		<div id="relation-list" class="panel panel-info">
			<div class="panel-heading">List of relations for the selected type</div>
			<div class="panel-body scrolling" style="padding: 0">
				<table cellspacing="1" class="table table-striped" style="width: 100%;">
					<thead>
						<tr>
							<th style="vertical-align: top">Document id</th>
							<th style="vertical-align: top">Subcorpus name</th>
							<th style="vertical-align: top">Source annotation phrase</th>
							<th style="vertical-align: top">Source annotation category</th>
							<th style="vertical-align: top">Target annotation phrase</th>
							<th style="vertical-align: top">Target annotation category</th>
						</tr>
					</thead>
					<tbody id="relation_statistic_items">
						<tr>
							<td colspan="6"><i>Choose relation type</i></td>
						</tr>
					{*
					{foreach from=$relations_list item=item}
    					<tr>
				    		<td style="vertical-align: middle"><a href="index.php?page=report&amp;corpus={$corpus_id}&amp;subpage=annotator&amp;id={$item.document_id}">{$item.document_id}</a></td>
    						<td style="vertical-align: middle">{$item.subcorpus_name}</td>
			    			<td style="vertical-align: middle">{$item.source_text}</td>
    						<td style="vertical-align: middle">{$item.source_type}</td>
			    			<td style="vertical-align: middle">{$item.target_text}</td>
	    					<td style="vertical-align: middle">{$item.target_type}</td>
				    	</tr>
    				{/foreach}
					*}
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
{include file="inc_footer.tpl"}
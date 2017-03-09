{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<table>
<tr>
<td style="vertical-align: top; width: 800px">

<form method="post">	
<div class="ui-widget ui-widget-content ui-corner-all">
	<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Resolve annotations agreement</div>
	<div id="agreement" style="overflow: scroll; height: 600px">
<table class="tablesorter" cellspacing="1">
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
		<td>{if $gr.user1}{$gr.user1.type}{else}<i>-</i>{/if}</td>
		<td>{if $gr.user2}{$gr.user2.type}{else}<i>-</i>{/if}</td>
		{assign var=cl value=""}
		{capture assign=ff}
			{if $gr.final}
				<ul>
					<li>
						<input type="radio" name="annotation_id_{$gr.final.id}" value="nop" checked="checked"> 
						<span title="The final annotation with type {$gr.final.type} already exists">Keep as <b>{$gr.final.type}</b></span>
					</li>
					{if $gr.user1 && $gr.user1.type != $gr.final.type}
					<li>
						<input type="radio" name="annotation_id_{$gr.final.id}" value="change_{$gr.user1.type_id}"> Change to <b>{$gr.user1.type}</b>
					</li>
					{/if}
					{if $gr.user2 && $gr.user2.type != $gr.final.type && (!$gr.user1 || $gr.user1.type != $gr.user2.type)}
					<li>
						<input type="radio" name="annotation_id_{$gr.final.id}" value="change_{$gr.user2.type_id}"> Change to <b>{$gr.user2.type}</b>
					</li>
					{/if}					
					<li>
						<input type="radio" name="annotation_id_{$gr.final.id}" value="change_select"> Change to
						<select name="annotation_id_{$gr.final.id}_select"> 
						{foreach from=$available_annotation_types item=type}
							<option value="{$type.annotation_type_id}">{$type.name}</option>
						{/foreach}						
						</select>
					</li>
					<li>
						<input type="radio" name="annotation_id_{$gr.final.id}" value="delete"> <span style="color: red">Delete</span>
					</li>
				</ul>
				{assign var=cl value="keep"}
				{assign var=keep value=$keep+1}
			{elseif $gr.user1 && $gr.user2 && $gr.user1.type == $gr.user2.type}
				<ul>
					<li><input type="radio" name="range_{$gr.from}_{$gr.to}" value="add_{$gr.user1.type_id}" checked="checked"> Add as <b>{$gr.user1.type}</b></li>
					<li><input type="radio" name="range_{$gr.from}_{$gr.to}" value="add_full">
						Add as 
						<select name="range_{$gr.from}_{$gr.to}_type_id_full">
							<option><i>choose type</i></option>
							{foreach from=$available_annotation_types item=type}
								<option value="{$type.annotation_type_id}">{$type.name}</option>
							{/foreach}
						</select>
					</li>					
				</ul>
				{assign var=cl value="add"}		
				{assign var=add value=$add+1}	
			{elseif $gr.user1 && $gr.user2 && $gr.user1.type != $gr.user2.type}
				<ul>
					<li><input type="radio" name="range_{$gr.from}_{$gr.to}" value="add_short" checked="checked">
						Add as 
						<select name="range_{$gr.from}_{$gr.to}_type_id_short">
							<option><i>choose type</i></option>
							<option value="{$gr.user1.type_id}">{$gr.user1.type}</option>
							<option value="{$gr.user2.type_id}">{$gr.user2.type}</option>
						</select>
					</li>
					<li><input type="radio" name="range_{$gr.from}_{$gr.to}" value="add_full">
						Add as 
						<select name="range_{$gr.from}_{$gr.to}_type_id_full">
							<option><i>choose type</i></option>
							{foreach from=$available_annotation_types item=type}
								<option value="{$type.annotation_type_id}">{$type.name}</option>
							{/foreach}
						</select>
					</li>					
				</ul>
				{assign var=cl value="choose"}
				{assign var=choose value=$choose+1}
			{else}
				<ul>
					<li><input type="radio" name="range_{$gr.from}_{$gr.to}" value="nop" checked="checked"> Do not create an annotation</li>
					{if $gr.user1}
					<li><input type="radio" name="range_{$gr.from}_{$gr.to}" value="add_{$gr.user1.type_id}"> Add as <b>{$gr.user1.type}</b></li>
					{/if}
					{if $gr.user2}
					<li><input type="radio" name="range_{$gr.from}_{$gr.to}" value="add_{$gr.user2.type_id}"> Add as <b>{$gr.user2.type}</b></li>
					{/if}
					<li><input type="radio" name="range_{$gr.from}_{$gr.to}" value="add_full">
						Add as 
						<select name="range_{$gr.from}_{$gr.to}_type_id_full">
							<option><i>choose type</i></option>
							{foreach from=$available_annotation_types item=type}
								<option value="{$type.annotation_type_id}">{$type.name}</option>
							{/foreach}
						</select>
					</li>					
				</ul>
				{assign var=cl value="choose"}
				{assign var=keep value=$keep+1}
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

<div class="legend">
	<input type="submit" value="Apply actions" class="button" name="submit" style="float: right"/>
	Filter annotations: 
	<span class="all"><a href="#">All: <b>{$keep+$add+$choose}</b></a></span>
	<span class="keep"><a href="#">Final: <b>{$keep}</b></a></span>
	<span class="add"><a href="#">Agreed: <b>{$add}</b></a></span>
	<span class="choose"><a href="#">Choose: <b>{$choose}</b></a></span>
	<br style="clear: both"/>
</div>

</div>
</form>

</td>

<td style="vertical-align: top">
	<div class="ui-widget ui-widget-content ui-corner-all">
		<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Document content</div>
		<div id="content" style="line-height: 200%;  overflow: auto;">
			<div style="margin: 5px;" class="contentBox {$report.format}">{$content_inline|format_annotations}</div>
		</div>
	</div>
</td>

<td style="width: 330px; vertical-align: top; ">
	<div id="rightPanelAccordion" class="ui-accordion ui-widget ui-helper-reset">
		<h3 id="cell_annotation_layers_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
			<span class="ui-icon ui-icon-triangle-1-s"></span>
			<a tabindex="-1" href="#">View configuration</a>
		</h3>			
		<div style="vertical-align: top; padding-top: 12px; padding-bottom: 12px; display: block; overflow: auto" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
	 		{include file="inc_widget_annotation_type_tree.tpl"}
	 		<br/>
	 		{include file="inc_widget_user_selection_a_b.tpl"}
	 		
			<form method="GET" action="index.php">
				{* The information about selected annotation sets, subsets and types is passed through cookies *}
				{* The information about selected users is paseed through cookies *}
				<input type="hidden" name="page" value="report"/>	
				<input type="hidden" name="corpus" value="{$corpus.id}"/>
				<input type="hidden" name="subpage" value="agreement"/>
				<input type="hidden" name="id" value="{$report.id}"/>
			 	<input class="button" type="submit" value="Apply configuration" id="apply"/>		 		
		 	</form>
		</div>		 		
	</div>
</td>

</tr>
</table> 
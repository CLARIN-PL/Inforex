{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}
{include file="inc_administration_top.tpl"}         

<div>
	<div id="eventGroupsContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Event groups</div>
		<div class="tableContent"> 
			<table id="eventGroupsTable" class="tablesorter" cellspacing="1">
				<thead>
					<tr>
						<th>id</th>
						<th>name</th>
						<th>description</th>
					</tr>				
				</thead>
				<tbody>
				{foreach from=$eventGroups item=group}
					<tr>
						<td>{$group.id}</td>
						<td>{$group.name}</td>
						<td>{$group.description}</td>
					</tr>					
				{/foreach}
				</tbody>
			</table>
		</div>
		<div class="tableOptions ui-widget ui-widget-content ui-corner-all" element="event_group">
			<span class="create" ><a href="#">(create)</a></span>
			<span class="edit" style="display:none"><a href="#">(edit)</a></span>
			<span class="delete" style="display:none"><a href="#">(delete)</a></span>
		</div>
	</div>
	
	<div id="eventTypesContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Event types</div>
		<div class="tableContent">
			<table id="eventTypesTable" class="tablesorter" cellspacing="1">
				<thead>
					<tr>
						<th>id</th>
						<th>name</th>
						<th>description</th>
					</tr>				
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div class="tableOptions ui-widget ui-widget-content ui-corner-all" element="event_type" parent="eventGroupsContainer">
			<span class="create" style="display:none"><a href="#">(create)</a></span>
			<span class="edit" style="display:none"><a href="#">(edit)</a></span>
			<span class="delete" style="display:none"><a href="#">(delete)</a></span>
		</div>
	</div>

	<div id="eventTypeSlotsContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Event type slots</div>
		<div class="tableContent">
			<table id="eventTypeSlotsTable" class="tablesorter" cellspacing="1">
				<thead>
					<tr>
						<th>id</th>
						<th>name</th>
						<th>description</th>
					</tr>				
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div class="tableOptions ui-widget ui-widget-content ui-corner-all" element="event_type_slot" parent="eventTypesContainer">
			<span class="create" style="display:none"><a href="#">(create)</a></span>
			<span class="edit" style="display:none"><a href="#">(edit)</a></span>
			<span class="delete" style="display:none"><a href="#">(delete)</a></span>
		</div>
	</div>

	<div style="clear:both"></div>

	<!--
	<div id="corpusEventGroupsContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Corpus event groups</div>
		<div class="tableContent">
			<table id="corpusEventGroupsTable" class="tablesorter">
				<thead>
					<tr>
						<th>id</th>
						<th>name</th>
						<th>description</th>
					</tr>				
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div class="tableOptions ui-widget ui-widget-content ui-corner-all" style="text-align:center">
			<span class="move"><a href="#">(>>>)</a></span>
		</div>
	</div>

	<div id="corpusContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Corpus</div>
		<div class="tableContent">
			<table id="corpusTable" class="tablesorter">
				<thead>
					<tr>
						<th>id</th>
						<th>name</th>
						<th>description</th>
					</tr>				
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div class="tableOptions ui-widget ui-widget-content ui-corner-all" style="text-align:center">
			<span class="move"><a href="#">(<<<)</a></span>
		</div>
	</div>

	<div style="clear:both"></div>
	
	!-->
</div>
{* <div style="width: 800px">
	<table id="user_activities" class="display" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th>Username</th>
				<th>Logged in</th>
				<th>Started</th>
				<th>Ended</th>
				<th>Duration <br/><small>[minutes]</small></th>
				<th>Actions</th>
				<th>Avg. inervals <br/><small>[minutes]</small></th>
			</tr>
		</thead>
		<tbody>
	{foreach from=$activities item=a}
		<tr>
			<td>{$a.screename}</td>
			<td style="text-align: center">{if $a.login}yes{else}no{/if}</td>		
			<td style="text-align: center">{$a.started}</td>
			<td style="text-align: center">{$a.ended}</td>
			<td style="text-align: right">{$a.duration}</td>
			<td style="text-align: right">{$a.counter}</td>
			<td style="text-align: center">
				{if $a.counter==0}
					0
				{else} 
					{math equation="y / x" x=$a.counter y=$a.duration format="%.2f"}
				{/if}
			</td>		
		</tr>
	{/foreach}
		</tbody>
	</table>
</div> *}

{include file="inc_administration_bottom.tpl"}         
{include file="inc_footer.tpl"}

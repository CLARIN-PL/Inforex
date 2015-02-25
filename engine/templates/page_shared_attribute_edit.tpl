{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}
{include file="inc_administration_top.tpl"}         

<div>
	<div id="sharedAttributesContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Shared attributes</div>
		<div class="tableContent"> 
			<table id="sharedAttributesTable" class="tablesorter" cellspacing="1">
				<thead>
					<tr>
						<th>id</th>
						<th>name</th>
						<th>type</th>
						<th>description</th>
					</tr>				
				</thead>
				<tbody>
				{foreach from=$sharedAttributes item=shared_attribute}
					<tr>
						<td>{$shared_attribute.id}</td>
						<td>{$shared_attribute.name}</td>
						<td>{$shared_attribute.type}</td>
						<td>{$shared_attribute.description}</td>
					</tr>					
				{/foreach}
				</tbody>
			</table>
		</div>
		<div class="tableOptions ui-widget ui-widget-content ui-corner-all">
			<span id="create_shared_attribute" ><a href="#">(create)</a></span>
			<span id="delete_shared_attribute" style="display:none"><a href="#">(delete)</a></span>
		</div>
	</div>
	
	<div id="sharedAttributesEnumContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Shared attribute values</div>
		<div class="tableContent">
			<table id="sharedAttributesEnumTable" class="tablesorter" cellspacing="1">
				<thead>
					<tr>
						<th>value</th>
						<th>description</th>
					</tr>				
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div class="tableOptions ui-widget ui-widget-content ui-corner-all">
			<span id="create_shared_attribute_enum" style="display:none"><a href="#">(create)</a></span>
			<span id="delete_shared_attribute_enum" style="display:none"><a href="#">(delete)</a></span>
		</div>
	</div>

	<div style="clear:both"></div>

	<div id="annotationTypesAttachedContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Annotation types attached</div>
		<div class="tableContent">
			<table id="annotationTypesAttachedTable" class="tablesorter">
				<thead>
					<tr>
						<th>id</th>
						<th>name</th>
					</tr>				
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div class="tableOptions ui-widget ui-widget-content ui-corner-all" style="text-align:center">
			<span id="move_detach" style="display:none"><a href="#">(>>>)</a></span>
		</div>
	</div>

	<div id="annotationTypesDetachedContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Annotation types detached</div>
		<div class="tableContent">
			<table id="annotationTypesDetachedTable" class="tablesorter">
				<thead>
					<tr>
						<th>id</th>
						<th>name</th>
					</tr>				
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div class="tableOptions ui-widget ui-widget-content ui-corner-all" style="text-align:center">
			<span id="move_attach" style="display:none"><a href="#">(<<<)</a></span>
		</div>
	</div>

	<div style="clear:both"></div>
	
	
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

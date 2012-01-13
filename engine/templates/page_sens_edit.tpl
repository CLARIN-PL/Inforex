{include file="inc_header.tpl"}

<h1>Sens editor</h1>

<div>
	<div id="sensContainer" class="tableContainer" style="float:left">
		<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Lematy</div>
		<div class="tableContent"> 
			<table id="sensTable" class="tablesorter">
{*				<thead>
					<tr>
						<th>id</th>
						<th>description</th>
					</tr>				
				</thead>
*}				<tbody>
				{foreach from=$sensList item=sens}
					<tr>
						<td class="sens" id={$sens.id} >{$sens.annotation_type}</td>
					</tr>					
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
	
	<div id="relationTypesContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Relation types</div>
		<div class="tableContent">
			<table id="relationTypesTable" class="tablesorter">
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
		<div class="tableOptions ui-widget ui-widget-content ui-corner-all" element="relation_type" parent="annotationSetsContainer">
			<span class="create" style="display:none"><a href="#">(create)</a></span>
			<span class="edit" style="display:none"><a href="#">(edit)</a></span>
			<span class="delete" style="display:none"><a href="#">(delete)</a></span>
		</div>
	</div>
	<div style="clear:both"></div>

</div>

{include file="inc_footer.tpl"}

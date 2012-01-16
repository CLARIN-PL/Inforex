{include file="inc_header.tpl"}

<h1>Sens editor</h1>
<div class="ajax_status">
	<span class="ajax_status_text" style="display:none"></span>
</div>
<div>
	<div id="sensContainer" class="sensTableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="sensTableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Lematy</div>
		<div class="sensTableContent"> 
			<table id="sensTable" class="tablesorter">
				<thead>
					<tr>
						<th style="width:25px;">Lp.</th>
						<th>Name</th>
					</tr>
				</thead>
				<tbody id="sensTableItems">
				{foreach from=$sensList key=key item=sens}
					<tr class="sensName" id={$sens.id}>
						<td>{$key+1}</td>
						<td class="sens_name">{$sens.annotation_type}</td>
					</tr>					
				{/foreach}
				</tbody>
			</table>
		</div>
		<div class="sensTableOptions ui-widget ui-widget-content ui-corner-all" element="event_group">
			<span class="sensCreate" ><a href="#">(create)</a></span>
			<span class="sensDelete" style="display:none"><a href="#">(delete)</a></span>
		</div>
	</div>
	
	<div id="sensDescriptionContainer" class="sensDescriptionContainer ui-widget ui-widget-content ui-corner-all" style="float:left;display:none">
		<div class="sensTableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all" {*style="display:none"*}>Opis znaczeń</div>
		<div class="sensDescriptionContent" {*style="display:none"*}>
			<div id="sensDescriptionList">
			</div>
		</div>
		<div class="tableOptions ui-widget ui-widget-content ui-corner-all" element="relation_type" parent="annotationSetsContainer">
			<span class="sensDescriptionCreate"><a href="#">(create)</a></span>
			<span class="sensDescriptionDelete" style="display:none"><a href="#">(delete)</a></span>
		</div>
	</div>	
	<div style="clear:both"></div>

</div>

{include file="inc_footer.tpl"}

{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}
{include file="inc_administration_top.tpl"}         

<div class="ajax_status">
	<span class="ajax_status_text" style="display:none"></span>
</div>
<div>
	<div id="sensContainer" class="sensTableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="sensTableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Lemma</div>
		<div class="sensTableContent"> 
			<table id="sensTable" class="tablesorter" cellspacing="1">
				<thead>
					<tr>
						<th style="width:25px;">Lp.</th>
						<th>Lemma</th>
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
			<span class="sensCreate" ><a href="#">(add lemma)</a></span>
			<span class="sensEdit" style="display:none"><a href="#">(edit)</a></span>
			<span class="sensDelete" style="display:none"><a href="#">(delete)</a></span>
		</div>
	</div>
	
	<div id="sensDescriptionContainer" class="sensDescriptionContainer ui-widget ui-widget-content ui-corner-all" style="float:left;display:none">		
	</div>	
	<div style="clear:both"></div>

</div>

{include file="inc_administration_bottom.tpl"}         
{include file="inc_footer.tpl"}
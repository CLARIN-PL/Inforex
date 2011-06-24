<div id="dialog" title="Błąd" style="display: none;">
	<p>
		<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 50px 0;"></span>
		<span class="message"></span>
	</p>
	<p><i><a href="">Refresh page.</a></i></p>
</div>
 
<table style="width: 100%; margin-top: 5px;">
	<tr>
		<td style="vertical-align: top"> 
			<div class="column" id="widget_text">
				<div class="ui-widget ui-widget-content ui-corner-all">
					<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Document content:</div>
					<div id="content">
						<div id="leftContent" style="padding: 5px;float:left; width:49%; border-right: 1px solid #E0CFC2" class="annotations scrolling content">{$content_inline|format_annotations}</div>
						<div id="rightContent" style="padding: 5px;width:49%" class="annotations scrolling content">{$content_inline2|format_annotations}</div>
						<div style="clear:both"></div>
					</div>
				</div>
			</div>
		</td>
		<td style="width: 300px; vertical-align: top; overflow: auto; ">
			<div id="cell_annotation_wait" style="display: none;">
				Trwa wczytywanie danych
				<img src="gfx/ajax.gif" />
				<input type="hidden" id="report_id" value="{$row.id}"/>
			</div>
			<div class="ui-widget ui-widget-content ui-corner-all">
				<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Relation list:</div>
				<div id="relationList" class="annotations relationsContainer scrolling">
					<table class="tablesorter" cellspacing="1" style="font-size: 8pt">
						<thead>
							<tr>
								<th>Jednostka źródłowa</th>
								<th>Nazwa relacji</th>
								<th>Jednostka docelowa</th>
								<th></th>
							</tr>
						</thead>
						<tbody id="relationListContainer">
						{foreach from=$allrelations item=relation}
							<tr>
								<td><span class="{$relation.source_type}" title="an#{$relation.source_id}:{$relation.source_type}">{$relation.source_text}</span></td>
								<td>{$relation.name}</td>
								<td><span class="{$relation.target_type}" title="an#{$relation.target_id}:{$relation.target_type}">{$relation.target_text}</span></td>
								<td class="relationDelete" source_id="{$relation.source_id}" target_id="{$relation.target_id}" relation_id="{$relation.id}" type_id="{$relation.relation_type_id}" style="cursor:pointer">X</td>
							</tr>
						{/foreach}							
						</tbody>
					</table>	
				</div>
			</div>
			<div class="ui-widget ui-widget-content ui-corner-all">
				<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Relation types:</div>
				<div id="relationTypesList" class="annotations relationTypesContainer scrolling">
					<table class="tablesorter" cellspacing="1" style="font-size: 8pt">
						<thead>
							<tr>
								<th></th>
								<th>Type name</th>
							</tr>
						</thead>
						<tbody>
						{foreach from=$availableRelations item=relation}
							<tr>
								<td><input type="radio" relation_id="{$relation.id}" name="quickAdd"/></td>
								<td title="{$relation.description}"><span class="addRelation token hiddenAnnotation" style="cursor:pointer" relation_id="{$relation.id}">{$relation.name}</span></td>
							</tr>
						{/foreach}							
						</tbody>
					</table>	
					<input type="checkbox" id="quickAdd" /> quick add
				</div>
			</div>
		</td>
	</tr>
</table>


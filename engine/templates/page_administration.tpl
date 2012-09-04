{include file="inc_header.tpl"}

<h1>Corpus editor</h1>

<div>
	<div id="corpusListContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Corpus list</div>
		<div class="tableContent"> 
			<table id="corpusListTable" class="tablesorter">
				<thead>
					<tr>
						<th>id</th>
						<th>name</th>
					</tr>				
				</thead>
				<tbody>
				{foreach from=$corpusList item=set}
					<tr>
						<td>{$set.id}</td>
						<td>{$set.name}</td>
					</tr>					
				{/foreach}
				</tbody>
			</table>
		</div>
		<div class="tableOptions ui-widget ui-widget-content ui-corner-all" element="corpus" parent="corpusListContainer">
			<span class="create"><a href="#">(create)</a></span>
		</div>
	</div>
	
	<div id="corpusElementsContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Corpus details</div>
		<div class="tableContent">
			<table id="corpusElementsTable" class="tablesorter">
				<thead>
					<tr>
						<th>element</th>
						<th>value</th>
					</tr>				
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>		
	</div>
	<div style="clear:both"></div>

</div>

{include file="inc_footer.tpl"}

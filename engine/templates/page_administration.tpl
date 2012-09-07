{include file="inc_header.tpl"}

<h1>Corpus editor</h1>

<div>
{if $corpusList}
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
			<span class="create" {if not "admin"|has_role}style="display:none"{/if} ><a href="#">(create)</a></span>
			<span class="delete" style="display:none"><a href="#">(delete)</a></span>
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
		<div class="tableOptions ui-widget ui-widget-content ui-corner-all" element="corpus_details" parent="corpusElementsContainer">
			<span class="edit" style="display:none"><a href="#">(edit)</a></span>
		</div>
	</div>
{/if}
	<div id="subcorpusListContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Subcorpus list</div>
		<div class="tableContent"> 
			<table id="subcorpusListTable" class="tablesorter">
				<thead>
					<tr>
						<th>id</th>
						<th>name</th>
						<th>description</th>
					</tr>				
				</thead>
				<tbody>
				{foreach from=$subcorpusList item=set}
					<tr>
						<td>{$set.id}</td>
						<td>{$set.name}</td>
						<td>{$set.description}</td>
					</tr>					
				{/foreach}				
				</tbody>
			</table>
		</div>
		<div class="tableOptions ui-widget ui-widget-content ui-corner-all" element="subcorpus" parent="subcorpusListContainer">
			<span class="create" {if $subcorpusList}id={$corpus.id} {else}style="display:none"{/if}><a href="#">(create)</a></span>
			<span class="edit" style="display:none"><a href="#">(edit)</a></span>
			<span class="delete" style="display:none"><a href="#">(delete)</a></span>
		</div>
	</div>	
	
	<div id="flagsListContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Flag in corpus list</div>
		<div class="tableContent"> 
			<table id="flagsListTable" class="tablesorter">
				<thead>
					<tr>
						<th>id</th>
						<th>name</th>
						<th>short</th>
						<th>sort</th>
					</tr>				
				</thead>
				<tbody>
				{foreach from=$flagsList item=set}
					<tr>
						<td>{$set.id}</td>
						<td>{$set.name}</td>
						<td>{$set.short}</td>
						<td>{$set.sort}</td>
					</tr>					
				{/foreach}				
				</tbody>
			</table>
		</div>
		<div class="tableOptions ui-widget ui-widget-content ui-corner-all" element="flag" parent="flagsListContainer">
			<span class="create" {if $flagsList}id={$corpus.id} {else}style="display:none"{/if}><a href="#">(create)</a></span>
			<span class="edit" style="display:none"><a href="#">(edit)</a></span>
			<span class="delete" style="display:none"><a href="#">(delete)</a></span>
		</div>
	</div>
	<div style="clear:both"></div>

</div>
{include file="inc_footer.tpl"}

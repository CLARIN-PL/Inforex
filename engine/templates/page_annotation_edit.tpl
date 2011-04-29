{include file="inc_header.tpl"}

<h1>Annotation editor</h1>

<div>
	<div id="annotationSetsContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Annotation sets</div>
		<div class="tableContent"> 
			<table id="annotationSetsTable" class="tablesorter">
				<thead>
					<tr>
						<th>id</th>
						<th>description</th>
					</tr>				
				</thead>
				<tbody>
				{foreach from=$annotationSets item=set}
					<tr>
						<td>{$set.id}</td>
						<td>{$set.description}</td>
					</tr>					
				{/foreach}
				</tbody>
			</table>
		</div>
		<div class="tableOptions ui-widget ui-widget-content ui-corner-all" element="annotation_set">
			<span class="create" ><a href="#">(create)</a></span>
			<span class="edit" style="display:none"><a href="#">(edit)</a></span>
			<span class="delete" style="display:none"><a href="#">(delete)</a></span>
		</div>
	</div>
	
	<div id="annotationSubsetsContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Annotation subsets</div>
		<div class="tableContent">
			<table id="annotationSubsetsTable" class="tablesorter">
				<thead>
					<tr>
						<th>id</th>
						<th>description</th>
					</tr>				
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div class="tableOptions ui-widget ui-widget-content ui-corner-all" element="annotation_subset" parent="annotationSetsContainer">
			<span class="create" style="display:none"><a href="#">(create)</a></span>
			<span class="edit" style="display:none"><a href="#">(edit)</a></span>
			<span class="delete" style="display:none"><a href="#">(delete)</a></span>
		</div>
	</div>

	<div id="annotationTypesContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Annotation types</div>
		<div class="tableContent">
			<table id="annotationTypesTable" class="tablesorter">
				<thead>
					<tr>
						<th>name</th>
						<th>short desc.</th>
						<th>description</th>
						<th style="display:none">css</th>
					</tr>				
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div class="tableOptions ui-widget ui-widget-content ui-corner-all" element="annotation_type" parent="annotationSubsetsContainer">
			<span class="create" style="display:none"><a href="#">(create)</a></span>
			<span class="edit" style="display:none"><a href="#">(edit)</a></span>
			<span class="delete" style="display:none"><a href="#">(delete)</a></span>
		</div>
	</div>

	<div style="clear:both"></div>

	<div id="annotationSetsCorporaContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="float:left">
		<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Annotation sets - Corpus</div>
		<div class="tableContent">
			<table id="annotationSetsCorporaTable" class="tablesorter">
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
			<span class="move unassign"><a href="#">(>>>)</a></span>
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
			<span class="move assign"><a href="#">(<<<)</a></span>
		</div>
	</div>

	<div style="clear:both"></div>
	
</div>
{* <div style="width: 800px"
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

{include file="inc_footer.tpl"}

{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}
{include file="inc_administration_top.tpl"}         

<div class="page_annotation">

    <div class="left" style="float: left; width: 200px;"> 
        <h2 style="margin: 0 0 5px 0">Select set</h2>
		<div id="annotationSetsContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="float:left; width: 200px; margin: 0">
			<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Sets</div>
			<div class="tableContent" style="height: 662px"> 
				<table id="annotationSetsTable" class="tablesorter" cellspacing="1">
					<thead>
						<tr>
							<th>id</th>
							<th>name</th>
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
	</div>
	
	<div class="right" style="margin-left: 215px; border-left: 1px solid #999; padding-left: 5px">
        <h2>Edit annotaiton subsets and categories</h2>
	   <table>
	       <tr>
	           <td style="vertical-align: top">
					<div id="annotationSubsetsContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="width: 300px">
						<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Subsets</div>
						<div class="tableContent">
							<table id="annotationSubsetsTable" class="tablesorter" cellspacing="1">
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
						<div class="tableOptions ui-widget ui-widget-content ui-corner-all" element="annotation_subset" parent="annotationSetsContainer">
							<span class="create" style="display:none"><a href="#">(create)</a></span>
							<span class="edit" style="display:none"><a href="#">(edit)</a></span>
							<span class="delete" style="display:none"><a href="#">(delete)</a></span>
						</div>
					</div>
		      </td>
		    
		      <td style="vertical-align: top">
					<div id="annotationTypesContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all">
						<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Categories</div>
						<div class="tableContent">
							<table id="annotationTypesTable" class="tablesorter" cellspacing="1">
								<thead>
									<tr>
										<th>name</th>
										<th title="short description">short</th>
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
			</td>
		</tr>
	</table>
    
    <h2 style="margin-top: 10px">Attach/detach annotation set to corpora</h2>
	<table>
		<tr>		
	       <td style="vertical-align: top">
				<div id="annotationSetsCorporaContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="width: 300px">
					<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">
					  The set is attached to the following corpora</div>
					<div class="tableContent">
						<table id="annotationSetsCorporaTable" class="tablesorter" cellspacing="1">
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
						<span class="move unassign"><a href="#" title="Detach schema from the corpora">(>>>)</a></span>
					</div>
				</div>
			</td>
			<td style="vertical-align: top">
				<div id="corpusContainer" class="tableContainer ui-widget ui-widget-content ui-corner-all" style="width: 300px">
					<div class="tableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">
					  Other corpora</div>
					<div class="tableContent">
						<table id="corpusTable" class="tablesorter" cellspacing="1">
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
						<span class="move assign"><a href="#" title="Attach schema to the corpora">(<<<)</a></span>
					</div>
				</div>
			</td>
		</tr>
	</table>
	</div>

	<div style="clear:both"></div>
	
</div>

{include file="inc_administration_bottom.tpl"}         
{include file="inc_footer.tpl"}

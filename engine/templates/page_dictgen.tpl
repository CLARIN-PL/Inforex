{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<h1>Annotations statistics</h1>

<div style="float: left; width: 400px;">
{include file="inc_document_filter.tpl"}
</div>

<div style="margin-left: 420px">
	<h2>Number of annotations according to categories and subcategories <small>(click the row to expand/roll-back)</h2>

	<table cellspacing="1" class="tablesorter" id="annmap" style="width: 800px">
		<thead>		
		
		<tr>
			<th>Annotation</th>
			<th>Subgroup</th>		
			<th>Category</th>
			<th>Count</th>
		</tr>
		
		</thead>
		<tbody>

		</tbody>
	</table>	
</div>	

<br style="clear: both;"/>

{include file="inc_footer.tpl"}
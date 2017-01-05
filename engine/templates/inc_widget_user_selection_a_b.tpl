{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
	<div id="user_selection_a_b">
		<table class="tablesorter" cellspacing="1" style="width: 100%">
			<tr>
				<th>Annotator name</th>
				<th title="Number of 'agreement' annotations" style="text-align: right; width: 40px">Anns</th>
				{*<th title="Number of documents with user's annotations">Docs</th>*}
				<th style="text-align: center">A</th>
				<th style="text-align: center">B</th>
			</tr>
			{if $users|@count > 0}
			{foreach from=$users item=a}
			<tr>
				<td style="line-height: 20px">{$a.screename}</td>
				<td style="line-height: 20px; text-align: right">{$a.annotation_count}</td>
				{*<td style="line-height: 20px; text-align: right">{$a.document_count}</td>*}
				<td style="text-align: center"><input type="radio" name="annotator_a_id" value="{$a.user_id}" {if $a.user_id == $annotator_a_id}checked="checked"{/if}/></td>
				<td style="text-align: center"><input type="radio" name="annotator_b_id" value="{$a.user_id}" {if $a.user_id == $annotator_b_id}checked="checked"{/if}/></td> 
			</tr>
			{/foreach}
			{/if}
		</table>

		{if $users|@count == 0}
			{capture assign=message}
			<em>There are no agreement annotations for the selected criteria.</em> 
			{/capture}
			{include file="common_message.tpl"}
		{/if}			

	</div>	
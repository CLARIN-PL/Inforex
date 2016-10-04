{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<div style="width: 300px; float: left">
<form action="index.php" method="GET">
	<input type="hidden" name="page" value="agreement_check"/>
	<input type="hidden" name="corpus" value="{$corpus.id}"/>
	
	<h1>Annotation set</h1>
		<select name="annotation_set_id">
			<option value="{$set.annotation_set_id}" style="font-style: italic">select an annotation set</option>
			{foreach from=$annotation_sets item=set}
			<option value="{$set.annotation_set_id}" {if $set.annotation_set_id==$annotation_set_id}selected="selected"{/if}>{$set.description}</option>
			{/foreach}
		</select>

	<h1>Comparision mode</h1>
		<select name="comparision_mode">
			{foreach from=$comparision_modes key=k item=mode}
			<option value="{$k}" {if $k==$comparision_mode}selected="selected"{/if}>{$mode}</option>
			{/foreach}
		</select>
	

{if $annotation_set_id}
	<h1>Users</h1>
		<em>Only with <i>agreement</i> annotations.</em>
		<table class="tablesorter" cellspacing="1" style="width: auto;">
			<tr>
				<th>Annotator name</th>
				<th>Annotation count</th>
				<th style="text-align: center">A</th>
				<th style="text-align: center">B</th>
			</tr>
			{foreach from=$annotators item=a}
			<tr>
				<td style="line-height: 20px">{$a.screename}</td>
				<td style="line-height: 20px; text-align: right">{$a.annotation_count}</td>
				<td><input type="radio" name="annotator_a_id" value="{$a.user_id}" {if $a.user_id == $annotator_a_id}checked="checked"{/if}/></td>
				<td><input type="radio" name="annotator_b_id" value="{$a.user_id}" {if $a.user_id == $annotator_b_id}checked="checked"{/if}/></td> 
			</tr>
			{/foreach}
		</table>
{/if}

	<br/>
	<input type="submit" value="Submit" class="button"/>
</form>

</div>

<div style="margin-left: 310px">
{if $annotator_a_id && $annotator_b_id}
<h1>Agreement</h1>

<h2>Summary</h2>
<table class="tablesorter" cellspacing="1" style="width: auto;">
	<tr>
		<th>Positive Specific Agreement</th>
		<td style="text-align: right">{$pcs|number_format:0}%</td>
	</tr>
	<tr>
		<th>Annotated by A and B</th>
		<td style="text-align: right">{$agreement.a_and_b|@count}</td>
	</tr>
	<tr>
		<th>Annotated only by A</th>
		<td style="text-align: right">{$agreement.only_a|@count}</td>
	</tr>
	<tr>
		<th>Annotated only by B</th>
		<td style="text-align: right">{$agreement.only_b|@count}</td>
	</tr>
</table>

<h2>Details</h2>
{assign var=last_report_id value=0}
<table class="tablesorter" cellspacing="1" style="width: auto;">
	<tr>
		<th>Report id</th>
		<th style="text-align: center" colspan="3">Only A</th>
		<th style="text-align: center" colspan="3">A and B</th>
		<th style="text-align: center" colspan="3">Only B</th>
	</tr>
	{foreach from=$agreement.annotations key=ank item=an}
	{if $last_report_id != $an.report_id}
	<tr>
		<th colspan="10" style="text-align: center; background-color: #FFB347">Report {$an.report_id}</th>
	</tr>
	{assign var=last_report_id value=$an.report_id}
	{/if}
	<tr>
		<td>{$an.report_id}</td>
		
		{if array_key_exists($ank, $agreement.only_a)}
			<td>[{$an.from},{$an.to}]</td> <td><em>{$an.text}</em></td> <td>[{$an.annotation_name}]</td>
		{else}
			<td colspan="3"></td>		
		{/if}
		
		{if array_key_exists($ank, $agreement.a_and_b)}
			<td style="background-color: #e5ffcc">[{$an.from},{$an.to}]</td> 
			<td style="background-color: #e5ffcc"><em>{$an.text}</em></td> 
			<td style="background-color: #e5ffcc">{if $agreement.annotations_a[$ank].annotation_name != $agreement.annotations_b[$ank].annotation_name}
				<span style="color: red">[{$agreement.annotations_a[$ank].annotation_name}], [{$agreement.annotations_b[$ank].annotation_name}]</span>
				{else}[{$an.annotation_name}]{/if}</td>
		{else}
			<td colspan="3" style="background-color: #e5ffcc"></td>		
		{/if}
		
		{if array_key_exists($ank, $agreement.only_b)}
			<td>[{$an.from},{$an.to}]</td> <td><em>{$an.text}</em></td> <td>[{$an.annotation_name}]</td>
		{else}
			<td colspan="3"></td>		
		{/if}
		
	</tr>
	{/foreach}
</table>

{else}
	{capture assign=message}
	<em>Select annotation set and users A and B in the panel on the left.</em> 
	{/capture}
	{include file="common_message.tpl"}
{/if}	
</div>
<br style="clear: both"/>

{include file="inc_footer.tpl"}
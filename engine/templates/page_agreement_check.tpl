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
	
	<h1>Document filter</h1>
	<div style="margin-left: 20px;">
		<h2>By flag</h2>
		<div style="margin-left: 20px; font-size: 10px">
		<select name="corpus_flag_id" style="font-size: 12px">
			<option style="font-style: italic">Select flag</option>
			{foreach from=$corpus_flags item=flag}
			<option value="{$flag.corpora_flag_id}" {if $flag.corpora_flag_id==$corpus_flag_id}selected="selected"{/if}><em>{$flag.name}</em> [{$flag.short}]</option>
			{/foreach}
		</select>
		<select name="flag_id" style="font-size: 12px">
			<option style="font-style: italic">type</option>
			{foreach from=$flags item=flag}
			<option value="{$flag.flag_id}" style="background-image:url(gfx/flag_{$flag.flag_id}.png); background-repeat: no-repeat; padding-left: 20px;" {if $flag.flag_id==$flag_id}selected="selected"{/if}>{$flag.name}</option>
			{/foreach}
		</select>
		</div>

		<h2>By subcorpus</h2>
    	<div class="checkbox_list" style="margin-left: 20px;">
    		{foreach from=$subcorpora item=subcorpus}
        	<label><input type="checkbox" name="subcorpus_ids[]" value="{$subcorpus.subcorpus_id}" {if in_array($subcorpus.subcorpus_id, $subcorpus_ids)}checked="checked"{/if} /> {$subcorpus.name}</label>
        	{/foreach}
    	</div>
	</div>

{if $annotation_set_id}
	<h1>Users</h1>
		<em>Only <i>agreement</i> annotations.</em>
		<table class="tablesorter" cellspacing="1" style="width: auto;">
			<tr>
				<th>Annotator name</th>
				<th title="Number of annotations">Anns</th>
				<th title="Number of documents with user's annotations">Docs</th>
				<th style="text-align: center">A</th>
				<th style="text-align: center">B</th>
			</tr>
			{if $annotators|@count == 0}
				{capture assign=message}
				<em>There are no agreement annotations for the selected criteria.</em> 
				{/capture}
				{include file="common_message.tpl"}			
			{else}
			{foreach from=$annotators item=a}
			<tr>
				<td style="line-height: 20px">{$a.screename}</td>
				<td style="line-height: 20px; text-align: right">{$a.annotation_count}</td>
				<td style="line-height: 20px; text-align: right">{$a.document_count}</td>
				<td><input type="radio" name="annotator_a_id" value="{$a.user_id}" {if $a.user_id == $annotator_a_id}checked="checked"{/if}/></td>
				<td><input type="radio" name="annotator_b_id" value="{$a.user_id}" {if $a.user_id == $annotator_b_id}checked="checked"{/if}/></td> 
			</tr>
			{/foreach}
			{/if}
		</table>
		
	<h1>Comparision mode</h1>
		<select name="comparision_mode">
			{foreach from=$comparision_modes key=k item=mode}
			<option value="{$k}" {if $k==$comparision_mode}selected="selected"{/if}>{$mode}</option>
			{/foreach}
		</select>		
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
		{*<th>Report id</th>*}
		<th style="text-align: center" colspan="5">Only A</th>
		<th style="text-align: center" colspan="5">A and B</th>
		<th style="text-align: center" colspan="5">Only B</th>
	</tr>
	{foreach from=$agreement.annotations key=ank item=an}
	{if $last_report_id != $an.report_id}
	<tr>
		<th colspan="15" style="text-align: center; background-color: #FFB347">Report {$an.report_id}</th>
	</tr>
	{assign var=last_report_id value=$an.report_id}
	{/if}
	<tr>
		{*<td>{$an.report_id}</td>*}
		
		{if array_key_exists($ank, $agreement.only_a)}
			<td>{$an.id}</td>
			<td>[{$an.from},{$an.to}]</td> 
			<td><em>{$an.text}</em></td>
			<td>{if $an.lemma}{$an.lemma}{else}<i>n/a</i>{/if}</td> 
			<td>[{$an.annotation_name}]</td>
		{else}
			<td colspan="5"></td>		
		{/if}
		
		{if array_key_exists($ank, $agreement.a_and_b)}
			<td style="background-color: #e5ffcc">{$agreement.annotations_a[$ank].id}<br/>{$agreement.annotations_b[$ank].id}</td>
			<td style="background-color: #e5ffcc">[{$an.from},{$an.to}]</td> 
			<td style="background-color: #e5ffcc"><em>{$an.text}</em></td>
			<td style="background-color: #e5ffcc">
				{if $agreement.annotations_a[$ank].lemma != $agreement.annotations_b[$ank].lemma}
				<span style="color: red">
					{if $agreement.annotations_a[$ank].lemma}{$agreement.annotations_a[$ank].lemma}{else}<i>n/a</i>{/if}
					<br/>
					{if $agreement.annotations_b[$ank].lemma}{$agreement.annotations_b[$ank].lemma}{else}<i>n/a</i>{/if}
				</span>
				{else}
					{if $an.lemma}{$an.lemma}{else}<i>n/a</i>{/if}
				{/if}
			</td>
			<td style="background-color: #e5ffcc">
				{if $agreement.annotations_a[$ank].annotation_name != $agreement.annotations_b[$ank].annotation_name}
				<span style="color: red">[{$agreement.annotations_a[$ank].annotation_name}]<br/>[{$agreement.annotations_b[$ank].annotation_name}]</span>
				{else}[{$an.annotation_name}]{/if}
			</td>
		{else}
			<td colspan="5" style="background-color: #e5ffcc"></td>		
		{/if}
		
		{if array_key_exists($ank, $agreement.only_b)}
			<td>{$an.id}</td>
			<td>[{$an.from},{$an.to}]</td> 
			<td><em>{$an.text}</em></td> 
			<td>{if $an.lemma}{$an.lemma}{else}<i>n/a</i>{/if}</td> 
			<td>[{$an.annotation_name}]</td>
		{else}
			<td colspan="5"></td>		
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
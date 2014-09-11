{if $wrong_changes}
	<div style="background: lightyellow; border: 1px solid #D9BB73; padding: 5px; margin: 5px;" id="content">
	{if $add_content}
	<h1>Error(s) in document content</h1>
	{else}
	<h1>Wrong changes</h1>
	{/if}
	<table cellspacing="1" class="table tablesorter" id="table-annotations" style="text-align: center; width: 99%">
		<tr>
		{if $wrong_annotations}
			<th>Details</th>
			<th>Id</th>
			<th>From</th>
			<th>To</th>
			<th>Type</th>
			<th>Text</th>
		{elseif $parse_error}
			<th>Line</th>
			<th>Column</th>
			<th>Description</th>
		{else}
			<td><h2>Changes in document</h2></td>
		{/if}		
		</tr>
		{if $wrong_annotations}
			{foreach from=$wrong_annotations item=c}
				<tr>
					<td style="color:red"> {$c.details}</td>
					<td>{$c.id}</td>
					<td>{$c.from}</td>
					<td>{$c.to}</td>
					<td>{$c.type}</td>
					<td class="annotations"><span class="{$c.type}">{$c.text}</span></td>
				</tr>
			{/foreach}
		{elseif $parse_error}
			{foreach from=$parse_error item=c}
				<tr>
					<td>{$c.line}</td>
					<td>{$c.col}</td>
					<td style="color:red"> {$c.description}</td>
				</tr>
			{/foreach}
		{else}
		<tr>
			<td style="vertical-align: top">
				<div class="annotations" style="border: 1px solid #777; background: white; padding: 5px; white-space: pre-wrap">{$document_changes}</div>
			</td>		
		</tr>
		{/if}
	</table>

       </div>
{/if}
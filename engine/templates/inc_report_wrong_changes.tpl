{if $wrong_changes}
	<div id="content">
		{if $wrong_annotations}
			<table cellspacing="1" class="table table-striped" id="table-annotations">
				<tr>
					<th>Details</th>
					<th>Id</th>
					<th>From</th>
					<th>To</th>
					<th>Type</th>
					<th>Text</th>
				</tr>
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
			</table>
		{elseif $parse_error}
			<table cellspacing="1" class="table table-striped" id="table-annotations">
				<tr>
					<th>Line</th>
					<th>Column</th>
					<th>Description</th>
				</tr>
				{foreach from=$parse_error item=c}
					<tr>
						<td>{$c.line}</td>
						<td>{$c.col}</td>
						<td style="color:red"> {$c.description}</td>
					</tr>
				{/foreach}
			</table>
		{else}
			<div class="panel panel-danger">
				<div class="panel-heading">Changes in the document</div>
				<div class="panel-body annotations" style="border: 1px solid #777; background: white; padding: 0; white-space: pre-wrap">{$document_changes}</div>
			</div>
		{/if}
	</div>
{/if}
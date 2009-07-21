{include file="inc_header.tpl"}
{include file="inc_menu.tpl"}

<td class="table_cell_content">

<h1>Backup</h1>

{if $output}
<div class="info">Backup został wykonany</div>
{/if}

<div>
	<ul>
	{foreach from=$files item=line}
	<li><a href="{$line.file}">{$line.file}</a> {$line.size}</li>
	{/foreach}
	</ul>
	
	<form method="POST">
		<input type="submit" name="backup" value="wykonaj backup"/> &mdash; ta operacja długo trwa!!
	</form>
</div>

{if $display}
<hr/>
<h2>{$file}</h2>
<pre style="padding: 5px; border: 1px solid orange; margin: 5px; ">
	{$display_content}
</pre>
{/if}

<br style="clear: both"/>
</td>

{include file="inc_footer.tpl"}
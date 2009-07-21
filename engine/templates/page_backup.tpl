{include file="inc_header.tpl"}
{include file="inc_menu.tpl"}
{include file="inc_filter.tpl"}

<h1>Backup</h1>

{if $output}
<div class="info">Backup został wykonany</div>
{/if}

<div>
	<ul>
	{foreach from=$files item=line}
	<li><a href="index.php?page=backup&amp;file={$line}">{$line}</a></li>
	{/foreach}
	</ul>
	
	<form method="POST">
		<input type="submit" name="backup" value="wykonaj backup"/>
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

{include file="inc_footer.tpl"}
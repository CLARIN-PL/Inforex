{include file="inc_header.tpl"}
{if $view!='full'}
	{include file="inc_menu.tpl"}
{/if}

<td class="table_cell_content">

{*
<div style="float: right; margin: 5px;">
<form method="post" action="index.php?page=report&amp;id={$row.id}" style="display: inline">
Typ zdarzenia: {$select_type}; Status: {$select_status} <input type="submit" value="zapisz" name="zapisz"/>
</form>
{if ($row.status==1)}
 | <form method="post" action="index.php?page=report&amp;id={$row.id}" style="display: inline">
	<input type="submit" value="OK" name="zapisz" id="accept"/>
	<input type="hidden" value="{$row.type}" name="type"/> 
	<input type="hidden" value="2" name="status"/> 
</form>
{/if}
*}
</div>

{*
<form>
	<input type="button" value="sprawdzony" style="border: 3px solid rgb(76,127,0); background: rgb(132,190,45); color: white; padding: 5px;">
</form>
*}

<div style="float: right">
	{if $view=="full"}
	<a href="index.php?page=report&amp;id={$row.id}&amp;view=noraml">normalny widok</a>	
	{else}
	<a href="index.php?page=report&amp;id={$row.id}&amp;view=full">pełny ekran</a>
	{/if}
</div>
	
<div style="text-align: left">	 
	{if $row_prev}
		{$row_prev_c} <a id="article_prev" href="index.php?page=report&amp;id={$row_prev}"><< poprzedni</a>
	{else}poprzedni{/if}
	| 
	{if $row_next}
		<a id="article_next" href="index.php?page=report&amp;id={$row_next}">następny >></a> {$row_next_c}
	{else}następny{/if}
	 
</div>

<div class="basictab_box">
	<ul class="basictab">
		<li{if $subpage=='preview'} class="selected"{/if}><a href="index.php?page=report&amp;subpage=preview&amp;id={$row.id}">Tekst</a></li>
		<li{if $subpage=='html'} class="selected"{/if}><a href="index.php?page=report&amp;subpage=html&amp;id={$row.id}">HTML</a></li>
		<li{if $subpage=='raw'} class="selected"{/if}><a href="index.php?page=report&amp;subpage=raw&amp;id={$row.id}">Źródłowy dokument</a></li>
		<li{if $subpage=='edit'} class="selected"{/if}><a href="index.php?page=report&amp;subpage=edit&amp;id={$row.id}">Edycja</a></li>
		<li{if $subpage=='edit_raw'} class="selected"{/if}><a href="index.php?page=report&amp;subpage=edit_raw&amp;id={$row.id}">Edycja / źródło</a></li>
	</ul>
</div>

<div>
	{if $subpage=='edit'}
		{include file="$subpage_file"}	
	{else}
	<table id="report">
		<tr>
			<th>Status:</th>
			<td><i>{$row.status_name}</i></td>
		</tr>
		<tr>
			<th>Typ:</th>
			<td><i>{$row.type_name}</i></td>
		</tr>
		<tr>
			<th>Tytuł:</th>
			<td><b>{$row.title}</b></td>
		</tr>
		<tr>
			<th>Firma:</th>
			<td>{$row.company}</td>
		</tr>
		<tr>
			<th>Treść</t>
			<td>{include file="$subpage_file"}</td>
		</tr>
	</table>
	{/if}
</div>
</td>

{include file="inc_footer.tpl"}
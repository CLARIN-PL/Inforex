{include file="inc_header.tpl"}
{if $view!='full'}
	{include file="inc_menu.tpl"}
{/if}

<td class="table_cell_content">

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

<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
	<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		<li class="ui-state-default ui-corner-top {if $subpage=='preview'}ui-state-active ui-tabs-selected{/if}"><a href="index.php?page=report&amp;subpage=preview&amp;id={$row.id}">Tekst</a></li>
		<li class="ui-state-default ui-corner-top {if $subpage=='html'}ui-state-active ui-tabs-selected{/if}"><a href="index.php?page=report&amp;subpage=html&amp;id={$row.id}">HTML</a></li>
		<li class="ui-state-default ui-corner-top {if $subpage=='raw'}ui-state-active ui-tabs-selected{/if}"><a href="index.php?page=report&amp;subpage=raw&amp;id={$row.id}">Źródłowy dokument</a></li>
	{if !$IS_RELEASE}	
		<li class="ui-state-default ui-corner-top {if $subpage=='edit'}ui-state-active ui-tabs-selected{/if}"><a href="index.php?page=report&amp;subpage=edit&amp;id={$row.id}">Edycja</a></li>
		<li class="ui-state-default ui-corner-top {if $subpage=='edit_raw'}ui-state-active ui-tabs-selected{/if}"><a href="index.php?page=report&amp;subpage=edit_raw&amp;id={$row.id}">Edycja / źródło</a></li>
		<li class="ui-state-default ui-corner-top {if $subpage=='annotator'}ui-state-active ui-tabs-selected{/if}"><a href="index.php?page=report&amp;subpage=annotator&amp;id={$row.id}">Adnotacja</a></li>
	{/if}
	</ul>

	<div>
		{if $subpage=='edit' || $subpage=='edit_raw' || $subpage=='annotator'}
			{include file="$subpage_file"}	
		{else}
		<br/>
		<div class="ui-widget ui-widget-content ui-corner-all" style="margin: 5px">			
		<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Meta dane raportu</div>		
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
			</table>
		</div>
		<br/>
		{include file="$subpage_file"}
		{/if}
	</div>
</div>
</td>

{include file="inc_footer.tpl"}
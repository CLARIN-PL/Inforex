{include file="inc_header.tpl"}
{include file="inc_menu.tpl"}

<td style="vertical-align: top; background: ; border: 1px solid rgb(68, 68, 68); background: linen">
<div class="filter_menu">
	 		
	<div class="total_count"><small>liczba raportów:</small><br/>{$total_count}</div>
	<h2>Szukaj</h2>
	<form action="index.php?page=browse">
		<input type="text" name="search" value="{$search}" style="width: 150px"/>
		<input type="hidden" name="page" value="browse"/> 
		<input type="submit" value="szukaj"/>
	</form>
	{if $search}
		<small><a href="index.php?page=browse&amp;search=">anuluj</a></small>
	{/if}

	<h2>Status</h2>
	<ul>
	{foreach from=$statuses item="status"}
		<li><a href="index.php?page=browse&amp;status={$status.link}"{if $status.selected} class="selected"{/if}>{$status.name|default:"<i>brak</i>"}</a>&nbsp;({$status.count})</li>
	{/foreach}
	</ul>
	
	<h2>Typ zdarzenia</h2>
	<ul>
	{foreach from=$types item="type"}
		<li><a href="index.php?page=browse&amp;type={$type.link}"{if $type.selected} class="selected"{/if}>{$type.name|default:"<i>brak</i>"}</a>&nbsp;({$type.count})</li>
	{/foreach}
	</ul>
	
	<h2>Rok</h2>
	<ul>
	{foreach from=$years item="year"}
		<li><a href="index.php?page=browse&amp;year={$year.link}"{if $year.selected} class="selected"{/if}>{$year.year}</a>&nbsp;({$year.count})</li>
	{/foreach}
	</ul>
	<h2>Miesiąc</h2>
	<ul>
	{foreach from=$months item="month"}
		<li><a href="index.php?page=browse&amp;month={$month.link}"{if $month.selected} class="selected"{/if}>{$month.month}</a>&nbsp;({$month.count})</li>
	{/foreach}
	</ul>

	<h2>Adnotacje</h2>
	<ul>
	{foreach from=$annotations item="annotation"}
		<li><a href="index.php?page=browse&amp;annotation={$annotation.link}"{if $annotation.selected} class="selected"{/if}>{$annotation.name}</a>&nbsp;({$annotation.count})</li>
	{/foreach}
	</ul>
</div>

</td>

<td class="table_cell_content" style="width: 1000px">

<form method="POST" action="index.php?page={$page}">
	Wyrażenie regularne: 
	<input type="text" name="regex" value="{$regex}"/>
	<input type="submit" name="regex_search" value="Szukaj"/>
	<input type="hidden" name="page" value="{$page}"/>
</form>

{if $total_match_count}
Liczba znalezionych dopasowań: <b>{$total_match_count}</b><br/>
Liczba dokumentów z dopasowaniami: <b>{$matched_report_count}</b>
{/if}

{foreach from=$items item="item"}
<hr/>
<div>
{$item.content}
</div>
{/foreach}

</td>

{include file="inc_footer.tpl"}
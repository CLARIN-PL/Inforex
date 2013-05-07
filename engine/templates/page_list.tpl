{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}
{include file="inc_menu.tpl"}
{include file="inc_filter.tpl"}

<td>
<h1><a href="index.php?page=list_total">Raporty</a> &raquo; {$year}-{$month}</h1>

<div id="pagging">
{section name=foo loop=$pages}
    <a {if $p==$smarty.section.foo.iteration-1} class="active"{/if}href="index.php?page=list&amp;year={$year}&amp;month={$month}&amp;p={$smarty.section.foo.iteration-1}">{$smarty.section.foo.iteration}</a>
{/section}
</div>

<table style="width: 100%">
	<tr style="border: 1px solid #999;">
		<th>Lp.</th>
		<th>#</th>
		<th>Numer</th>
		<!--<th>Firma</th>-->
		<th>Nazwa&nbsp;raportu</th>
		<th>Typ&nbsp;raportu</th>
		<th>Status</th>
		<th colspan="2"> </th>
	</tr>
{foreach from=$rows item=r name=list}
	<tr class="row_{if ($smarty.foreach.list.index%2==0)}even{else}odd{/if}{if $r.formated==1}_formated{elseif $r.status==2}_ok{/if}">
		<td style="text-align: right">{$smarty.foreach.list.index+$from}.</td>
		<td>#{$r.id}</td>
		<td>{$r.number}</td>
		<!--<td>{$r.company}</td>-->
		<td><a href="index.php?page=report&amp;id={$r.id}">{$r.title}</a></td>
		<td style="{if $r.type==1}color: #777;{/if}; text-align: center;">{$r.type_name|default:"---"}</td>
		<td style="{if $r.status==1}color: #777;{/if}; text-align: center;">{$r.status_name|default:"---"}</td>
		<td>{if $r.status==2}<div style="width: 10px; height: 10px; background: #3366FF"> </div>
			{else}<div style="width: 10px; height: 10px; background: #ddd"> </div>{/if}</td>
		<td>{if $r.formated==1}<div style="width: 10px; height: 10px; background: orange"> </div>
			{else}<div style="width: 10px; height: 10px; background: #ddd"> </div>{/if}</td>
	</tr>
{/foreach}
</table>

{include file="inc_footer.tpl"}
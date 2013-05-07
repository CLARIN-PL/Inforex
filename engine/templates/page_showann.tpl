{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<td class="table_cell_content">

<h1>Przeglądarka anotacji</h1>

<ul>
{foreach from=$sentences item=i}
<li><b>#<a href="?page=report&amp;corpus={$cid}&amp;id={$i.report_id}">{$i.report_id}</a></b><br/>{$i.html}</li>
{/foreach}
</ul>

{include file="inc_footer.tpl"}
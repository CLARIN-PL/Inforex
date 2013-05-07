{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}
<h1>Ustawienia filtrowania</h1>
<hr/>
<form method="post" action=".">
<h2>Status raportu</h2>
<ul>
{foreach from=$statuses item=status}
<li><input type="checkbox" name="statuses[]" value="{$status.id}" style="vertical-align: middle"/> {$status.status}<br/><span>{$status.description}</span></li>
{/foreach}
</ul>

<h2>Rodzaj raportu</h2>
<ul>
{foreach from=$types item=type}
<li><input type="checkbox" name="types[]" value="{$type.id}" style="vertical-align: middle"/> {$type.name}</li>
{/foreach}
</ul>
<input type="submit" value="Zapisz" />
</form>
{include file="inc_footer.tpl"}

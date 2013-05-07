{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<td class="table_cell_content">

	{include file="inc_system_messages.tpl"}

	<h1>Import tekstu</h1>
	<form method="POST">
		Url: <input type="text" name="url" style="width: 400px" value="{$url}"/> <input type="submit" value="Importuj"/>
	</form>
	<br/>
</td>

{include file="inc_footer.tpl"}

{include file="inc_header.tpl"}

<td class="table_cell_content">

	<h1>Przypisane role:</h1>
	<ul>
	{foreach from=$user.role item=description key=role}
		<li><b>{$role}</b> &mdash; {$description}</li>
	{foreachelse}
		<li><i>brak</i></li>
	{/foreach}
	</ul>
</td>

{include file="inc_footer.tpl"}

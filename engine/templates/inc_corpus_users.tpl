{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<table class="tablesorter" cellspacing="1" id="corpus_update" style="width: 300px; margin: 10px">
	<thead>
		<tr>
			<th>User</th>
            <th>Login</th>
			<th style="text-align: center">Assign</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$users_in_corpus item=user}
		<tr>
			<td>{$user.screename}</td>
			<td>{$user.login}</td>
			<td style="text-align: center"><input {if $user.role}checked="checked"{/if} class="userInCorpus" type="checkbox" element_type="users" value="{$user.user_id}" /></td>
		</tr>
		{/foreach}
	</tbody>
</table>
<table class="tablesorter" cellspacing="1" id="corpus_set_corpus_role" style="width: 300px; margin: 10px">
	<thead>
		<tr>
			<th style="background: white"></th>
			{foreach from=$corpus_roles item=role}
				<th style="text-align: center">{$role.description}</th>
			{/foreach}
		</tr>
	</thead>
	<tbody>
		{foreach from=$users_roles item=user}		
			<tr>
				<th>{$user.screename}</th>
				{foreach from=$corpus_roles item=role}
				<td style="text-align: center; {if $user.role|@contains:$role.role} background: #9DD943;{/if}">
					<input {if $user.role|@contains:$role.role}checked="checked"{/if} class="corpusRole" type="checkbox" user_id="{$user.user_id}" role="{$role.role}" value="1"/>
				</td>
				{/foreach}
			</tr>
		{/foreach}
	</tbody>
</table>
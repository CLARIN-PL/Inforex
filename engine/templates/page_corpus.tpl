{include file="inc_header.tpl"}

<td class="table_cell_content">

	{include file="inc_system_messages.tpl"}

	<h1>Informacje o korpusie</h1>
	
	<i>Nazwa: </i><b>{$corpus.name}</b>
	
	{if $users_roles}
	<h1>Dostęp użytkowników</h1>
	<form method="POST" action="index.php?page=corpus&amp;corpus={$corpus.id}">
	<input type="hidden" name="action" value="corpus_role_update"/>
	<table class="formated" cellspacing="1">
		<tr>
			<th></th>
			<th>Odczyt</th>
			<th>Anotacja</th>
			<th>Dodawanie<br/>dokumentów</th>
		</tr>
		<tr>
			<td><i>Właściciel:</i> <b>{$owner.screename}</b></td>
			<td style="text-align: center; background: #9DD943">
				<input type="checkbox" readonly="readonly" checked="checked"/>
			</td> 
			<td style="text-align: center; background: #9DD943">
				<input type="checkbox" readonly="readonly" checked="checked"/>
			</td> 
			<td style="text-align: center; background: #9DD943">
				<input type="checkbox" readonly="readonly" checked="checked"/>
			</td> 
		</tr>
		{foreach from=$users_roles item=user}		
			<tr>
				<th>{$user.screename}</th>
				<td style="text-align: center; {if $user.role|@contains:"read"} background: #9DD943;{/if}">
					<input type="checkbox" name="role[{$user.user_id}][read]" value="1" {if $user.role|@contains:"read"} checked="checked"{/if}/>
				</td>
				<td style="text-align: center; {if $user.role|@contains:"annotate"} background: #9DD943;{/if}">
					<input type="checkbox" name="role[{$user.user_id}][annotate]" value="1" {if $user.role|@contains:"annotate"} checked="checked"{/if}/>
				</td>
				<td style="text-align: center; {if $user.role|@contains:"add_documents"} background: #9DD943;{/if}">
					<input type="checkbox" name="role[{$user.user_id}][add_documents]" value="1" {if $user.role|@contains:"add_documents"} checked="checked"{/if}/>
				</td>
			</tr>
		{/foreach}
		<tr>
			<td colspan="4" style="text-align: right; background: #444"><input type="submit" value="Zapisz"/></td>		
		</tr>
	</table>
	</form>
	{/if}
	<br/>
</td>

{include file="inc_footer.tpl"}

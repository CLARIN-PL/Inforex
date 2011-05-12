{include file="inc_header.tpl"}

<td class="table_cell_content">

	{include file="inc_system_messages.tpl"}

	<h1>Informacje o korpusie</h1>
	
	<i>Nazwa: </i><b>{$corpus.name}</b>
	<h1>Options</h1>
	<div id="corpusId" style="display:none">{$corpus.id}</div>
	<a href="#" id="annotationSets" style="padding:0px 10px 0px 0px">Annotation sets</a>
	<a href="#" id="eventGroups" style="padding:0px 10px 0px 0px">Event groups</a>
	<a href="#" id="reportPerspectives" style="padding:0px 10px 0px 0px">Report perspectives</a>
	
		
	
	{if $users_roles}
	<h1>Dostęp użytkowników</h1>
	<form method="POST" action="index.php?page=corpus&amp;corpus={$corpus.id}">
	<input type="hidden" name="action" value="corpus_role_update"/>
	<table class="tablesorter" cellspacing="1">
		<tr>
			<th style="background: white"></th>
			{foreach from=$corpus_roles item=role}
			<th style="text-align: center">{$role.description}</th>
			{/foreach}
			<th style="text-align: center">Report perspectives access</th>			
		</tr>
		<tr>
			<th style="background:  background: #9DD943;"><i>Właściciel:</i> <b>{$owner.screename}</b></th>
			{foreach from=$corpus_roles item=role}
			<td style="text-align: center; background: #9DD943">
				<input type="checkbox" readonly="readonly" checked="checked"/>
			</td> 
			{/foreach}
			<td style="text-align: center"><a href="#" class="userReportPerspectives" userid="{$owner.user_id}">details</a></td>
		</tr>
		{foreach from=$users_roles item=user}		
			<tr>
				<th>{$user.screename}</th>
				{foreach from=$corpus_roles item=role}
				<td style="text-align: center; {if $user.role|@contains:$role.role} background: #9DD943;{/if}">
					<input type="checkbox" name="role[{$user.user_id}][{$role.role}]" value="1" {if $user.role|@contains:$role.role} checked="checked"{/if}/>
				</td>
				{/foreach}
				<td style="text-align: center"><a href="#" class="userReportPerspectives" userid="{$user.user_id}">details</a></td>
			</tr>
		{/foreach}
		<tr>
			<td colspan="{$corpus_roles_span}" style="text-align: right; background: #444"><input type="submit" value="Zapisz"/></td>
			<td style="display:none"></td>		
		</tr>
	</table>
	</form>
	{/if}
	<br/>
</td>

{include file="inc_footer.tpl"}

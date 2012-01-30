{include file="inc_header.tpl"}

<td class="table_cell_content">

	{include file="inc_system_messages.tpl"}

	<h1>Informacje o korpusie</h1>
	
	<i>Nazwa: </i><b>{$corpus.name}</b><br/>
	<i>Właściciel:</i> <b>{$owner.screename}</b> --- posiada pełny dostęp</th>
	
	<h1>Options</h1>
	<div id="corpusId" style="display:none">{$corpus.id}</div>
	<a href="#" id="annotationSets" style="padding:0px 10px 0px 0px">Annotation sets</a>
	<a href="#" id="eventGroups" style="padding:0px 10px 0px 0px">Event groups</a>
	<a href="#" id="reportPerspectives" style="padding:0px 10px 0px 0px">Report perspectives</a>
	
	<br/>
		
	{if $users_roles}
	<h1>Dostęp użytkowników</h1>
	<a href="#" id="usersInCorpus" style="padding:0px 10px 0px 0px">Dodaj/usuń użytkowników z projektu</a>
	<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all" style="background: #f3f3f3; margin-bottom: 5px; position: relative">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-state-active ui-tabs-selected" id="roles">
				<a href="#">Roles</a>
			</li>
			<li class="ui-state-default ui-corner-top" id="perspectives">
				<a href="#">Perspectives</a>
			</li>		
		</ul>
	
		<div id="roles">
			<form method="POST" action="index.php?page=corpus&amp;corpus={$corpus.id}">
			<input type="hidden" name="action" value="corpus_role_update"/>
			<table class="tablesorter" cellspacing="1">
				<tr>
					<th style="background: white"></th>
					{foreach from=$corpus_roles item=role}
						<th style="text-align: center">{$role.description}</th>
					{/foreach}
		{*			<th style="text-align: center">Report perspectives access</th>			
		*}		</tr>
				{foreach from=$users_roles item=user}		
					<tr>
						<th>{$user.screename}</th>
						{foreach from=$corpus_roles item=role}
						<td style="text-align: center; {if $user.role|@contains:$role.role} background: #9DD943;{/if}">
							<input type="checkbox" name="role[{$user.user_id}][{$role.role}]" value="1" {if $user.role|@contains:$role.role} checked="checked"{/if}/>
						</td>
						{/foreach}
	{*					<td style="text-align: center"><a href="#" class="userReportPerspectives" userid="{$user.user_id}">details</a></td>
	*}				</tr>
				{/foreach}
				<tr>
					<td colspan="{$corpus_roles_span}" style="text-align: right; background: #444"><input type="submit" value="Zapisz"/></td>
					<td style="display:none"></td>		
				</tr>
			</table>
			</form>
		</div>
		<div id="perspectives" style="display:none">
			<table class="tablesorter" cellspacing="1">
				<tr class="thead">
					<th style="background: white"></th>
					{foreach from=$corpus_perspectivs key=id item=perspectiv}
						<th perspective_id="{$id}" style="text-align: center">{$perspectiv.title}</th>
					{/foreach}
				</tr>
				{foreach from=$users_roles key=user_id item=user}		
					<tr class="tbody" id={$user_id}>
						<th>{$user.screename}</th>
						{foreach from=$corpus_perspectivs key=perspectiv item=perspectiv_data}
							{if $perspectiv_data.access eq 'role'}
								<td perspective_id="{$perspectiv}" style="text-align: center;{if in_array($perspectiv, $users_perspectives.$user_id)} background: #9DD943;{/if}">
									<input class="setUserReportPerspective" type="checkbox" userid="{$user_id}" perspective_id="{$perspectiv}" value="1" {if in_array($perspectiv, $users_perspectives.$user_id)}checked="checked"{/if}/>
							{else}
								<td perspective_id="{$perspectiv}" style="text-align: center;">
								<i>{$perspectiv_data.access}</i>
							{/if}																	 							
							</td>	
						{/foreach}
					</tr>
				{/foreach}
			</table>
			</form>
		</div>
	</div>
	{/if}
	<br/>
</td>

{include file="inc_footer.tpl"}
{include file="inc_header.tpl"}

<td class="table_cell_content">

	{include file="inc_system_messages.tpl"}

	<div style="float: left">
		<h1>Basic information</h1>
		
		<table class="tablesorter" cellspacing="1" style="width: 300px">
			<tr>
				<th>Name: </th>
				<td>{$corpus.name}</td>
			</tr>
			<tr>		
				<th>Owner:</th> 
				<td>{$owner.screename}</td>
			</tr>
			<tr>		
				<th>Access:</th> 
				<td>{if $corpus.public}public{else}restricted{/if}</td>
			</tr>
		</table>
	 </div>
	
	<div style="margin-left: 320px">
		<h1>Corpus elements</h1>
		<div id="corpusId" style="display:none">{$corpus.id}</div>
		<ul>
			<li><a href="#" id="reportPerspectives" style="padding:0px 10px 0px 0px">Report perspectives</a> &mdash; select perspectives that can be access by users,</li>
			<li><a href="#" id="annotationSets" style="padding:0px 10px 0px 0px">Annotation sets</a> &mdash; select annotation sets available in the Semantic Annotator,</li>
			<li><a href="#" id="eventGroups" style="padding:0px 10px 0px 0px">Event groups</a></li>
		</ul>
	</div>
	
	<br style="clear: both"/>
		
	{if $users_roles}
	<h1>User access</h1>
	<a href="#" id="usersInCorpus" style="border: 1px solid #999; margin: 4px; padding: 4px; display: block; width: 200px; background: #eee">Manage users access to the corpora</a>
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
			<table class="tablesorter" cellspacing="1">
				<tr>
					<th style="background: white"></th>
					{foreach from=$corpus_roles item=role}
						<th style="text-align: center">{$role.description}</th>
					{/foreach}
				</tr>
				{foreach from=$users_roles item=user}		
					<tr>
						<th>{$user.screename}</th>
						{foreach from=$corpus_roles item=role}
						<td style="text-align: center; {if $user.role|@contains:$role.role} background: #9DD943;{/if}">
							<input class="setCorpusRole" type="checkbox" userid="{$user.user_id}" role="{$role.role}" value="1" {if $user.role|@contains:$role.role} checked="checked"{/if}/>
						</td>
						{/foreach}
					</tr>
				{/foreach}
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
								{if isset($users_perspectives.$user_id)}
									<td perspective_id="{$perspectiv}" style="text-align: center;{if in_array($perspectiv, $users_perspectives.$user_id)} background: #9DD943;{/if}">
										<input class="setUserReportPerspective" type="checkbox" userid="{$user_id}" perspective_id="{$perspectiv}" value="1" {if in_array($perspectiv, $users_perspectives.$user_id)}checked="checked"{/if}/>
								{else}
									<td perspective_id="{$perspectiv}" style="text-align: center;">
										<input class="setUserReportPerspective" type="checkbox" userid="{$user_id}" perspective_id="{$perspectiv}" value="1" />
								{/if}
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
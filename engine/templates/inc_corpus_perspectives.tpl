{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<input type"button" class="button" id="reportPerspectives" value="Add/remove perspectives"/>

<h2>Access to the perspectives</h2>
 
<table class="tablesorter" cellspacing="1" id="corpus_set_corpus_perspective_roles" style="width: auto;">
	<thead>
		<tr>
			<th style="background: white; text-align: center">User</th>
			{foreach from=$corpus_perspectivs key=id item=perspectiv}
				<th perspective_id="{$id}" style="text-align: center; width: 100px;">{$perspectiv.title}</th>
			{/foreach}
		</tr>
	</thead>
	<tbody>
	    {*
	    <tr id={$user_id}>
            <th>owner</th>
            {foreach from=$corpus_perspectivs key=perspectiv item=perspectiv_data}
            <td style="text-align: center;">
            <input class="userReportPerspective" type="checkbox" value="1" readonly="readonly" checked="checked"/>
            </td>
	        {/foreach}
	    </tr>
	    *}
		{foreach from=$users_roles key=user_id item=user}		
			<tr id={$user_id}>
				<th>{$user.screename}</th>
				{foreach from=$corpus_perspectivs key=perspectiv item=perspectiv_data}
					{if $perspectiv_data.access eq 'role'}
						{if isset($users_perspectives.$user_id)}
							<td perspective_id="{$perspectiv}" style="text-align: center;{if in_array($perspectiv, $users_perspectives.$user_id)} background: #9DD943;{/if}">
								<input {if in_array($perspectiv, $users_perspectives.$user_id)}checked="checked"{/if} class="userReportPerspective" type="checkbox" user_id="{$user_id}" perspective_id="{$perspectiv}" value="1" />
						{else}
							<td perspective_id="{$perspectiv}" style="text-align: center;">
								<input class="userReportPerspective" type="checkbox" user_id="{$user_id}" perspective_id="{$perspectiv}" value="1" />
						{/if}
					{else}
						<td perspective_id="{$perspectiv}" style="text-align: center;">
						<i>{$perspectiv_data.access}</i>
					{/if}																	 							
					</td>	
				{/foreach}
			</tr>
		{/foreach}
	</tbody>
</table>

{if $users_roles|@count == 0}
<div>
<i>No other users have access to this corpus (<a href="index.php?page=corpus&amp;corpus={$corpus.id}&amp;subpage=users">manage users</a>).</i>
</div>
{/if}

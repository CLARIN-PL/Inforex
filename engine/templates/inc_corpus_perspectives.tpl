{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<div style="margin: 20px">


</div>
<table class="tablesorter" cellspacing="1" id="corpus_set_corpus_perspective_roles" style="width: 99%; margin: 10px">
	<thead>
		<tr>
			<th style="background: white">
			     <a href="#" id="reportPerspectives" title="Select perspectives that can be access by users">Assign/remove perspectives</a>
			</th>
			{foreach from=$corpus_perspectivs key=id item=perspectiv}
				<th perspective_id="{$id}" style="text-align: center">{$perspectiv.title}</th>
			{/foreach}
		</tr>
	</thead>
	<tbody>
	    <tr id={$user_id}>
            <th>owner</th>
            {foreach from=$corpus_perspectivs key=perspectiv item=perspectiv_data}
            <td style="text-align: center;">
            <input class="userReportPerspective" type="checkbox" value="1" readonly="readonly" checked="checked"/>
            </td>
	        {/foreach}
	    </tr>
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
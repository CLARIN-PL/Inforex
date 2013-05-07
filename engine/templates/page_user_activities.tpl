{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<h1>User activities</h1>

<div style="width: 800px"
	<table id="user_activities" class="display" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th>Username</th>
				<th>Logged in</th>
				<th>Started</th>
				<th>Ended</th>
				<th>Duration <br/><small>[minutes]</small></th>
				<th>Actions</th>
				<th>Avg. inervals <br/><small>[minutes]</small></th>
			</tr>
		</thead>
		<tbody>
	{foreach from=$activities item=a}
		<tr>
			<td>{$a.screename}</td>
			<td style="text-align: center">{if $a.login}yes{else}no{/if}</td>		
			<td style="text-align: center">{$a.started}</td>
			<td style="text-align: center">{$a.ended}</td>
			<td style="text-align: right">{$a.duration}</td>
			<td style="text-align: right">{$a.counter}</td>
			<td style="text-align: center">
				{if $a.counter==0}
					0
				{else} 
					{math equation="y / x" x=$a.counter y=$a.duration format="%.2f"}
				{/if}
			</td>		
		</tr>
	{/foreach}
		</tbody>
	</table>
</div>

{include file="inc_footer.tpl"}

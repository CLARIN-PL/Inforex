{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<table style="width: 100%">
	<tr>
	<td style="width: 50%; vertical-align: top; padding-right: 10px">
{if $corpus_public}
	<h2>Public corpora</h2>
	<table class="tablesorter" id="public" cellspacing="1">
		<thead>
		<tr>
	        <th style="text-align: left; width: 25px">ID</th>
			<th style="text-align: left; width: 150px">Name</th>
			<th style="text-align: left">Description</th>
			<th style="text-align: right; width: 50px">Documents</th>
		</tr>
		</thead>
		<tbody>
	    {foreach from=$corpus_public item=corpus}
	    <tr>
	        <td style="color: grey; text-align: right">{$corpus.id}</td>
			<td><a href="?corpus={$corpus.id}&amp;page=start">{$corpus.name}</a></td>
			<td>{$corpus.description}</td>
			<td style="text-align: right">{$corpus.reports}</td>
		</tr>
		{/foreach}
		</tbody>
	</table>
</div>
{/if}
	</td>
		<td style="width: 50%; vertical-align: top">
	<h2>Your corpora</h2>
{if $corpus_private}
	<table class="tablesorter" id="user_corpora" cellspacing="1">
		<thead>
		<tr>
	        <th style="text-align: left; width: 25px">ID</th>
			<th style="text-align: left; width: 150px">Name</th>
			<th style="text-align: left">Description</th>
			<th style="text-align: right; width: 50px">Documents</th>
		</tr>
		</thead>
		<tbody>
	    {foreach from=$corpus_private item=corpus}
	    {if $corpus.user_id == $user.user_id}
	    <tr>
	        <td style="color: grey; text-align: right">{$corpus.id}</td>
			<td><a href="?corpus={$corpus.id}&amp;page=start">{$corpus.name}</a></td>
			<td>{$corpus.description}</td>
			<td style="text-align: right">{$corpus.reports}</td>
		</tr>
		{/if}
		{/foreach}
		</tbody>	    
	</table>
	
	{if "admin"|has_role || "create_corpus"|has_role}
	<div style="clear: both;">
	    <button type="button" class="button add_corpora_button">Create a new corpus</button>
	</div>
	{/if}
	
    <h2>Other private corpora you have access to</h2>
    <table class="tablesorter" id="restricted" cellspacing="1">
    	<thead>
        <tr>
            <th style="text-align: left; width: 25px">ID</th>
            <th style="text-align: left; width: 150px">Name</th>
            <th style="text-align: left">Description</th>
            <th style="text-align: right; width: 50px">Documents</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$corpus_private item=corpus}
        {if $corpus.user_id != $user.user_id}
        <tr>
            <td style="color: grey; text-align: right">{$corpus.id}</td>
            <td><a href="?corpus={$corpus.id}&amp;page=start">{$corpus.name}</a></td>
            <td>{$corpus.description}</td>
            <td style="text-align: right">{$corpus.reports}</td>
        </tr>
        {/if}
        {/foreach}
        </tbody>      
    </table>
{else}
	<div class="infobox-light">
		{if !$user_id}
			<a href="#" class="login_link">Log in</a> to see the list.
		{else}
			No corpora available.
		{/if}
	</div>
{/if}
</td>

</tr>
</table>

<br style="clear: both"/>

{include file="inc_footer.tpl"}

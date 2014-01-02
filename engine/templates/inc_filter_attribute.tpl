{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<div class="filter_box">
	{assign var="is_any_inactive" value="0"}
	{capture name=filter_box}
		<ul>
		{foreach from=$attribute_options item="row"}
			<li{if $row.selected} class="active"{/if}>
				<span class="num">&nbsp;{$row.count}</span>
				[<a href="index.php?page=browse&amp;corpus={$corpus.id}&amp;{$filter_type}={$row.link}">{if $row.selected}&ndash;{else}+{/if}</a>]					
				<a href="index.php?page=browse&amp;corpus={$corpus.id}&amp;{$filter_type}={$row.id}&amp;filter_order={$row.filter_order}">{$row.name|default:"<i>brak</i>"}</a>
			</li>
			{if !$row.selected}{assign var="is_any_inactive" value="1"}{/if}
		{/foreach}
		</ul>
	{/capture}
	
	{if $is_any_inactive}
		<a class="cancel" href="index.php?page=browse&amp;corpus={$corpus.id}&amp;{$filter_type}="><small class="toggle">cancel</small>
	{else}
		<a class="toggle_simple" label="#filter_{$filter_type}" href="#">
	{/if}
		<span {if $is_any_inactive}class="active"{/if}>{$filter_type|capitalize}</span>
	</a>
	
	<div id="filter_{$filter_type}" {if !$is_any_inactive}style="display: none"{/if}> 
	{$smarty.capture.filter_box}
	</div>
</div>

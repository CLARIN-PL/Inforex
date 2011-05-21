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
		<a class="toggle_simple" label="#filter_{$filter_type}" href=""><small class="toggle">show/hide</small>
	{/if}
		<h2 {if $is_any_inactive}class="active"{/if}>{$filter_type|capitalize}</h2>
	</a>
	
	<div id="filter_{$filter_type}" {if !$is_any_inactive}style="display: none"{/if}> 
	{$smarty.capture.filter_box}
	</div>
</div>

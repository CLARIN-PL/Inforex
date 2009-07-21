<div id="filter_box">
Status raportu:
{assign var=count value=0}
<ul>
{foreach from=$statuses item=s}
	{assign var=count value=$count+$s.count}
	<li{if $status==$s.status_id} class="active"{/if}><a href="index.php?action=status_set&amp;page={$page}&amp;status={$s.status_id}">{$s.status_name} ({$s.count})</a></li>
{/foreach}
	<li{if !$status} class="active"{/if}><a href="index.php?action=status_set&amp;page={$page}">wszystkie ({$count})</a></li>
</ul>
</div>
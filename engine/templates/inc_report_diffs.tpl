<ul>
{foreach from=$diffs item=diff}
	<li>
		Modified on <b>{$diff.datetime}</b> by <b>{$diff.screename}</b>
		{if $diff.diff_raw|strip_tags|trim != ""}
		<pre style="border:1px solid #555; background: white;" >{$diff.diff_raw}</pre>
		{else}
		<br/>
		{/if}
		{if $diff.comment|trim != ""}
		Comment<br/>
		<div style="border:1px solid #555; background: white;font-style:italic">{$diff.comment}</div>
		{/if}
	</li>
{foreachelse}
	<li><i>There were no changes.</i></li>	
{/foreach}
</ul>
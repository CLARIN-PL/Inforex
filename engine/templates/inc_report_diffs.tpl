<ul>
{foreach from=$diffs item=diff}
	<li>
		Modified on <b>{$diff.datetime}</b> by <b>{$diff.screename}</b> 
		<pre style="border:1px solid #555; background: white;" >{$diff.diff_raw}</pre>
	</li>
{foreachelse}
	<li><i>There were no changes.</i></li>	
{/foreach}
</ul>
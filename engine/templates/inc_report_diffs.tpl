{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<ul id="diffs">
{foreach from=$diffs item=diff}
	<li class="diff">
		<div class="header">Modified on <b>{$diff.datetime}</b> by <em>{$diff.screename}</em></div>

        {if $diff.comment|trim != ""}
        <div class="comment">
          <div class="subheader">Comment</div>
          <div class="content">{$diff.comment}</div>
        </div> 
        {/if}

        <div class="subheader2">Changes</div>
        <div class="diff">
    	   {if $diff.diff_raw|strip_tags|trim != ""}
		      <pre style="border:1px solid #555; background: white; white-space: pre-wrap;" >{$diff.diff_raw}</pre>
		   {else}
		      <i>no changes</i>
		   {/if}
		</div>		
	</li>
{foreachelse}
	<li><i>There were no changes.</i></li>	
{/foreach}
</ul>
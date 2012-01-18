<h1>Długość dokumentów</h1>

<table>
{foreach from=$lengths item=l key=key name=lengths}
    {if $smarty.foreach.lengths.index==0}
        <tr>
            <th>Tokenów</th>
	        {foreach from=$l item=c}
	            <th>{$c}</th>     
	        {/foreach}
	    </tr>
	{else}            
		<tr>
		    <th><i>do</i> {$key}</th>
		    {foreach from=$l item=c}
		        <td><div style="width: {$c*5}px; background: orange; display block;">{$c}</div></td>     
		    {/foreach}
		</tr>
	{/if}
{/foreach}
</table>

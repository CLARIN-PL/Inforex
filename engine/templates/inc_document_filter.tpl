    <h2>Common filters</h2>

    {capture name=link_ext_filters assign=link_ext_filters}{foreach from=$filters item=filter}{if $filter.selected}&amp;filter_{$filter.name}={$filter.selected}{/if}{/foreach}{/capture}

    <table class="tablesorter" cellspacing="1">
        <tr>
            <th style="width: 100px">Subcorpus:</th>
            <td>
            {assign var=subcorpus_set  value=0}
            {foreach from=$subcorpora item=s}
                {if $s.subcorpus_id==$subcorpus} 
                    {assign var=subcorpus_set value=1}
                    <em>{$s.name}</em>
                {else}
                    <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;subcorpus={$s.subcorpus_id}">{$s.name}</a>
                {/if},                
            {/foreach}
            {if $subcorpus_set==0}
                <em>all</em>
            {else}
                <a href="index.php?page={$page}&amp;corpus={$corpus.id}">all</a>
            {/if}        
            </td>    
        </tr>
    </table>
    
    {if $filters|@count>0}
    <h2>Custom filters</h2>
    
    <table class="tablesorter" cellspacing="1">
    
        {foreach from=$filters item=filter}
        <tr>
            <th style="width: 100px">{$filter.name}</th>
            <td>
            {assign var=filter_set  value=0}
            {foreach from=$filter.values item=value key=key name=values}
                {if $smarty.foreach.values.index > 0},{/if}            
                {if $key==$filter.selected}
                    {assign var=filter_set value=1}
                    <em>{$value}</em>            
                    <input type="hidden" name="filter_{$filter.name}" value="{$key}"/>
                {else}
                <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;ctag={$ctag}&amp;subcorpus={$subcorpus}&amp;filter_{$filter.name}={$key}">{$value}</a>
                {/if}
            {/foreach}
            
            {if $filter.all}
                ,
	            {if $filter_set==0}
	                <em>wszystkie</em>
	            {else}
	                <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;ctag={$ctag}&amp;subcorpus={$subcorpus}">wszystkie</a>
	            {/if}
	        {/if}        
            </td>
        </tr>
        {/foreach}
    </table>
    {/if}
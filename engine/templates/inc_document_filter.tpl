{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
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
                    <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;subcorpus={$s.subcorpus_id}&amp;status={$status}">{$s.name}</a>
                {/if},                
            {/foreach}
            {if $subcorpus_set==0}
                <em>all</em>
            {else}
                <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;status={$status}">all</a>
            {/if}        
            </td>    
        </tr>
        {if $statuses}
        <tr>
            <th>Status:</th>
            <td>
            {assign var=status_set  value=0}
            {foreach from=$statuses item=s}
                {if $s.id == $status} 
                    {assign var=status_set value=1}
                    <em>{$s.status}</em>
                {else}
                    <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;subcorpus={$subcorpus}&amp;status={$s.id}">{$s.status}</a>
                {/if},                
            {/foreach}
            {if $status_set==0}
                <em>all</em>
            {else}
                <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;subcorpus={$subcorpus}">all</a>
            {/if}  
            </td>
        </tr>
        {/if}
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
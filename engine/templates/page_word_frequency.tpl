{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<h1>Word frequency list</h1>

<div style="float: left; width: 400px;">
	<h2>Common filters</h2>

    {capture name=link_ext_filters assign=link_ext_filters}{foreach from=$filters item=filter}{if $filter.selected}&amp;filter_{$filter.name}={$filter.selected}{/if}{/foreach}{/capture}
	
	<table class="tablesorter" cellspacing="1" style="width: 400px">
		<tr>
		    <th style="width: 100px">Parts of speech:</th>
		    <td>
	           {assign var=pos_set  value="0"}
		       {foreach from=$classes item=class}
	                {if $class==$ctag}
	                    {assign var=pos_set  value=$class}
	                    <em>{$class}</em>                    
	                {else}
	                    <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;subcorpus={$subcorpus}&amp;ctag={$class}{$link_ext_filters}">{$class}</a>
	                {/if},
	            {/foreach}
	            {if $pos_set=="0"}
	                <em>wszystkie</em>
	            {else}
	                <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;subcorpus={$subcorpus}{$link_ext_filters}">wszystkie</a>
	            {/if}                        
		    </td>    
		</tr>
	    <tr>
	        <th style="width: 100px">Subcorpora:</th>
	        <td>
	        {assign var=subcorpus_set  value=0}
	        {foreach from=$subcorpora item=s}
	            {if $s.subcorpus_id==$subcorpus} 
	                {assign var=subcorpus_set value=1}
	                <em>{$s.name}</em>
	            {else}
	                <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;ctag={$ctag}&amp;subcorpus={$s.subcorpus_id}{$link_ext_filters}">{$s.name}</a>
	            {/if},                
	        {/foreach}
	        {if $subcorpus_set==0}
	            <em>wszystkie</em>
	        {else}
	            <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;ctag={$ctag}{$link_ext_filters}">wszystkie</a>
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
	        {foreach from=$filter.values item=value}
	            {if $value==$filter.selected}
	                {assign var=filter_set value=1}
	                <em>{$value}</em>            
	            {else}
	            <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;ctag={$ctag}&amp;subcorpus={$subcorpus}&amp;filter_{$filter.name}={$value}">{$value}</a>
	            {/if},
	        {/foreach}
	        {if $filter_set==0}
	            <em>wszystkie</em>
	        {else}
	            <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;ctag={$ctag}&amp;subcorpus={$subcorpus}">wszystkie</a>
	        {/if}        
	        </td>
	    </tr>
	    {/foreach}
	</table>
	{/if}
</div>

<div style="margin-left: 420px">
    <h2>List of words</h2>
	<div id="wf_loader"><img src="gfx/ajax.gif" class="ajax_loader" />Trwa ładowanie danych do tabeli</div>
    <div class="pagging">
		Pages:
			<span class="pagedisplay pagging"></span>
			<input type="hidden" class="pagesize" value="" />
	</div>
	
    <table id="words_frequences" class="tablesorter" cellspacing="1" style="width: 200px">
    <thead>
        <tr>
            <th>No.</th>
            <th>Word</th>
            <th>Count</th>
            <th>Documents</th>
            <th title="% of documents containing the word">Doc.&nbsp;%</th>
            <th title="proportion of documents to word count">Doc./Count</th>
        </tr>
    </thead>
    <tbody>
        
    </tbody>
    </table>
     <div class="pagging">
    	Pages:
        	<span class="pagedisplay pagging"></span>
        	<input type="hidden" class="pagesize" value="" />
    </div>
    <div style="padding: 10px;display:none;" id="nowords">
    	<i>There are no words for these criteria</i>
    </div>
</div>

<br style="clear: both"/>

{include file="inc_footer.tpl"}
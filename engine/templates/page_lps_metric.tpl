{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}
<div id="page_lps_metrics">
	<h1>Porównanie metryk</h1>
	
	<div style="float: left; width: 180px; margin-right: 20px">
	<b>Metryki</b>
	<ul>	
		<li>
			{if $metric=="tokens"}
				<b>liczba tokenów</b>
			{else}
				<a href="index.php?page=lps_metric&amp;corpus={$corpus.id}&amp;metric=tokens">liczba tokenów</a>
			{/if}
		</li>
		<li>
			{if $metric=="class"}
				<b>klasy gramatyczne</b>
			{else}
				<a href="index.php?page=lps_metric&amp;corpus={$corpus.id}&amp;metric=class&amp;class=subst">klasy gramatyczne</a>
			{/if}
		</li>
		<li>
			{if $metric=="ratio"}
				<b>proporcje klas gramatycznych</b>
			{else}
				<a href="index.php?page=lps_metric&amp;corpus={$corpus.id}&amp;metric=ratio&amp;class1=subst&amp;class2=adv">proporcje klas gramatycznych</a>
			{/if}
		</li>
	</ul>
	
	{if $metric == "ratio" }
	
	<b>Proporcje</b>
	<table>
		<tr>
		<td style="width: 50%">
			<ul>
				{foreach from=$classes item=c}	
				<li>
					{if $metric=="ratio" && $class1==$c}
						<b>{$c}</b>
					{else}
						<a href="index.php?page=lps_metric&amp;corpus={$corpus.id}&amp;metric=ratio&amp;class1={$c}&amp;class2={$class2}">{$c}</a>
					{/if}			
				</li>
				{/foreach}
				<hr/>
				
	            {foreach from=$poses item=v key=c}  
	            <li>
	                {if $metric=="ratio" && $class1==$c}
	                    <b>{$c}</b>
	                {else}
	                    <a href="index.php?page=lps_metric&amp;corpus={$corpus.id}&amp;metric=ratio&amp;class1={$c}&amp;class2={$class2}">{$c}</a>
	                {/if}           
	            </li>
	            {/foreach}
				
			</ul>
		</td><td style="width: 50%">
			<ul>
				{foreach from=$classes item=c}	
				<li>
					{if $metric=="ratio" && $class2==$c}
						<b>{$c}</b>
					{else}
						<a href="index.php?page=lps_metric&amp;corpus={$corpus.id}&amp;metric=ratio&amp;class1={$class1}&amp;class2={$c}">{$c}</a>
					{/if}			
				</li>
				{/foreach}
	            <hr/>
	            
	            {foreach from=$poses item=v key=c}  
	            <li>
	                {if $metric=="ratio" && $class2==$c}
	                    <b>{$c}</b>
	                {else}
	                    <a href="index.php?page=lps_metric&amp;corpus={$corpus.id}&amp;metric=ratio&amp;class1={$class1}&amp;class2={$c}">{$c}</a>
	                {/if}           
	            </li>
	            {/foreach}
			</ul>
		</td></tr>
	</table>
	
	{elseif $metric == "class"}
	
		<b>Klasy gramatyczne</b>
		<ul>
			{foreach from=$classes item=c}	
			<li>
				{if $metric=="class" && $class==$c}
					<b>{$c}</b>
				{else}
					<a href="index.php?page=lps_metric&amp;corpus={$corpus.id}&amp;metric=class&amp;class={$c}">{$c}</a>
				{/if}			
			</li>
			{/foreach}
		</ul>
	{/if}
	
	</div>
	
	{if $metric == "ratio" }
	<b>Proporcja <em>{$class1}</em>/<em>{$class2}</em></b>
	{elseif $metric == "class"}
	<b>Liczba tokenów z klasą gramatyczną <em>{$class}</em></b>
	{else}
	<b>Długość dokumentu</b>
	{/if}
	
	<table style="width: 800px" class="tablesorter" cellspacing="1">
	{foreach from=$stats item=l key=key name=stats}
	    {if $smarty.foreach.stats.index==0}
	        <tr>
	            <th style="width: 100px">Wartość</th>
		        {foreach from=$l item=c}
		            <th>{$c}</th>     
		        {/foreach}
		    </tr>
		{else}            
			<tr>
			    <th>{if $key<>"0"} <span style="font-weight: normal">></span> {$key_last}{$unit} <span style="font-weight: normal">do</span>{/if} {$key}{$unit}</th>
			    {assign var="key_last" value=$key}
			    {foreach from=$l item=c}
			    	{if $c==0}
			    		<td></td>
			    	{else}
			        	<td><div style="width: {$c}px; background: orange; display block;">{$c}</div></td>
			        {/if}     
			    {/foreach}
			</tr>
		{/if}
	{/foreach}
	</table>
	<br style="clear: both"/>
</div>

{include file="inc_footer.tpl"}

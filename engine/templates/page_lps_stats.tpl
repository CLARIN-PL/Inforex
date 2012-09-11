{include file="inc_header.tpl"}

<td class="table_cell_content">

<input type="hidden" id="subcorpus_id" value="{$subcorpus}"/>

<h2>Filter</h2>

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
                <a href="index.php?page=lps_stats&amp;corpus={$corpus.id}&amp;subcorpus={$s.subcorpus_id}">{$s.name}</a>
            {/if},                
        {/foreach}
        {if $subcorpus_set==0}
            <em>wszystkie</em>
        {else}
            <a href="index.php?page=lps_stats&amp;corpus={$corpus.id}">wszystkie</a>
        {/if}        
        </td>    
    </tr>
</table>

<br/>

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Autorzy listów</a></li>
        <li><a href="#tabs-2">Znaczniki</a></li>
        <li><a href="#tabs-3">Błędy</a></li>
        <li><a href="#tabs-4">Współwystępowanie błędów</a></li>
        <li><a href="#tabs-5">Interpunkcja</a></li>
    </ul>
    <div id="tabs-1">
        {include file="inc_lps_stats_authors.tpl"}    
    </div>
    <div id="tabs-2">
        {include file="inc_lps_stats_tags.tpl"}    
    </div>
    <div id="tabs-3">
        {include file="inc_lps_stats_errors.tpl"}    
    </div>
    <div id="tabs-4">
        {include file="inc_lps_stats_errors_matrix.tpl"}    
    </div>
    <div id="tabs-5">
        {include file="inc_lps_stats_interpunction.tpl"}    
    </div>
</div>


{include file="inc_footer.tpl"}
{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<td class="table_cell_content">

<h1>Corpus statistics</h1>

<div style="padding: 10px">
Number of words in accepted documents. <br/>Word is a sequence of characters matchig following regex "<em><code>(\pL|\pM|\pN)+</code></em>" (according to <a href="http://www.regular-expressions.info/unicode.html">http://www.regular-expressions.info/unicode.html</a>).
</div>

<table cellspacing="1" class="tablesorter" style="width: 500px">
	<thead>
	<tr>
		<th style="vertical-align: top">Subcorpus</th>
		<th style="vertical-align: top">Documents <br/><small>only accepted</small></th>
		<th style="vertical-align: top">Words</th>
        <th>Characters <br/><small>(no whitespaces)</small></th>
        <th style="vertical-align: top">Tokens</th>
	</tr>
	</thead>
	<tbody>
	  {foreach from=$stats item=item key=key}
	    {if $key eq "summary" }
    	    {capture name=summary}
	        <tr>
	            <th>TOTAL</th>
	            <th style="text-align: right">{$item.documents|number_format:0:",":"."}</th>
	            <th style="text-align: right">{$item.words|number_format:0:",":"."}</th>
	            <th style="text-align: right">{$item.chars|number_format:0:",":"."}</th>
                <th style="text-align: right">{$item.tokens|number_format:0:",":"."}</th>
	        </tr>	    
            {/capture}
	    {else}
        <tr>
            <th>{$item.name}</th>
            <td style="text-align: right">{$item.documents|number_format:0:",":"."}</td>
            <td style="text-align: right">{$item.words|number_format:0:",":"."}</td>
            <td style="text-align: right">{$item.chars|number_format:0:",":"."}</td>
            <td style="text-align: right">{$item.tokens|number_format:0:",":"."}</td>
        </tr>
        {/if}
      {/foreach}
	</tbody>
	<tfoot>
	   {$smarty.capture.summary}
	</tfoot>
</table>
<br/>

{include file="inc_footer.tpl"}
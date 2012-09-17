{include file="inc_header.tpl"}

<h1>Word frequency list</h1>

<h2>Filtering</h2>

<table class="tablesorter" cellspacing="1">
	<tr>
	    <th style="width: 100px">Parts of speech:</th>
	    <td>
           {assign var=pos_set  value="0"}
	       {foreach from=$classes item=class}
                {if $class==$ctag}
                    {assign var=pos_set  value=$class}
                    <em>{$class}</em>                    
                {else}
                    <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;subcorpus={$subcorpus}&amp;ctag={$class}">{$class}</a>
                {/if},
            {/foreach}
            {if $pos_set=="0"}
                <em>wszystkie</em>
            {else}
                <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;subcorpus={$subcorpus}">wszystkie</a>
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
                <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;ctag={$ctag}&amp;subcorpus={$s.subcorpus_id}">{$s.name}</a>
            {/if},                
        {/foreach}
        {if $subcorpus_set==0}
            <em>wszystkie</em>
        {else}
            <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;ctag={$ctag}">wszystkie</a>
        {/if}        
        </td>    
    </tr>
</table>

<h2>List of words</h2>

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
    {foreach from=$words item=word name=word}
    <tr>
        <td style="text-align: right">{$smarty.foreach.word.index+1}</td>
        <td><b>{$word.base|escape:"html"}</b></td>
        <td style="text-align: right">{$word.c}</td>
        <td style="text-align: right"><a href="index.php?page=browse&amp;corpus={$corpus.id}&amp;reset=1&amp;base={$word.base|escape:"html"}&amp;subcorpus={$subcorpus}" title="show list of documents in new window">{$word.docs}</a></td>
        <td style="text-align: right">{$word.docs_per|string_format:"%.2f"}%</td>
        <td style="text-align: right">{$word.docs_c|string_format:"%.4f"}</td>
    </tr>    
    {/foreach}
</tbody>
</table>

<br/>

{include file="inc_footer.tpl"}
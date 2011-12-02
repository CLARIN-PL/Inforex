{include file="inc_header.tpl"}

<h1>Morfologia</h1>

<h2>Kryteria</h2>

<table class="tablesorter" cellspacing="1">
	<tr>
	    <th style="width: 100px">Klasa:</th>
	    <td>
	       {if $ctag=="ign"}
	           <em>ign</em>,
	           <a href="index.php?page=morphology&amp;corpus={$corpus.id}&amp;subcorpus={$subcorpus}">wszystkie</a>
	       {else}
	           <a href="index.php?page=morphology&amp;corpus={$corpus.id}&amp;ctag=ign&amp;subcorpus={$subcorpus}">ign</a>,
	           <em>wszystkie</em>
	       {/if} 
	    </td>    
	</tr>
    <tr>
        <th style="width: 100px">Podkorpus:</th>
        <td>
        {assign var=subcorpus_set  value=0}
        {foreach from=$subcorpora item=s}
            {if $s.subcorpus_id==$subcorpus} 
                {assign var=subcorpus_set value=1}
                <em>{$s.name}</em>
            {else}
                <a href="index.php?page=morphology&amp;corpus={$corpus.id}&amp;ctag=ign&amp;subcorpus={$s.subcorpus_id}">{$s.name}</a>
            {/if},                
        {/foreach}
        {if $subcorpus_set==0}
            <em>wszystkie</em>
        {else}
            <a href="index.php?page=morphology&amp;corpus={$corpus.id}&amp;ctag={$ctag}">wszystkie</a>
        {/if}        
        </td>    
    </tr>
</table>

<h2>Statystyki</h2>

<table id="words_frequences" class="tablesorter" cellspacing="1" style="width: 200px">
<thead>
    <tr>
        <th>Lp.</th>
        <th>Słowo</th>
        <th>Liczba</th>
        <th>Dokumenty</th>
    </tr>
</thead>
<tbody>
    {foreach from=$words item=word name=word}
    <tr>
        <td style="text-align: right">{$smarty.foreach.word.index+1}</td>
        <td>{$word.base}</td>
        <td style="text-align: right">{$word.c}</td>
        <td style="text-align: right">{$word.docs}</td>
    </tr>    
    {/foreach}
</tbody>
</table>

<br/>

{include file="inc_footer.tpl"}
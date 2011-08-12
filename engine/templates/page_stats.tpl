{include file="inc_header.tpl"}

<td class="table_cell_content">

<h1>Statystyki korpusu</h1>

<div  style="padding: 10px"><em>Statystyki dotyczą dokumentów, których status ustawiony jest na <i>Przyjęty</i> z pominięciem tagów HTML.</em></div>

<table cellspacing="1" class="tablesorter" style="width: 400px">
	<thead>
	<tr>
		<th style="vertical-align: top">Podkorpus</th>
		<th style="vertical-align: top">Dokumenty</th>
		<th style="vertical-align: top">Słowa</th>
        <th>Znaki <br/><small>(bez białych)</small></th>
	</tr>
	</thead>
	<tbody>
	  {foreach from=$stats item=item key=key}
	    {if $key eq "summary" }
    	    {capture name=summary}
	        <tr>
	            <th>ŁĄCZNIE</th>
	            <th style="text-align: right">{$item.documents|number_format:0:",":"."}</th>
	            <th style="text-align: right">{$item.words|number_format:0:",":"."}</th>
	            <th style="text-align: right">{$item.chars|number_format:0:",":"."}</th>
	        </tr>	    
            {/capture}
	    {else}
        <tr>
            <th>{$item.name}</th>
            <td style="text-align: right">{$item.documents|number_format:0:",":"."}</td>
            <td style="text-align: right">{$item.words|number_format:0:",":"."}</td>
            <td style="text-align: right">{$item.chars|number_format:0:",":"."}</td>
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
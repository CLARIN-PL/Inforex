{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<table style="width: 99%">
<tr>
<td style="vertical-align: top; width: 50%"> 
 
	<div style="height: 500px; margin-right: 10px; overflow: auto;">
		<table class="tablesorter" cellspacing="1" style="width: 200px;" id="interp_items">
		  <thead>
		    <tr>
		        {foreach from=$interpunction_header item=h}
		            <th>{$h}</th>
		        {/foreach}
		        <th>Lista</th>
		    </tr>
		  </thead>
		  <tbody style="">
		  {foreach from=$interpunction item=arr key=item}
		    <tr>
		        <td>{$item|escape|trim}</td>
		        {foreach from=$interpunction_header item=h key=kh}
		            {if $h<>"Interpunkcja"}
		            <td style="text-align: right">{$arr.$kh}</td>
		            {/if}
		        {/foreach}
		        <td>(<a href="#" class="interp" interp="{$item|escape|trim}" title="Pokaż listę dokumentów">wyświetl</a>)</td>
		    </tr>
		{/foreach}
		  </tbody>
		 </table>
	</div>

</td>
<td style="vertical-align: top"> 
  
	<div style="height: 500px; overflow: auto;">
		 <table class="tablesorter" id="interp" style="width: 300px;" cellspacing="1">
		    <thead>
		        <tr>
		            <th>Lp.</th>
		            <th>Podkorpus</th>
		            <th>Dokument</th>
		            <th>Wystąpienia</th>
		        </tr>
		    </thead>
		    <tbody>
		    </tbody>
		 </table>
	</div>

</td>
</tr>
</table>
 
 <br style="clear: both"/>
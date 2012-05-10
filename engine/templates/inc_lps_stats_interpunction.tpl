<h1>Interpunkcja</h1>

<div style="width: 600px; height: 400px; float: left; margin-right: 10px;  overflow: auto;">
	<table class="tablesorter" cellspacing="1" style="width: 200px;">
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
  
 <div style="width: 310px; height: 400px; overflow: auto;">
	 <table class="tablesorter" id="interp" style="width: 300px;">
	    <thead>
	        <tr>
	            <th>Lp.</th>
	            <th>Podkorpus</th>
	            <th>Dokument</th>
	        </tr>
	    </thead>
	    <tbody>
	    </tbody>
	 </table>
</div>
 
 <br style="clear: both"/>
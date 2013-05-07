{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<h1>Statystyki relacji</h1>
<table style="width: 100%">
	<tr>
		<td style="vertical-align: top; width: 450px;">
			<div class="corpus_id" id={$corpus_id}>Typy relacji:
			</div>
			<div class="document_id" id={$document_id}></div>
			<table cellspacing="1" class="tablesorter" style="width: 400px;">
				<thead>
					<tr>
						<th style="vertical-align: top">Nazwa relacji</th>
						<th style="vertical-align: top">Relacja</th>
						<th style="vertical-align: top">Liczba relacji</th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$relations_type key=key1 item=item}
		    			<tr class="setGroup">
    		    			<td colspan="2" style="vertical-align: middle">{$item.relation_name}</td>
		        		    <td style="vertical-align: middle" class="relationNameCount">{$item.relation_count}</td>
			        	</tr>
			        	{foreach from=$item.types key=key2 item=types}			        		
			        		<tr class="subsetGroup{if $key2 eq '0' && $key1 eq '0'} selected{/if}" {if $key1 neq '0'} style="display:none" {/if} id={$item.relation_id}>
			        			<td class="empty"></td>
								<td style="vertical-align: middle" class="relationName">{$types.relation_type}</td>
		        		    	<td style="vertical-align: middle" class="relationCount">{$types.relation_count}</td>
			        		</tr>
			        	{/foreach}
	    			{/foreach}
				{*
				{foreach from=$relations_type item=item}
		    		<tr class="relationName" id={$item.relation_id}>
    		    		<td style="vertical-align: middle">{$item.relation_name}</td>
	        		    <td style="vertical-align: middle" class="relationNameCount">{$item.relation_count}</td>
		        	</tr>
	    		{/foreach}
	    		*}
				</tbody>
			</table>
		</td>
		<td style="vertical-align: top;" class="relation_statistic_list">
			<div class="relation_limit" id={$relations_limit} >Lista relacji:
			</div>
			<div id="relation_pages">
			{foreach from=$relations_pages item=item}
				{if $item.from eq '0'}
					{if $item.to gt $item.from}
						<span class="relationPage inactive" id={$relation_set_id}><span>[{$item.from} - {$item.to}]</span></span>
					{/if}
				{else}
					<span class="relationPage active" id={$relation_set_id}><a href="#" class="relationNameLink" id={$relations_type.0.types.0.relation_type}>[{$item.from} - {$item.to}]</a></span>
				{/if}
			{/foreach}
			</div>
			<table cellspacing="1" class="tablesorter" style="width: 100%;">
				<thead>
					<tr>
						<th style="vertical-align: top">Id dokumentu</th>
						<th style="vertical-align: top">Nazwa podkorpusu</th>
						<th style="vertical-align: top">Tekst jednostki źródłowej</th>
						<th style="vertical-align: top">Typ jednostki źródłowej</th>
						<th style="vertical-align: top">Tekst jednostki docelowej</th>
						<th style="vertical-align: top">Typ jednostki docelowej</th>
					</tr>
				</thead>
				<tbody id="relation_statistic_items">
				{foreach from=$relations_list item=item}
    				<tr>
			    		<td style="vertical-align: middle"><a href="index.php?page=report&amp;corpus={$corpus_id}&amp;subpage=annotator&amp;id={$item.document_id}">{$item.document_id}</a></td>
    					<td style="vertical-align: middle">{$item.subcorpus_name}</td>
			    		<td style="vertical-align: middle">{$item.source_text}</td>
    					<td style="vertical-align: middle">{$item.source_type}</td>
			    		<td style="vertical-align: middle">{$item.target_text}</td>
    					<td style="vertical-align: middle">{$item.target_type}</td>
			    	</tr>
    			{/foreach}
				</tbody>
			</table>	
		</td>
	</tr>
</table>
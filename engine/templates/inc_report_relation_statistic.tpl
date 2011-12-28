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
						<th style="vertical-align: top">Liczba relacji</th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$relations_type item=item}
		    		<tr class="relationName" id={$item.relation_id}>
    		    		<td style="vertical-align: middle">{$item.relation_name}</td>
	        		    <td style="vertical-align: middle" class="relationNameCount">{$item.relation_count}</td>
		        	</tr>
	    		{/foreach}
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
					<span class="relationPage active" id={$relation_set_id}><a href="#">[{$item.from} - {$item.to}]</a></span>
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
			    		<td style="vertical-align: middle">{$item.document_id}</td>
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
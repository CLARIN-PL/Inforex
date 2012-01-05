{include file="inc_header.tpl"}
<td class="table_cell_content">
	<h1>Testy spójności</h1>
	<table style="width: 100%">
		<tr>
			<td style="vertical-align: top; width: 450px;">
				<div class="corpus_id" id={$corpus_id}>Typy relacji:
				</div>
				<table cellspacing="1" class="tablesorter" id="testslist" style="width: 400px;">
					<thead>
						<tr>
							<th style="vertical-align: top">Nazwa testu</th>
							<th style="vertical-align: top">Opis testu</th>
						</tr>
					</thead>
					<tbody>
						<tr class="setGroup{if $test_order eq 'empty_chunks'} selected{/if}">
							<td style="vertical-align: middle">Wykrywanie pustych chunków</td>
		        		    <td style="vertical-align: middle">Dokumenty zawierające puste chunki lub chunki zawierające tylko białe znaki</td>		        		    
			        	</tr>
			        	<tr class="setGroup">
							<td style="vertical-align: middle">Ciągłość tokenów</td>
		        		    <td style="vertical-align: middle">Dla każdego tokenu A w dokumencie (oprócz ostatniego) istnieje token B taki, że A.to+1=B.from</td>		        		    
			        	</tr>
			        	<tr class="setGroup">
							<td style="vertical-align: middle">Tokeny przecinające anotacje</td>
		        		    <td style="vertical-align: middle">Dla każdej anotacji A nie istnieje taki token T, dla którego (T.from&nbsp>&nbspA.from&nbspAND&nbspT.from&nbsp<&nbspA.to) OR (T.to&nbsp>&nbspA.from&nbspAND&nbspT.to&nbsp<&nbspA.to)</td>		        		    
			        	</tr>			        	
					</tbody>
				</table>
			</td>
			<td style="vertical-align: top;" class="test_result_list">
				<table cellspacing="1" class="tablesorter" style="width: 100%;">
					<thead>
						<tr>
							<th style="vertical-align: top">Lp.</th>
							<th style="vertical-align: top">Id dokumentu</th>
							<th style="vertical-align: top">Ilość naruszeń spójności</th>							
						</tr>
					</thead>
					<tbody id="tests_items">
						{foreach from=$reports key=key item=item}
							<tr>
								<td style="vertical-align: middle">{$key+1}</td>
								<td style="vertical-align: middle"><a href="index.php?page=report&amp;corpus={$corpus_id}&amp;subpage=annotator&amp;id={$item.document_id}">{$item.document_id}</a></td>
								<td style="vertical-align: middle">{$item.count}</td>							
							</tr>
						{/foreach}
					</tbody>
				</table>	
			</td>
		</tr>
	</table>
</td>
{include file="inc_footer.tpl"}
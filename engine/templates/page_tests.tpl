{include file="inc_header.tpl"}
<td class="table_cell_content">
	<h1>Testy spójności</h1>
	<table style="width: 100%">
		<tr>
			<td style="vertical-align: top; width: 650px;">
				<div class="corpus_id" id={$corpus_id}>Typy testów:
				</div>
				<table cellspacing="1" class="tablesorter" id="testslist" style="width: 600px;">
					<thead>
						<tr>
							<th style="vertical-align: top">Nazwa&#160;testu</th>
							<th style="vertical-align: top">Opis&#160;testu</th>
							<th style="vertical-align: top">Do&#160;poprawy</th>
						</tr>
					</thead>
					<tbody>
						<tr class="group{if $count_reports_empty_chunk} wrong{else} corect{/if}" id="empty_chunk">
							<td style="vertical-align: middle" class="test_name">Wykrywanie pustych chunków</td>
		        		    <td style="vertical-align: middle">Dokumenty zawierające puste chunki lub chunki zawierające tylko białe znaki</td>		        		    
		        		    <td style="vertical-align: middle">{$count_reports_empty_chunk|default:"<i>brak</i>"}</td>
			        	</tr>
			        	<tr class="group{if $count_reports_wrong_tokens} wrong{else} corect{/if}" id="wrong_tokens">
							<td style="vertical-align: middle" class="test_name">Ciągłość tokenów</td>
		        		    <td style="vertical-align: middle">Dla każdego tokenu A w dokumencie (oprócz ostatniego) istnieje token B taki, że (A.to+1&#160;=&#160;B.from)</td>
		        		    <td style="vertical-align: middle">{$count_reports_wrong_tokens|default:"<i>brak</i>"}</td>		        		    
			        	</tr>
			        	<tr class="group{if $count_reports_tokens_out_of_scale} wrong{else} corect{/if}" id="tokens_out_of_scale">
							<td style="vertical-align: middle" class="test_name">Zasięg tokenów</td>
		        		    <td style="vertical-align: middle">Indeksy tokenów nie mogą wykraczać poza ramy dokumnetu, czyli dla każdego tokenu T w dokumencie D spełniona jest zależność, (T.from&#160;<=&#160;D.length&#160;AND&#160;T.to&#160;<=&#160;D.length)</td>
		        		    <td style="vertical-align: middle">{$count_reports_tokens_out_of_scale|default:"<i>brak</i>"}</td>		        		    
			        	</tr>
			        	<tr class="group{if $count_reports_wrong_annotations} wrong{else} corect{/if}" id="wrong_annotations">
							<td style="vertical-align: middle" class="test_name">Tokeny przecinające anotacje</td>
		        		    <td style="vertical-align: middle">Dla każdej anotacji A nie istnieje taki token T, dla którego (T.from&#160;>&#160;A.from&#160;AND&#160;T.from&#160;<&#160;A.to&#160;AND&#160;T.to&#160;>&#160;A.to) OR (T.from&#160;<&#160;A.from&#160;AND&#160;T.to&#160;>&#160;A.from&#160;AND&#160;T.to&#160;<&#160;A.to)</td>
		        		    <td style="vertical-align: middle">{$count_reports_wrong_annotations|default:"<i>brak</i>"}</td>		        		    
			        	</tr>
			        	<tr class="group{if $count_reports_wrong_annotations_by_annotation} wrong{else} corect{/if}" id="wrong_annotations_by_annotation">
							<td style="vertical-align: middle" class="test_name">Anotacje przecinające anotacje</td>
		        		    <td style="vertical-align: middle">Dla każdej anotacji A1 nie istnieje taka anotacja A2 będąca tego samego typu, dla której (A2.from&#160;>&#160;A1.from&#160;AND&#160;A2.from&#160;<&#160;A1.to&#160;AND&#160;A2.to&#160;>&#160;A1.to) OR (A2.from&#160;<&#160;A1.from&#160;AND&#160;A2.to&#160;>&#160;A1.from&#160;AND&#160;A2.to&#160;<&#160;A1.to)</td>
		        		    <td style="vertical-align: middle">{$count_reports_wrong_annotations_by_annotation|default:"<i>brak</i>"}</td>		        		    
			        	</tr>			        	
					</tbody>
				</table>
			</td>
			<td style="vertical-align: top;" class="test_result_list">
				<div class="result_test_name"></div>
				<table cellspacing="1" class="tablesorter" id="tests_document_list" style="width: 100%;">
					<thead>
						<tr>
							<th style="vertical-align: top">Lp.</th>
							<th style="vertical-align: top">Id dokumentu</th>
							<th style="vertical-align: top">Ilość naruszeń spójności</th>
							<th style="vertical-align: top">Elementy naruszające spójność</th>							
						</tr>
					</thead>
					<tbody>
						{foreach from=$reports_empty_chunk key=key item=item}
							<tr class="tests_items empty_chunk">
								<td style="vertical-align: middle">{$key+1}</td>
								<td style="vertical-align: middle"><a href="index.php?page=report&amp;corpus={$corpus_id}&amp;subpage=annotator&amp;id={$item.document_id}">{$item.document_id}</a></td>
								<td colspan="2" style="vertical-align: middle">{$item.count}</td>							
							</tr>
							{foreach from=$item.data item=chunk name=data}
								{if $smarty.foreach.data.last}
								{else}
									<tr class="tests_errors empty_chunk">
										<td colspan="3" class="empty"></td>
										<td style="vertical-align: middle">Pusty chunk: znajduje się w linii {$chunk}</td>
									</tr>
								{/if}
							{/foreach}
						{/foreach}
						{foreach from=$reports_wrong_tokens key=key item=item}
							<tr class="tests_items wrong_tokens">
								<td style="vertical-align: middle">{$key+1}</td>
								<td style="vertical-align: middle"><a href="index.php?page=report&amp;corpus={$corpus_id}&amp;subpage=annotator&amp;id={$item.document_id}">{$item.document_id}</a></td>
								<td colspan="2" style="vertical-align: middle">{$item.count}</td>							
							</tr>
							{foreach from=$item.data item=token}
								<tr class="tests_errors wrong_tokens">
									<td colspan="3" class="empty"></td>
									<td style="vertical-align: middle">Dla tokenu o indeksie {$token.id} i zakesie [{$token.from}, {$token.to}] nie istnieje token będący jego następnikiem</td>
								</tr>
							{/foreach}
						{/foreach}
						{foreach from=$reports_tokens_out_of_scale key=key item=item}
							<tr class="tests_items tokens_out_of_scale">
								<td style="vertical-align: middle">{$key+1}</td>
								<td style="vertical-align: middle"><a href="index.php?page=report&amp;corpus={$corpus_id}&amp;subpage=annotator&amp;id={$item.document_id}">{$item.document_id}</a></td>
								<td colspan="2" style="vertical-align: middle">{$item.count}</td>							
							</tr>
							{foreach from=$item.data item=token}
								<tr class="tests_errors tokens_out_of_scale">
									<td colspan="3" class="empty"></td>
									<td style="vertical-align: middle">Token o indeksie {$token.id} i zakesie [{$token.from}, {$token.to}] wykracza poza ramy dokumentu o długości [{$token.content_length}]</td>
								</tr>
							{/foreach}
						{/foreach}
						{foreach from=$reports_wrong_annotations key=key item=item}
							<tr class="tests_items wrong_annotations">
								<td style="vertical-align: middle">{$key+1}</td>
								<td style="vertical-align: middle"><a href="index.php?page=report&amp;corpus={$corpus_id}&amp;subpage=annotator&amp;id={$item.document_id}">{$item.document_id}</a></td>
								<td colspan="2" style="vertical-align: middle">{$item.count}</td>							
							</tr>
							{foreach from=$item.data item=annotation}
								<tr class="tests_errors wrong_annotations">
									<td colspan="3" class="empty"></td>
									<td style="vertical-align: middle">Anotacja: <span class="{$annotation.annotation_type}" title="an#{$annotation.annotation_id}:{$annotation.annotation_type}">{$annotation.annotation_text}</span> o zakresie [{$annotation.annotation_from},{$annotation.annotation_to}] przecina się z tokenem o indeksie {$annotation.token_id} i zakesie [{$annotation.token_from}, {$annotation.token_to}]</td>
								</tr>
							{/foreach}
						{/foreach}
						{foreach from=$reports_wrong_annotations_by_annotation key=key item=item}
							<tr class="tests_items wrong_annotations_by_annotation">
								<td style="vertical-align: middle">{$key+1}</td>
								<td style="vertical-align: middle"><a href="index.php?page=report&amp;corpus={$corpus_id}&amp;subpage=annotator&amp;id={$item.document_id}">{$item.document_id}</a></td>
								<td colspan="2" style="vertical-align: middle">{$item.count}</td>							
							</tr>
							{foreach from=$item.data item=annotation}
								<tr class="tests_errors wrong_annotations_by_annotation">
									<td colspan="3" class="empty"></td>
									<td style="vertical-align: middle"><span class="{$annotation.type1}" title="an#{$annotation.id1}:{$annotation.type1}">{$annotation.text1}</span> <span class="{$annotation.type2}" title="an#{$annotation.id2}:{$annotation.type2}">{$annotation.text2}</span></td>
								</tr>
							{/foreach}
						{/foreach}
					</tbody>
				</table>	
			</td>
		</tr>
	</table>
</td>
{include file="inc_footer.tpl"}
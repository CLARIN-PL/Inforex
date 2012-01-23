{include file="inc_header.tpl"}
<td class="table_cell_content">
	<h1 class="corpus_id" id={$corpus_id}>Testy spójności</h1>
	<table style="width: 100%">
		<tr>
			<td style="vertical-align: top; width: 650px;">
				<div class="documents_in_corpus" id={$documents_in_corpus}>Typy testów:
				</div>
				<table cellspacing="1" class="tablesorter" id="testslist" style="width: 600px;">
					<thead>
						<tr>
							<th style="vertical-align: top" rowspan="2">Nazwa&#160;testu</th>
							<th style="vertical-align: top" rowspan="2">Opis&#160;testu</th>
							<th style="vertical-align: top" colspan="3">Dane&#160;testu</th>							
						</tr>
						<tr>
							<th style="vertical-align: top">Stan&#160;testu</th>
							<th style="vertical-align: top">Czas&#160;testu [s]</th>
							<th style="vertical-align: top">Do&#160;poprawy</th>
						</tr>
					</thead>
					<tbody>
						<tr class="group" id="empty_chunk">
							<td style="vertical-align: middle" class="test_name">Wykrywanie pustych chunków</td>
		        		    <td style="vertical-align: middle">Dokumenty zawierające puste chunki lub chunki zawierające tylko białe znaki</td>
		        		    <td style="vertical-align: middle" class="test_process">start</td>		        		    
		        		    <td style="vertical-align: middle" class="test_time running">0</td>
		        		    <td style="vertical-align: middle" class="test_result"><i>brak</i></td>
			        	</tr>
			        	<tr class="group" id="wrong_chunk">
							<td style="vertical-align: middle" class="test_name">Struktura dokumentu</td>
		        		    <td style="vertical-align: middle">Dokumenty zawierające błędy w strukturze dokumentu</td>
		        		    <td style="vertical-align: middle" class="test_process">start</td>		        		    
		        		    <td style="vertical-align: middle" class="test_time running">0</td>
		        		    <td style="vertical-align: middle" class="test_result"><i>brak</i></td>
			        	</tr>
			        	<tr class="group" id="wrong_tokens">
							<td style="vertical-align: middle" class="test_name">Ciągłość tokenów</td>
		        		    <td style="vertical-align: middle">Dla każdego tokenu A w dokumencie (oprócz ostatniego) istnieje token B taki, że (A.to+1&#160;=&#160;B.from)</td>
		        		    <td style="vertical-align: middle" class="test_process">start</td>
		        		    <td style="vertical-align: middle" class="test_time running">0</td>		        		    
		        		    <td style="vertical-align: middle" class="test_result"><i>brak</i></td>		        		    
			        	</tr>
			        	<tr class="group" id="tokens_out_of_scale">
							<td style="vertical-align: middle" class="test_name">Zasięg tokenów</td>
		        		    <td style="vertical-align: middle">Indeksy tokenów nie mogą wykraczać poza ramy dokumnetu, czyli dla każdego tokenu T w dokumencie D spełniona jest zależność, (T.from&#160;<=&#160;D.length&#160;AND&#160;T.to&#160;<=&#160;D.length)</td>
		        		    <td style="vertical-align: middle" class="test_process">start</td>
		        		    <td style="vertical-align: middle" class="test_time running">0</td>		        		    
		        		    <td style="vertical-align: middle" class="test_result"><i>brak</i></td>		        		    
			        	</tr>
			        	<tr class="group" id="wrong_annotations">
							<td style="vertical-align: middle" class="test_name">Tokeny przecinające anotacje</td>
		        		    <td style="vertical-align: middle">Dla każdej anotacji A nie istnieje taki token T, dla którego (T.from&#160;>&#160;A.from&#160;AND&#160;T.from&#160;<&#160;A.to&#160;AND&#160;T.to&#160;>&#160;A.to) OR (T.from&#160;<&#160;A.from&#160;AND&#160;T.to&#160;>&#160;A.from&#160;AND&#160;T.to&#160;<&#160;A.to)</td>
		        		    <td style="vertical-align: middle" class="test_process">start</td>
		        		    <td style="vertical-align: middle" class="test_time running">0</td>		        		    
		        		    <td style="vertical-align: middle" class="test_result"><i>brak</i></td>		        		    
			        	</tr>
			        	<tr class="group" id="wrong_annotations_by_annotation">
							<td style="vertical-align: middle" class="test_name">Anotacje przecinające anotacje</td>
		        		    <td style="vertical-align: middle">Dla każdej anotacji A1 nie istnieje taka anotacja A2 będąca tego samego typu, dla której (A2.from&#160;>&#160;A1.from&#160;AND&#160;A2.from&#160;<&#160;A1.to&#160;AND&#160;A2.to&#160;>&#160;A1.to) OR (A2.from&#160;<&#160;A1.from&#160;AND&#160;A2.to&#160;>&#160;A1.from&#160;AND&#160;A2.to&#160;<&#160;A1.to)</td>
		        		    <td style="vertical-align: middle" class="test_process">start</td>
		        		    <td style="vertical-align: middle" class="test_time running">0</td>		        		    
		        		    <td style="vertical-align: middle" class="test_result"><i>brak</i></td>		        		    
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
					</tbody>
				</table>	
			</td>
		</tr>
	</table>
</td>
{include file="inc_footer.tpl"}
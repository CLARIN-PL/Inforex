{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
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
							<th style="vertical-align: middle">Aktywny test</th>
							<th style="vertical-align: top;" rowspan="2">Nazwa i opis testu</th>
							<th style="vertical-align: top" colspan="3">Dane&#160;testu</th>							
						</tr>
						<tr>
							<th style="vertical-align: middle"><input class="activeTests" type="checkbox" /></th>
							<th style="vertical-align: top">Stan&#160;testu</th>
							<th style="vertical-align: top">Czas&#160;testu [s]</th>
							<th style="vertical-align: top">Do&#160;poprawy</th>
						</tr>
					</thead>
					<tbody>
						<tr class="group">
							<td style="vertical-align: middle; background: #eee"><input class="activeTest allLin" type="checkbox" /></td>
							<td style="vertical-align: middle; background: #eee" colspan="5" class="test_name"><b>Testy dla lingwistów</b>  </td>		        		    
			        	</tr>
			        	<tr class="group" id="wrong_annotations_by_annotation">
			        		<td style="vertical-align: middle"><input class="activeTest lin" id="wrong_annotations_by_annotation" type="checkbox" /></td>
							<td style="vertical-align: middle" class="test_name">
							    <b>Anotacje przecinające anotacje</b><br/>
		        		        Dla każdej anotacji A1 nie istnieje taka anotacja A2 będąca tego samego typu, dla której (A2.from&#160;>&#160;A1.from&#160;AND&#160;A2.from&#160;<&#160;A1.to&#160;AND&#160;A2.to&#160;>&#160;A1.to) OR (A2.from&#160;<&#160;A1.from&#160;AND&#160;A2.to&#160;>&#160;A1.from&#160;AND&#160;A2.to&#160;<&#160;A1.to)
		        		    </td>
		        		    <td style="vertical-align: middle" class="test_process">stop</td>
		        		    <td style="vertical-align: middle" class="test_time">0</td>		        		    
		        		    <td style="vertical-align: middle" class="test_result"><i>brak</i></td>		        		    
			        	</tr>
			        	<tr class="group" id="wrong_annotation_chunks_type">
			        		<td style="vertical-align: middle"><input class="activeTest lin" id="wrong_annotation_chunks_type" type="checkbox" /></td>
							<td style="vertical-align: middle" class="test_name">
							     <b>Anotacje składniowe</b><br/>
		        		        Frazy „duże” są rozłączne (frazy duże to chunk_np, chunk_adjp, chunk_vp).</br>Frazy chunk_agp nie mogą przekraczać granic fraz „dużych”.</br>Frazy chunk_qp nie mogą przekraczać granic fraz chunk_agp ani granic fraz „dużych”.</td>
		        		    <td style="vertical-align: middle" class="test_process">stop</td>
		        		    <td style="vertical-align: middle" class="test_time">0</td>		        		    
		        		    <td style="vertical-align: middle" class="test_result"><i>brak</i></td>		        		    
			        	</tr>
			        	<tr class="group" id="wrong_annotation_in_annotation">
			        		<td style="vertical-align: middle"><input class="activeTest lin" id="wrong_annotation_in_annotation" type="checkbox" /></td>
							<td style="vertical-align: middle" class="test_name">
							     <b>Anotacje w anotacjach</b><br/>
		        		        Dla każdej anotacji A1 nie istnieje anotacja A2 będąca tego samego typu, dla której (A2.from&#160;>=&#160;A1.from&#160;AND&#160;A2.to&#160;<=&#160;A1.to)</td>
		        		    <td style="vertical-align: middle" class="test_process">stop</td>
		        		    <td style="vertical-align: middle" class="test_time">0</td>		        		    
		        		    <td style="vertical-align: middle" class="test_result"><i>brak</i></td>		        		    
			        	</tr>
			        	<tr class="group" id="wrong_annotations_duplicate">
			        		<td style="vertical-align: middle"><input class="activeTest lin" id="wrong_annotations_duplicate" type="checkbox" /></td>
							<td style="vertical-align: middle" class="test_name">
							     <b>Duplikaty anotacji</b><br/>
		        		         Duplikatem jest para anotacji, które posiadają takie same wartości dla atrybutów `report_id`, `from`, `to`, `type` oraz ustawione są jako stage=`final`</td>
		        		    <td style="vertical-align: middle" class="test_process">stop</td>
		        		    <td style="vertical-align: middle" class="test_time">0</td>		        		    
		        		    <td style="vertical-align: middle" class="test_result"><i>brak</i></td>		        		    
			        	</tr>
			        	<tr class="group" id="wrong_annotations_by_sentence">
			        		<td style="vertical-align: middle"><input class="activeTest lin" id="wrong_annotations_by_sentence" type="checkbox" /></td>
							<td style="vertical-align: middle" class="test_name">
							     <b>Anotacje przekraczające granice zdań</b><br/>
		        		        Anotacje wykraczające poza granice zdania</td>
		        		    <td style="vertical-align: middle" class="test_process">stop</td>
		        		    <td style="vertical-align: middle" class="test_time">0</td>		        		    
		        		    <td style="vertical-align: middle" class="test_result"><i>brak</i></td>		        		    
			        	</tr>
			        	<tr class="group" id="wrong_chunk">
			        		<td style="vertical-align: middle"><input class="activeTest lin" id="wrong_chunk" type="checkbox" /></td>
							<td style="vertical-align: middle" class="test_name">
							 <b>Struktura dokumentu</b><br/>
		        		        Dokumenty zawierające błędy w strukturze dokumentu</td>
		        		    <td style="vertical-align: middle" class="test_process">stop</td>		        		    
		        		    <td style="vertical-align: middle" class="test_time">0</td>
		        		    <td style="vertical-align: middle" class="test_result"><i>brak</i></td>
			        	</tr>
						<tr class="group" id="empty_chunk">
							<td style="vertical-align: middle"><input class="activeTest lin" id="empty_chunk" type="checkbox" /></td>
							<td style="vertical-align: middle" class="test_name">
							     <b>Wykrywanie pustych chunków</b><br/>
		        		        Dokumenty zawierające puste chunki lub chunki zawierające tylko białe znaki</td>
		        		    <td style="vertical-align: middle" class="test_process">stop</td>		        		    
		        		    <td style="vertical-align: middle" class="test_time">0</td>
		        		    <td style="vertical-align: middle" class="test_result"><i>brak</i></td>
			        	</tr>
			        	<tr class="group">
							<td style="vertical-align: middle; background: #eee"><input class="activeTest allTech" type="checkbox" /></td>
							<td style="vertical-align: middle; background: #eee" colspan="5" class="test_name"><b>Testy techniczne</b>  </td>		        		    
			        	</tr>
			        	<tr class="group" id="wrong_tokens">
			        		<td style="vertical-align: middle"><input class="activeTest tech" id="wrong_tokens" type="checkbox" /></td>
							<td style="vertical-align: middle" class="test_name">
							     <b>Ciągłość tokenów</b><br/>
		        		            Dla każdego tokenu A w dokumencie (oprócz ostatniego) istnieje token B taki, że (A.to+1&#160;=&#160;B.from)</td>
		        		    <td style="vertical-align: middle" class="test_process">stop</td>
		        		    <td style="vertical-align: middle" class="test_time">0</td>		        		    
		        		    <td style="vertical-align: middle" class="test_result"><i>brak</i></td>		        		    
			        	</tr>
			        	<tr class="group" id="tokens_out_of_scale">
			        		<td style="vertical-align: middle"><input class="activeTest tech" id="tokens_out_of_scale" type="checkbox" /></td>
							<td style="vertical-align: middle" class="test_name">
							     <b>Zasięg tokenów</b><br/>
		        		            Indeksy tokenów nie mogą wykraczać poza ramy dokumnetu, czyli dla każdego tokenu T w dokumencie D spełniona jest zależność, (T.from&#160;<=&#160;D.length&#160;AND&#160;T.to&#160;<=&#160;D.length)</td>
		        		    <td style="vertical-align: middle" class="test_process">stop</td>
		        		    <td style="vertical-align: middle" class="test_time">0</td>		        		    
		        		    <td style="vertical-align: middle" class="test_result"><i>brak</i></td>		        		    
			        	</tr>
			        	<tr class="group" id="wrong_annotations">
			        		<td style="vertical-align: middle"><input class="activeTest tech" id="wrong_annotations" type="checkbox" /></td>
							<td style="vertical-align: middle" class="test_name">
							     <b>Tokeny przecinające anotacje</b><br/>
		        		            Dla każdej anotacji A nie istnieje taki token T, dla którego (T.from&#160;>&#160;A.from&#160;AND&#160;T.from&#160;<&#160;A.to&#160;AND&#160;T.to&#160;>&#160;A.to) OR (T.from&#160;<&#160;A.from&#160;AND&#160;T.to&#160;>&#160;A.from&#160;AND&#160;T.to&#160;<&#160;A.to)</td>
		        		    <td style="vertical-align: middle" class="test_process">stop</td>
		        		    <td style="vertical-align: middle" class="test_time">0</td>		        		    
		        		    <td style="vertical-align: middle" class="test_result"><i>brak</i></td>		        		    
			        	</tr>			        	
					</tbody>
				</table>
				{if count(annotations_in_corpus)}
					<div class="annotations_in_corpus">
					Aktywne anotacje:
					<form class="corpusannotations">
					{foreach from=$annotations_in_corpus item=ann key=k}
						<input class="activeAnnotation" type="checkbox" name={$ann.description} value="{$ann.annotation_set_id}" id="{$ann.annotation_set_id}" />
						<label for="{$ann.annotation_set_id}">{$ann.description}</label>
					{/foreach}
					</form>
					</div>
				{/if}
				<button class="buttonTest stop">Test start</button>
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
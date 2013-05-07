{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<div id="frame_elements" style="display: none">	
	<b>Przybornik:</b>
	
	<div class="elements" id="elements_sections" style="overflow: auto; height: 100px; ">
		
		<ul>
			<li><a href="#elem-0">szkielet</a></li>
			<li><a href="#elem-8">[env]</a></li>
			<li><a href="#elem-1">[opener]</a></li>
			<li><a href="#elem-2">[p]</a></li>
			<li><a href="#elem-3">[ornament]</a></li>
			<li><a href="#elem-4">[closer]</a></li>
			<li><a href="#elem-5">[ps]</a></li>
			<li><a href="#elem-6">symbole</a></li>
			<li><a href="#elem-7">walidacja</a></li>
		</ul>
			
		<div id="elem-0">
			<p>Szkielet dokumentu składa się ze znaczników <span class="tag">text</span>, <span class="tag">body</span> i <span class="tag">pb</span>.</p><br/>					
			<a href="#" id="tei_struct">Utwórz szkielet dokumentu</a>
		</div>

		<div id="elem-8">
			[<span class="tag"><a href="#" id="element_envelope">envelope</a></span>] &mdash; koperta,<br/>
			<br/>
			<b>Znaczniki zagnieżdżone:</b>
			<ul class="elements">
				<li><span class="tag">p</span> &mdash; paragraf wewnątrz sekcji opener,<br/>
					<b>Atrybuty:</b>
					{include file="inc_report_transcription_elements_p_rend.tpl"}
					<br/>			
					<b>Pozostało znaczniki:</b>
					{include file="inc_report_transcription_elements_p_tags.tpl"}
				</li>
			</ul>							
		</div>
			
		<div id="elem-1">
			[<span class="tag"><a href="#" id="element_opener">opener</a></span>] &mdash; rozpoczęcie listu,<br/>
			<br/>
			<b>Znaczniki zagnieżdżone:</b>
			<ul class="elements">
				<li><span class="tag">dateline</span> &mdash; linia z datą,
					<ul>
						<li>[<a href="#" class="element_opener_dateline_rend"><span class="tag">dateline</span> <span class="attribute">rend</span>="<span class="value">left</span>"</a>]</span> &mdash; wyrównanie do lewej,</li>
						<li>[<a href="#" class="element_opener_dateline_rend"><span class="tag">dateline</span> <span class="attribute">rend</span>="<span class="value">center</span>"</a>]</span> &mdash; wyrównanie do środka,<br>
						<li>[<a href="#" class="element_opener_dateline_rend"><span class="tag">dateline</span> <span class="attribute">rend</span>="<span class="value">right</span>"</a>]</span> &mdash; wyrównanie do prawej,<br>
					</ul>
				</li>
				<li><span class="tag">head</span> &mdash; linia z nagłówkiem,
					<ul>
						<li>[<a href="#" class="element_head_rend"><span class="tag">head</span> <span class="attribute">rend</span>="<span class="value">left</span>"</a></span>] &mdash; wyrównanie do lewej,</li>
						<li>[<a href="#" class="element_head_rend"><span class="tag">head</span> <span class="attribute">rend</span>="<span class="value">center</span>"</a></span>] &mdash; wyrównanie do środka,<br>
						<li>[<a href="#" class="element_head_rend"><span class="tag">head</span> <span class="attribute">rend</span>="<span class="value">right</span>"</a></span>] &mdash; wyrównanie do prawej,<br>
					</ul>				
				</li>				
				<li><span class="tag">p</span> &mdash; paragraf wewnątrz sekcji opener,<br/>
					<b>Atrybuty:</b>
					{include file="inc_report_transcription_elements_p_rend.tpl"}
					<b>Typowe znaczniki:</b>
					<ul>
						<li>&laquo;<a href="#" class="element_salute"><span class="tag">salute</span></a>&raquo; &mdash; powitanie,</li>
					</ul>
					<br/>			
					<b>Pozostało znaczniki:</b>
					{include file="inc_report_transcription_elements_p_tags.tpl"}							
				</li>
			</ul>
		</div>
		
		<div id="elem-2">
			[<span class="tag">p</span>] &mdash; blok tekstu wizualnie tworzący spójny fragment tekstu,
			{include file="inc_report_transcription_elements_p_rend.tpl"}		
			<br/>			
			[<span class="tag">p</span> <span class="attribute">place</span>="<span class="value">...</span>"] &mdash; blok tekstu występujący poza główną linią narracji,
			<table class="p_place">
				<tr>
					<td>[<a href="#" class="element_p_place"><span class="value">left top</span></a>]</td>
					<td>[<a href="#" class="element_p_place"><span class="value">center top</span></a>]</td>
					<td>[<a href="#" class="element_p_place"><span class="value">right top</span></a>]</td>
				</tr>
				<tr>
					<td>[<a href="#" class="element_p_place"><span class="value">left center</span></a>]</td>
					<td>treść strony</td>
					<td>[<a href="#" class="element_p_place"><span class="value">right center</span></a>]</td>
				</tr>
				<tr>
					<td>[<a href="#" class="element_p_place"><span class="value">left bottom</span></a>]</td>
					<td>[<a href="#" class="element_p_place"><span class="value">center bottom</span></a>]</td>
					<td>[<a href="#" class="element_p_place"><span class="value">right bottom</span></a>]</td>
				</tr>
			</table>
			<br/>

			<b>Znaczniki zagnieżdżone:</b>
			{include file="inc_report_transcription_elements_p_tags.tpl"}		
		</div>		
		
		<div id="elem-3"><span class="tag">ornament</span> &mdash; linia rozdzielająca tekst,
			<ul class="elements">
				<li>[<a href="#" class="element_ornament"><span class="tag">ornament</span> <span class="attribute">type</span>="<span class="value">characters</span>"</a>] &mdash; ciąg gwiazdek itp.,</li>
				<li>[<a href="#" class="element_ornament"><span class="tag">ornament</span> <span class="attribute">type</span>="<span class="value">line</span>"</a>] &mdash; prosta linia pozioma,</li>
				<li>[<a href="#" class="element_ornament"><span class="tag">ornament</span> <span class="attribute">type</span>="<span class="value">space</span>"</a>] &mdash; pionowy odstęp,</li>
				<li>[<a href="#" class="element_ornament"><span class="tag">ornament</span> <span class="attribute">type</span>="<span class="value">wave</span>"</a>] &mdash; falista linia pozioma,</li>
			</ul>
		</div>
		
		<div id="elem-4">[<span class="tag">closer</span>] &mdash; zakończenie listu
			<ul class="elements">
				<li>[<a href="#" id="element_closer"><span class="tag">closer</span></a>] &mdash; od nowej linii,</li>
				<li>[<a href="#" id="element_closer_inline"><span class="tag">closer</span> <span class="attribute">rend</span>="<span class="value">inline</span>"</a>] &mdash; zaczyna się w ostatniej linii poprzedniego akapitu,</li>
			</ul>
			<br/>
			<b>Znaczniki zagnieżdżone:</b>
			<ul class="elements">
				<li><span class="tag">dateline</span> &mdash; linia z datą,
					<ul>
						<li>[<a href="#" class="element_opener_dateline_rend"><span class="tag">dateline</span> <span class="attribute">rend</span>="<span class="value">left</span>"</a>]</span> &mdash; wyrównanie do lewej,</li>
						<li>[<a href="#" class="element_opener_dateline_rend"><span class="tag">dateline</span> <span class="attribute">rend</span>="<span class="value">center</span>"</a>]</span> &mdash; wyrównanie do środka,<br>
						<li>[<a href="#" class="element_opener_dateline_rend"><span class="tag">dateline</span> <span class="attribute">rend</span>="<span class="value">right</span>"</a>]</span> &mdash; wyrównanie do prawej,<br>
					</ul>
				</li>
				<li><span class="tag">p</span> &mdash; paragraf wewnątrz sekcji closer,<br/>
					<b>Atrybuty:</b>
					{include file="inc_report_transcription_elements_p_rend.tpl"}		
					<b>Typowe znaczniki:</b>
					<ul>
						<li>&laquo;<a href="#" class="element_salute"><span class="tag">salute</span></a>&raquo; &mdash; pożegnanie,</li>
						<li>&laquo;<a href="#" id="element_signed"><span class="tag">signed</span></a>&raquo; &mdash; podpis autora,</li>
					</ul>
					<br/>			
					<b>Pozostało znaczniki:</b>
					{include file="inc_report_transcription_elements_p_tags.tpl"}		
				</li>
			</ul>
		</div>
		
		<div id="elem-5">[<a href="#" id="element_ps"><span class="tag">ps</span></a>] &mdash; sekcja post scriptum,<br/>
			<br/>
			<b>Znaczniki zagnieżdżone:</b>
			<ul class="elements">
				<li>[<span class="tag">p</span> <span class="attribute">type</span>="<span class="value">meta</span>"] &mdash; rozpoczęcie sekcji,
					<ul>
						<li>[<a href="#" id="element_ps_p_meta_block"><span class="tag">p</span> <span class="attribute">type</span>="<span class="value">meta</span>"</a>] &mdash; w oddzielnej linii,</li>
						<li>[<a href="#" id="element_ps_p_meta_inline"><span class="tag">p</span> <span class="attribute">type</span>="<span class="value">meta</span>" <span class="attribute">rend</span>="<span class="value">inline</span>"</a>] &mdash; w tej samej linii co treść ps,</li>
					</ul>
				</li>
				<li>[<span class="tag">p</span>] &mdash; blok tekstu w sekcji ps,
					<b>Atrybuty:</b>
					{include file="inc_report_transcription_elements_p_rend.tpl"}
					<br/>		
					<b>Znaczniki:</b>
					{include file="inc_report_transcription_elements_p_tags.tpl"}		
				</li>
			</ul>	
		</div>
		
		<div id="elem-6">
			<ul id="list_of_symbols">
				<li><a href="#" title="kropka na wysokości połowy wiersza">·</a></li>
				<li><a href="#" title="gwiazdka na wysokości połowy wiersza">∗</a></li>
				<li><a href="#" title="polski cudzysłów otwierający">„</a></li>
				<li><a href="#" title="polski cudzysłów zamykający">”</a></li>		
			</ul>
		</div>

		<div id="elem-7">
			<input type="button" value="Sprawdź poprawność struktury dokumentu" id="validate"/>
			<div id="validate_result">
				<ol></ol>
			</div>
		</div>		
	</div>
	
</div>
<div id="frame_elements">
	<b>Elementry struktury dokumentu:</b>
	<div class="elements"> 
	<h3><a href="#" id="tei_struct">Utwórz szkielet dokumentu</a></h3>
	<ol class="elements">		
		<li><span class="tag"><a href="#" id="element_opener">opener</a></span> &mdash; rozpoczęcie listu,
			<ul>
				<li><span class="tag">dateline</span> &mdash; linia z datą,
					<ul>
						<li><a href="#" class="element_p_rend"><span class="attribute">rend</span>="<span class="value">left</span>"</a></span> &mdash; wyrównanie do lewej,</li>
						<li><a href="#" class="element_p_rend"><span class="attribute">rend</span>="<span class="value">center</span>"</a></span> &mdash; wyrównanie do środka,<br>
						<li><a href="#" class="element_p_rend"><span class="attribute">rend</span>="<span class="value">right</span>"</a></span> &mdash; wyrównanie do prawej,<br>
					</ul>
				</li>
				<li><a href="#" class="element_p"><span class="tag">p</span></a> &mdash; paragraf wewnątrz sekcji opener,<br/>
					<b>Atrybuty:</b>
					<ul>
						<li><a href="#" class="element_p_rend"><span class="attribute">rend</span>="<span class="value">left</span>"</a></span> &mdash; wyrównanie do lewej,</li>
						<li><a href="#" class="element_p_rend"><span class="attribute">rend</span>="<span class="value">center</span>"</a></span> &mdash; wyrównanie do środka,<br>
						<li><a href="#" class="element_p_rend"><span class="attribute">rend</span>="<span class="value">right</span>"</a></span> &mdash; wyrównanie do prawej,<br>
					</ul>
					<b>Znaczniki:</b>
					<ul>
						<li><a href="#" class="element_salute"><span class="tag">salute</span></a> &mdash; powitanie,</li>
					</ul>
				</li>
			</ul>
		</li>
		<li><a href="#" class="element_p"><span class="tag">p</span></a> &mdash; paragraf,<br/>
			<b>Atrybuty:</b>
			<ul>
				<li><a href="#" class="element_p_rend"><span class="attribute">rend</span>="<span class="value">left</span>"</a></span> &mdash; wyrównanie do lewej,</li>
				<li><a href="#" class="element_p_rend"><span class="attribute">rend</span>="<span class="value">center</span>"</a></span> &mdash; wyrównanie do środka,<br>
				<li><a href="#" class="element_p_rend"><span class="attribute">rend</span>="<span class="value">right</span>"</a></span> &mdash; wyrównanie do prawej,<br>
				<li><a href="#" class="element_p_rend"><span class="attribute">rend</span>="<span class="value">indent</span>"</a></span> &mdash; wcięcie pierwszej linii,</li>
				<li><a href="#" class="element_p_rend"><span class="attribute">rend</span>="<span class="value">margin-left</span>"</a></span> &mdash; margines z lewej strony,</li>
			</ul>
			<b>Znaczniki:</b>
			<ul class="elements">
				<li><a href="#" id="element_p_add"><span class="tag">add</span></a> &mdash; wstawienie liter, wyrazów lub fraz,</li>
				<li><span class="tag">corr</span> &mdash; korekta błędu gramatycznego
					<ul>
						<li><a href="#" id="element_corr_author"><span class="attribute">resp</span>="<span class="value">author</span>"</a></span> &mdash; poprawka naniesiona przez piszącego (autorska),</li>
						<li><a href="#" id="element_corr_editor"><span class="attribute">resp</span>="<span class="value">editor</span>"</a></span> &mdash; autor popełnił błąd językowy, anotator go poprawił (anotatora),<br>
							Wartości dla <span class="attribute">type</span>="...":
							<ul>
								<li><span class="value"><a href="#" class="element_corr_editor">capital</a></span> &mdash; małe/wielkie litery,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">cons-alter</a></span> &mdash; podobne spółgłoski,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">flex</a></span> &mdash; błąd fleksyjny (odmiany),</li>
								<li><span class="value"><a href="#" class="element_corr_editor">hyphenation</a></span> &mdash; dzielenie słowa między liniami,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">i-repr</a></span> &mdash; zapis "i",</li>
								<li><span class="value"><a href="#" class="element_corr_editor">nasal</a></span> &mdash; nosowość,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">orth</a></span> &mdash; błąd ortograficzny,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">soft-repr</a></span> &mdash; zmiększenia,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">sep-comp</a></span> &mdash; pisownia rozdzielna (jest "kręgo słup" zamiast "kręgosłup"),</li>
								<li><span class="value"><a href="#" class="element_corr_editor">unsep-comp</a></span> &mdash; pisownia łączna (jest "niewiem" zamiast "nie wiem"),</li>
								<li><span class="value"><a href="#" class="element_corr_editor">typ-anom</a></span> &mdash; literówki,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">unvoic-cons</a></span> &mdash; ubezdźwięcznianie,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">voic-cons</a></span> &mdash; udźwięcznianie,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">vowel-alter</a></span> &mdash; podobne samogłoski,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">other</a></span> &mdash; inne.</li>
							</ul>
						</li>
					</ul>
				</li>
				<li><a href="#" id="element_p_del"><span class="tag">del</span></a> &mdash; skreślenie,</li>
				<li><span class="tag">figure</span> &mdash; symbol graficzny,
					<ul>
						<li><a href="#" class="element_figure_type" title="arrow"><span class="attribute">type</span>="<span class="value">arrow</span>"</a> (<a href="#" title="wstawia znacznik z możliwością dodania tekstu" class="element_figure_open" val="arrow">z tekstem</a>) &mdash; strzałka,</li>
						<li><a href="#" class="element_figure_type" title="emotikon"><span class="attribute">type</span>="<span class="value">emotikon</span>"</a> (<a href="#" title="wstawia znacznik z możliwością dodania tekstu" class="element_figure_open" val="emotikon">z tekstem</a>) &mdash; emotikonka,</li>
						<li><a href="#" class="element_figure_type" title="heart"><span class="attribute">type</span>="<span class="value">heart</span>"</a> (<a href="#" title="wstawia znacznik z możliwością dodania tekstu" class="element_figure_open" val="heart">z tekstem</a>) &mdash; serce,</li>
					</ul>
				</li>
				<li><span class="tag">gap</span> &mdash; opuszczone, nieczytelne,
					<ul>
						<li><a href="#" class="element_gap_reason" title="illegible"><span class="attribute">reason</span>="<span class="value">illegible</span>"</a> &mdash; nieczytelny,</li>
						<li><a href="#" class="element_gap_reason" title="missing"><span class="attribute">reason</span>="<span class="value">missing</span>"</a> &mdash; pominięty,</li>
						<li><a href="#" class="element_gap_reason" title="prosecutor"><span class="attribute">reason</span>="<span class="value">prosecutor</span>"</a> &mdash; zamazane przez prokuraturę,</li>
					</ul>								
				</li>
				<li><a href="#" id="element_head"><span class="tag">head</span></a> &mdash; linia z nagłówkiem,</li>				
				<li><span class="tag">hi</span> &mdash; wyróżnienie,
					<ul>
						<li><a href="#" class="element_hi_rend" title="bold"><span class="attribute">rend</span>="<span class="value">bold</span>"</a> &mdash; wytłuszczono poprzez nadpisanie,</li>
						<li><a href="#" class="element_hi_rend" title="underline"><span class="attribute">rend</span>="<span class="value">underline</span>"</a> &mdash; podkreślono,</li>
						<li><a href="#" class="element_hi_rend" title="uppercase"><span class="attribute">rend</span>="<span class="value">uppercase</span>"</a> &mdash; użyto drukowanych liter,</li>
					</ul>
				</li>
				<li><a href="#" id="element_salute"><span class="tag">salute</span></a> &mdash; powitanie, pożegnanie,</li>
				<li><a href="#" id="element_p_lb"><span class="tag">lb</span></a> &mdash; łamanie linii,</li>				
				<li><span class="tag">unclear</span> &mdash; częściowo nieczytelny tekst,
					<ul>
						<li><a href="#" class="element_unclear_cert" title="high"><span class="attribute">cert</span>="<span class="value">high</span>"</a> &mdash; duża pewność,</li>
						<li><a href="#" class="element_unclear_cert" title="normal"><span class="attribute">cert</span>="<span class="value">normal</span>"</a> &mdash; średnia pewność,</li>
						<li><a href="#" class="element_unclear_cert" title="low"><span class="attribute">cert</span>="<span class="value">low</span>"</a> &mdash; niska pewność,</li>
						<li><a href="#" class="element_unclear_cert" title="unknown"><span class="attribute">cert</span>="<span class="value">unknown</span>"</a> &mdash; nieznane,</li>
					</ul>				
				</li>				
			</ul>		
		</li>		
		<li><span class="tag">ornament</span> &mdash; linia rozdzielająca tekst. Rodzaje:
			<ul>
				<li><a href="#" class="ornament"><span class="attribute">type</span>="<span class="value">line</span>"</a> &mdash; zwykła linia pozioma</li>
				<li><a href="#" class="ornament"><span class="attribute">type</span>="<span class="value">characters</span>"</a> &mdash; ciąg gwiazdek itp.</li>
			</ul>
		</li>
		<li><a href="#" id="element_closer"><span class="tag">closer</span></a> &mdash; zakończenie listu
			<ul>
				<li><a href="#" class="element_p"><span class="tag">p</span></a> &mdash; paragraf wewnątrz sekcji closer,<br/>
					<b>Atrybuty:</b>
					<ul>
						<li><a href="#" class="element_p_rend"><span class="attribute">rend</span>="<span class="value">left</span>"</a></span> &mdash; wyrównanie do lewej,</li>
						<li><a href="#" class="element_p_rend"><span class="attribute">rend</span>="<span class="value">center</span>"</a></span> &mdash; wyrównanie do środka,<br>
						<li><a href="#" class="element_p_rend"><span class="attribute">rend</span>="<span class="value">right</span>"</a></span> &mdash; wyrównanie do prawej,<br>
					</ul>
					<b>Znaczniki:</b>
					<ul>
						<li><a href="#" class="element_salute"><span class="tag">salute</span></a> &mdash; pożegnanie,</li>
						<li><a href="#" id="element_signed"><span class="tag">signed</span></a> &mdash; podpis autora,</li>
					</ul>
				</li>
			</ul>
		</li>
		<li><a href="#" id="element_ps"><span class="tag">ps</span></a> &mdash; sekcja post scriptum
			<ul>
				<li><a href="#" id="element_ps_meta"><span class="tag">meta</span></a> &mdash; rozpoczęcie sekcji</li>
				<li><a href="#" id="element_ps_content"><span class="tag">paragraf</span></a> &mdash; element ps</li>
			</ul>	
		</li>
	</ol>
	</div>
</div>
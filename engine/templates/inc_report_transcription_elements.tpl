<div id="frame_elements">
	<b>Elementry struktury dokumentu:</b>
	<div class="elements"> 
	<h3><a href="#" id="tei_struct">Utwórz szkielet dokumentu</a></h3>
	<ul class="elements">
		<li><span class="tag"><a href="#" id="element_opener">opener</a></span> &mdash; rozpoczęcie listu,
			<ul>
				<li><span class="tag">dateline</span> &mdash; linia z datą,
					<ul>
						<li><a href="#" class="element_opener_dateline_rend"><span class="attribute">rend</span>="<span class="value">left</span>"</a></span> &mdash; wyrównanie do lewej,</li>
						<li><a href="#" class="element_opener_dateline_rend"><span class="attribute">rend</span>="<span class="value">center</span>"</a></span> &mdash; wyrównanie do środka,<br>
						<li><a href="#" class="element_opener_dateline_rend"><span class="attribute">rend</span>="<span class="value">right</span>"</a></span> &mdash; wyrównanie do prawej,<br>
					</ul>
				</li>
				<li><span class="tag">salute</span> &mdash; linia z powitaniem,
					<ul>
						<li><a href="#" class="element_opener_salute_rend"><span class="attribute">rend</span>="<span class="value">left</span>"</a></span> &mdash; wyrównanie do lewej,</li>
						<li><a href="#" class="element_opener_salute_rend"><span class="attribute">rend</span>="<span class="value">center</span>"</a></span> &mdash; wyrównanie do środka,<br>
						<li><a href="#" class="element_opener_salute_rend"><span class="attribute">rend</span>="<span class="value">right</span>"</a></span> &mdash; wyrównanie do prawej,<br>
					</ul>
				</li>
			</ul>
		</li>
		<li><a href="#" id="element_p"><span class="tag">p</span></a> &mdash; paragraf,
			<ul class="elements">
				<li><a href="#" id="element_p_add"><span class="tag">add</span></a> &mdash; wstawienie liter, wyrazów lub fraz,</li>
				<li><span class="tag">corr</span> &mdash; korekta błędu gramatycznego
					<ul>
						<li><a href="#" id="element_corr_author"><span class="attribute">resp</span>="<span class="value">author</span>"</a></span> &mdash; korekta autorska,</li>
						<li><a href="#" id="element_corr_editor"><span class="attribute">resp</span>="<span class="value">editor</span>"</a></span> &mdash; korekta edytorska,<br>
							Wartości dla <span class="attribute">type</span>="...":
							<ul>
								<li><span class="value"><a href="#" class="element_corr_editor">soft-repr</a></span> &mdash; zmiększenia,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">i-repr</a></span> &mdash; zapis "i",</li>
								<li><span class="value"><a href="#" class="element_corr_editor">voic-cons</a></span> &mdash; udźwięcznianie,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">unvoic-cons</a></span> &mdash; ubezdźwięcznianie,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">nasal</a></span> &mdash; nosowość,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">typ-anom</a></span> &mdash; literówki,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">sep-comp</a></span> &mdash; pisownia rozdzielna,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">unsep-comp</a></span> &mdash; pisownia łączna,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">capital</a></span> &mdash; małe/wielkie litery,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">cons-alter</a></span> &mdash; podobne spółgłoski,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">vowel-alter</a></span> &mdash; podobne samogłoski,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">punct</a></span> &mdash; interpunkcja,</li>
								<li><span class="value"><a href="#" class="element_corr_editor">other</a></span> &mdash; inne.</li>
							</ul>
						</li>
					</ul>
				</li>
				<li><a href="#" id="element_p_del"><span class="tag">del</span></a> &mdash; skreślenie,</li>
				<li><span class="tag">figure</span> &mdash; symbol graficzny,
					<ul>
						<li><a href="#" class="element_figure_type" title="emotikon"><span class="attribute">type</span>="<span class="value">emotikon</span>"</a> &mdash; emotikonka,</li>
						<li><a href="#" class="element_figure_type" title="heart"><span class="attribute">type</span>="<span class="value">heart</span>"</a> &mdash; serce,</li>
					</ul>
				</li>
				<li><span class="tag">gap</span> &mdash; opuszczone, nieczytelne,
					<ul>
						<li><a href="#" class="element_gap_reason" title="illegible"><span class="attribute">reason</span>="<span class="value">illegible</span>"</a> &mdash; nieczytelny,</li>
						<li><a href="#" class="element_gap_reason" title="missing"><span class="attribute">reason</span>="<span class="value">missing</span>"</a> &mdash; pominięty,</li>
						<li><a href="#" class="element_gap_reason" title="prosecutor"><span class="attribute">reason</span>="<span class="value">prosecutor</span>"</a> &mdash; zamazane przez prokuraturę,</li>
					</ul>								
				</li>
				<li><span class="tag">hi</span> &mdash; wyróżnienie,
					<ul>
						<li><a href="#" class="element_hi_rend" title="bold"><span class="attribute">rend</span>="<span class="value">bold</span>"</a> &mdash; wytłuszczono poprzez nadpisanie,</li>
						<li><a href="#" class="element_hi_rend" title="underline"><span class="attribute">rend</span>="<span class="value">underline</span>"</a> &mdash; podkreślono,</li>
						<li><a href="#" class="element_hi_rend" title="uppercase"><span class="attribute">rend</span>="<span class="value">uppercase</span>"</a> &mdash; użyto drukowanych liter,</li>
					</ul>
				</li>
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
		<li><a href="#" id="element_closer"><span class="tag">closer</span></a> &mdash; podpis autora
			<ul>
				<li><span class="tag">salute</span>
					<ul>
						<li><a href="#" class="element_closer_salute_rend"><span class="attribute">rend</span>="<span class="value">left</span>"</a></span> &mdash; wyrównanie do lewej,</li>
						<li><a href="#" class="element_closer_salute_rend"><span class="attribute">rend</span>="<span class="value">center</span>"</a></span> &mdash; wyrównanie do środka,<br>
						<li><a href="#" class="element_closer_salute_rend"><span class="attribute">rend</span>="<span class="value">right</span>"</a></span> &mdash; wyrównanie do prawej,<br>
					</ul>
				</li>
				<li><span class="tag">signed</span>
					<ul>
						<li><a href="#" class="element_closer_signed_rend"><span class="attribute">rend</span>="<span class="value">left</span>"</a></span> &mdash; wyrównanie do lewej,</li>
						<li><a href="#" class="element_closer_signed_rend"><span class="attribute">rend</span>="<span class="value">center</span>"</a></span> &mdash; wyrównanie do środka,<br>
						<li><a href="#" class="element_closer_signed_rend"><span class="attribute">rend</span>="<span class="value">right</span>"</a></span> &mdash; wyrównanie do prawej,<br>
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
	</ul>
	</div>
</div>
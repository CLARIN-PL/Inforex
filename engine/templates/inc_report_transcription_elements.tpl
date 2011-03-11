<div id="frame_elements">
	<b>Elementry struktury dokumentu:</b>
	<div class="elements"> 
	<h3><a href="#" id="tei_struct">Utwórz szkielet dokumentu</a></h3>
	<ol class="elements">		
		<li>[<span class="tag"><a href="#" id="element_opener">opener</a></span>] &mdash; rozpoczęcie listu,
			<ul>
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
					<ul>
						<li>[<a href="#" class="element_p_rend"><span class="tag">p</span> <span class="attribute">rend</span>="<span class="value">left</span>"</a></span>] &mdash; wyrównanie do lewej,</li>
						<li>[<a href="#" class="element_p_rend"><span class="tag">p</span> <span class="attribute">rend</span>="<span class="value">center</span>"</a></span>] &mdash; wyrównanie do środka,<br>
						<li>[<a href="#" class="element_p_rend"><span class="tag">p</span> <span class="attribute">rend</span>="<span class="value">right</span>"</a></span>] &mdash; wyrównanie do prawej,<br>
					</ul>
					<b>Znaczniki:</b>
					<ul>
						<li>&laquo;<a href="#" class="element_salute"><span class="tag">salute</span></a>&raquo; &mdash; powitanie,</li>
					</ul>
				</li>
			</ul>
		</li>
		<li><span class="tag">p</span> &mdash; paragraf,<br/>
			<b>Atrybuty:</b>
			<ul>
				<li>[<a href="#" class="element_p_rend"><span class="tag">p</span> <span class="attribute">rend</span>="<span class="value">left</span>"</a></span>] &mdash; wyrównanie do lewej,</li>
				<li>[<a href="#" class="element_p_rend"><span class="tag">p</span> <span class="attribute">rend</span>="<span class="value">center</span>"</a></span>] &mdash; wyrównanie do środka,<br>
				<li>[<a href="#" class="element_p_rend"><span class="tag">p</span> <span class="attribute">rend</span>="<span class="value">right</span>"</a></span>] &mdash; wyrównanie do prawej,<br>
				<li>[<a href="#" class="element_p_rend"><span class="tag">p</span> <span class="attribute">rend</span>="<span class="value">indent</span>"</a></span>] &mdash; wcięcie pierwszej linii,</li>
				<li>[<a href="#" class="element_p_rend"><span class="tag">p</span> <span class="attribute">rend</span>="<span class="value">margin-left</span>"</a></span>] &mdash; margines z lewej strony,</li>
			</ul>
			<b>Znaczniki:</b>
			<ul class="elements">
				<li>&laquo;<a href="#" id="element_p_add"><span class="tag">add</span></a>&raquo; &mdash; wstawienie liter, wyrazów lub fraz,</li>
				<li><span class="tag">corr</span> &mdash; korekta błędu gramatycznego
					<ul>
						<li>&laquo;<a href="#" id="element_corr_author"><span class="tag">corr</span> <span class="attribute">resp</span>="<span class="value">author</span>"</a></span>&raquo; &mdash; poprawka naniesiona przez piszącego (autorska),</li>
						<li>&laquo;<a href="#" id="element_corr_editor"><span class="tag">corr</span> <span class="attribute">resp</span>="<span class="value">editor</span>"</a></span>&raquo; &mdash; autor popełnił błąd językowy, anotator go poprawił (anotatora),<br>
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
				<li>&laquo;<a href="#" id="element_p_del"><span class="tag">del</span></a>&raquo; &mdash; skreślenie,</li>
				<li><span class="tag">figure</span> &mdash; symbol graficzny,
					<ul>
						<li>&laquo;<a href="#" class="element_figure_type" title="arrow"><span class="tag">figure</span> <span class="attribute">type</span>="<span class="value">arrow</span>"</a>&raquo; (<a href="#" title="wstawia znacznik z możliwością dodania tekstu" class="element_figure_open" val="arrow">z tekstem</a>) &mdash; strzałka,</li>
						<li>&laquo;<a href="#" class="element_figure_type" title="cross"><span class="tag">figure</span> <span class="attribute">type</span>="<span class="value">cross</span>"</a>&raquo; (<a href="#" title="wstawia znacznik z możliwością dodania tekstu" class="element_figure_open" val="heart">z tekstem</a>) &mdash; krzyż,</li>
						<li>&laquo;<a href="#" class="element_figure_type" title="emotikon"><span class="tag">figure</span> <span class="attribute">type</span>="<span class="value">emotikon</span>"</a>&raquo; (<a href="#" title="wstawia znacznik z możliwością dodania tekstu" class="element_figure_open" val="emotikon">z tekstem</a>) &mdash; emotikonka,</li>
						<li>&laquo;<a href="#" class="element_figure_type" title="heart"><span class="tag">figure</span> <span class="attribute">type</span>="<span class="value">heart</span>"</a>&raquo; (<a href="#" title="wstawia znacznik z możliwością dodania tekstu" class="element_figure_open" val="heart">z tekstem</a>) &mdash; serce,</li>
					</ul>
				</li>
				<li><span class="tag">gap</span> &mdash; opuszczone, nieczytelne,
					<ul>
						<li>&laquo;<a href="#" class="element_gap_reason" title="illegible"><span class="tag">gap</span> <span class="attribute">reason</span>="<span class="value">illegible</span>"</a>&raquo; &mdash; nieczytelny,</li>
						<li>&laquo;<a href="#" class="element_gap_reason" title="missing"><span class="tag">gap</span> <span class="attribute">reason</span>="<span class="value">missing</span>"</a>&raquo; &mdash; pominięty,</li>
						<li>&laquo;<a href="#" class="element_gap_reason" title="prosecutor"><span class="tag">gap</span> <span class="attribute">reason</span>="<span class="value">prosecutor</span>"</a>&raquo; &mdash; zamazane przez prokuraturę,</li>
					</ul>								
				</li>
				<li><span class="tag">hi</span> &mdash; wyróżnienie,
					<ul>
						<li>&laquo;<a href="#" class="element_hi_rend"><span class="tag">hi</span> <span class="attribute">rend</span>="<span class="value">bold</span>"</a>&raquo; &mdash; wytłuszczono poprzez nadpisanie,</li>
						<li>&laquo;<a href="#" class="element_hi_rend"><span class="tag">hi</span> <span class="attribute">rend</span>="<span class="value">underline</span>"</a>&raquo; &mdash; podkreślono,</li>
						<li>&laquo;<a href="#" class="element_hi_rend"><span class="tag">hi</span> <span class="attribute">rend</span>="<span class="value">uppercase</span>"</a>&raquo; &mdash; użyto drukowanych liter,</li>
					</ul>
				</li>
				<li>&laquo;<a href="#" id="element_salute"><span class="tag">salute</span></a>&raquo; &mdash; powitanie, pożegnanie,</li>
				<li>&laquo;<a href="#" id="element_p_lb"><span class="tag">lb</span></a>&raquo; &mdash; łamanie linii,</li>				
				<li><span class="tag">unclear</span> &mdash; częściowo nieczytelny tekst,
					<ul>
						<li>&laquo;<a href="#" class="element_unclear_cert" title="high"><span class="tag">unclear</span> <span class="attribute">cert</span>="<span class="value">high</span>"</a>&raquo; &mdash; duża pewność,</li>
						<li>&laquo;<a href="#" class="element_unclear_cert" title="normal"><span class="tag">unclear</span> <span class="attribute">cert</span>="<span class="value">normal</span>"</a>&raquo; &mdash; średnia pewność,</li>
						<li>&laquo;<a href="#" class="element_unclear_cert" title="low"><span class="tag">unclear</span> <span class="attribute">cert</span>="<span class="value">low</span>"</a>&raquo; &mdash; niska pewność,</li>
						<li>&laquo;<a href="#" class="element_unclear_cert" title="unknown"><span class="tag">unclear</span> <span class="attribute">cert</span>="<span class="value">unknown</span>"</a>&raquo; &mdash; nieznane,</li>
					</ul>				
				</li>				
			</ul>		
		</li>		
		<li><span class="tag">ornament</span> &mdash; linia rozdzielająca tekst. Rodzaje:
			<ul>
				<li>[<a href="#" class="element_ornament"><span class="tag">ornament</span> <span class="attribute">type</span>="<span class="value">characters</span>"</a>] &mdash; ciąg gwiazdek itp.</li>
				<li>[<a href="#" class="element_ornament"><span class="tag">ornament</span> <span class="attribute">type</span>="<span class="value">line</span>"</a>] &mdash; prosta linia pozioma</li>
				<li>[<a href="#" class="element_ornament"><span class="tag">ornament</span> <span class="attribute">type</span>="<span class="value">wave</span>"</a>] &mdash; falista linia pozioma</li>
			</ul>
		</li>
		<li>[<a href="#" id="element_closer"><span class="tag">closer</span></a>] &mdash; zakończenie listu
			<ul>
				<li><span class="tag">p</span> &mdash; paragraf wewnątrz sekcji closer,<br/>
					<b>Atrybuty:</b>
					<ul>
						<li>[<a href="#" class="element_p_rend"><span class="tag">p</span> <span class="attribute">rend</span>="<span class="value">left</span>"</a>] &mdash; wyrównanie do lewej,</li>
						<li>[<a href="#" class="element_p_rend"><span class="tag">p</span> <span class="attribute">rend</span>="<span class="value">center</span>"</a>] &mdash; wyrównanie do środka,<br>
						<li>[<a href="#" class="element_p_rend"><span class="tag">p</span> <span class="attribute">rend</span>="<span class="value">right</span>"</a>] &mdash; wyrównanie do prawej,<br>
					</ul>
					<b>Znaczniki:</b>
					<ul>
						<li>&laquo;<a href="#" class="element_salute"><span class="tag">salute</span></a>&raquo; &mdash; pożegnanie,</li>
						<li>&laquo;<a href="#" id="element_signed"><span class="tag">signed</span></a>&raquo; &mdash; podpis autora,</li>
					</ul>
				</li>
			</ul>
		</li>
		<li>[<a href="#" id="element_ps"><span class="tag">ps</span></a>] &mdash; sekcja post scriptum
			<ul>
				<li>[<a href="#" id="element_ps_meta"><span class="tag">meta</span></a>] &mdash; rozpoczęcie sekcji</li>
				<li>[<a href="#" id="element_ps_content"><span class="tag">paragraf</span></a>] &mdash; element ps</li>
			</ul>	
		</li>
	</ol>
	<b>Symbole:</b><br/>
	<ul id="list_of_symbols">
		<li><a href="#" title="kropka na wysokości połowy wiersza">·</a></li>
		<li><a href="#" title="gwiazdka na wysokości połowy wiersza">∗</a></li>
		<li><a href="#" title="polski cudzysłów otwierający">„</a></li>
		<li><a href="#" title="polski cudzysłów zamykający">”</a></li>		
	</ul>
	</div>
</div>
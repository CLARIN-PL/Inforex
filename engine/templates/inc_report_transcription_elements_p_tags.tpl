{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<ul class="elements">
	<li>&laquo;<span class="tag">add</span></a>&raquo; &mdash; wstawienie liter, wyrazów lub fraz,
		<ul>
			<li>&laquo;<a href="#" class="element_add"><span class="tag">add</span> <span class="attribute">place</span>="<span class="value">above</span>"</a>&raquo; &mdash; tekst dopisany nad linią,</li>
			<li>&laquo;<a href="#" class="element_add"><span class="tag">add</span> <span class="attribute">place</span>="<span class="value">below</span>"</a>&raquo; &mdash; tekst dopisany pod linią,</li>
			<li>&laquo;<a href="#" class="element_add"><span class="tag">add</span> <span class="attribute">place</span>="<span class="value">inline</span>"</a>&raquo; &mdash; tekst dopisany w linii między słowami,</li>
		</ul>
	</li>
	<li>&laquo;<span class="tag">corr</span>&raquo; &mdash; korekta błędu gramatycznego
		<ul>
			<li>&laquo;<a href="#" class="element_corr_author"><span class="tag">corr</span> <span class="attribute">resp</span>="<span class="value">author</span>"</a></span>&raquo; &mdash; poprawka naniesiona przez piszącego (autorska),</li>
			<li>&laquo;<a href="#" class="element_corr_editor"><span class="tag">corr</span> <span class="attribute">resp</span>="<span class="value">editor</span>"</a></span>&raquo; &mdash; autor popełnił błąd językowy, anotator go poprawił (anotatora),<br>
				Wartości dla <span class="attribute">type</span>="...":
				<ul>
					<li><span class="value"><a href="#" class="element_corr_editor_type">capital</a></span> &mdash; małe/wielkie litery,</li>
					<li><span class="value"><a href="#" class="element_corr_editor_type">cons-alter</a></span> &mdash; podobne spółgłoski,</li>
					<li><span class="value"><a href="#" class="element_corr_editor_type">flex</a></span> &mdash; błąd fleksyjny (odmiany),</li>
					<li><span class="value"><a href="#" class="element_corr_editor_type">hypercorr</a></span> &mdash; hiperpoprawność,</li>
					<li><span class="value"><a href="#" class="element_corr_editor_type">hyphenation</a></span> &mdash; dzielenie słowa między liniami,</li>
					<li><span class="value"><a href="#" class="element_corr_editor_type">j-ji-repr</a></span> &mdash; zapis „i”,</li>
					<li><span class="value"><a href="#" class="element_corr_editor_type">nasal</a></span> &mdash; nosowość,</li>
					<li><span class="value"><a href="#" class="element_corr_editor_type">orth</a></span> &mdash; błąd ortograficzny,</li>
                    <li><span class="value"><a href="#" class="element_corr_editor_type">sep-comp</a></span> &mdash; pisownia rozdzielna (jest "kręgo słup" zamiast "kręgosłup"),</li>
					<li><span class="value"><a href="#" class="element_corr_editor_type">soft-repr</a></span> &mdash; zmiękczenia,</li>
                    <li><span class="value"><a href="#" class="element_corr_editor_type">spoken</a></span> &mdash; mówiony,</li>
					<li><span class="value"><a href="#" class="element_corr_editor_type">unsep-comp</a></span> &mdash; pisownia łączna (jest "niewiem" zamiast "nie wiem"),</li>
					<li><span class="value"><a href="#" class="element_corr_editor_type">typ-anom</a></span> &mdash; literówki,</li>
					<li><span class="value"><a href="#" class="element_corr_editor_type">unvoic-cons</a></span> &mdash; ubezdźwięcznianie,</li>
					<li><span class="value"><a href="#" class="element_corr_editor_type">voic-cons</a></span> &mdash; udźwięcznianie,</li>
					<li><span class="value"><a href="#" class="element_corr_editor_type">vowel-alter</a></span> &mdash; podobne samogłoski,</li>
					<li><span class="value"><a href="#" class="element_corr_editor_type">other</a></span> &mdash; inne.</li>
				</ul>
			</li>
		</ul>
	</li>
	<li>&laquo;<span class="tag">del</span>&raquo; &mdash; skreślenie tekstu dokonane przez autora listu,
		<ul>
			<li>&laquo;<a href="#" class="element_del"><span class="tag">del</span> <span class="attribute">type</span>="<span class="value">strikeout</span>"</a>&raquo; &mdash; pojedyncze przekreślenie,</li>
			<li>&laquo;<a href="#" class="element_del"><span class="tag">del</span> <span class="attribute">type</span>="<span class="value">crossout</span>"</a>&raquo; &mdash; przekreślenie X,</li>
		</ul>
	</li>
	<li>&laquo;<span class="tag">figure</span>&raquo; &mdash; symbol graficzny,
		<ul>
			<li>Warianty:
				<ul>
					<li>&laquo;<a href="#" class="element_figure_type" title="arrow"><span class="tag">figure</span> <span class="attribute">type</span>="<span class="value">arrow</span>"</a>&raquo; (<a href="#" title="wstawia znacznik z możliwością dodania tekstu" class="element_figure_open" val="arrow">z tekstem</a>) &mdash; strzałka,</li>
					<li>&laquo;<a href="#" class="element_figure_type" title="cross"><span class="tag">figure</span> <span class="attribute">type</span>="<span class="value">cross</span>"</a>&raquo; (<a href="#" title="wstawia znacznik z możliwością dodania tekstu" class="element_figure_open" val="heart">z tekstem</a>) &mdash; krzyż,</li>
					<li>&laquo;<a href="#" class="element_figure_type" title="emotikon"><span class="tag">figure</span> <span class="attribute">type</span>="<span class="value">emotikon</span>"</a>&raquo; (<a href="#" title="wstawia znacznik z możliwością dodania tekstu" class="element_figure_open" val="emotikon">z tekstem</a>) &mdash; emotikonka,</li>
					<li>&laquo;<a href="#" class="element_figure_type" title="heart"><span class="tag">figure</span> <span class="attribute">type</span>="<span class="value">heart</span>"</a>&raquo; (<a href="#" title="wstawia znacznik z możliwością dodania tekstu" class="element_figure_open" val="heart">z tekstem</a>) &mdash; serce,</li>
                    <li>&laquo;<a href="#" class="element_figure_type" title="other"><span class="tag">figure</span> <span class="attribute">type</span>="<span class="value">other</span>"</a>&raquo; (<a href="#" title="wstawia znacznik z możliwością dodania tekstu" class="element_figure_open" val="other">z tekstem</a>) &mdash; inne symbole,</li>
				</ul>
			</li>
			<li>Atrybuty:
				<ul>
					<li><a href="#" class="element_figure_rend"><span class="attribute">rend</span>="<span class="value">multiple"</span></a></li>
				</ul>
			</li>
		</ul>
	</li>
	<li>&laquo;<span class="tag">gap</span>&raquo; &mdash; opuszczone, nieczytelne,
		<ul>
			<li>&laquo;<a href="#" class="element_gap_reason"><span class="tag">gap</span> <span class="attribute">reason</span>="<span class="value">hspace</span>"</a>&raquo; &mdash; pozioma przestrzeń,</li>
			<li>&laquo;<a href="#" class="element_gap_reason"><span class="tag">gap</span> <span class="attribute">reason</span>="<span class="value">illegible</span>"</a>&raquo; &mdash; nieczytelny,</li>
			<li>&laquo;<a href="#" class="element_gap_reason"><span class="tag">gap</span> <span class="attribute">reason</span>="<span class="value">missing</span>"</a>&raquo; &mdash; pominięty,</li>
			<li>&laquo;<a href="#" class="element_gap_reason"><span class="tag">gap</span> <span class="attribute">reason</span>="<span class="value">prosecutor</span>"</a>&raquo; &mdash; zamazane przez prokuraturę,</li>
			<li>&laquo;<a href="#" class="element_gap_reason"><span class="tag">gap</span> <span class="attribute">reason</span>="<span class="value">signature</span>"</a>&raquo; &mdash; podpis,</li>
		</ul>								
	</li>
	<li>&laquo;<span class="tag">hi</span>&raquo; &mdash; wyróżnienie,
		<ul>
			<li>&laquo;<a href="#" class="element_hi_rend"><span class="tag">hi</span> <span class="attribute">rend</span>="<span class="value">bold</span>"</a>&raquo; &mdash; wytłuszczonie poprzez nadpisanie,</li>
            <li>&laquo;<a href="#" class="element_hi_rend"><span class="tag">hi</span> <span class="attribute">rend</span>="<span class="value">larger</span>"</a>&raquo; &mdash; zwiększony rozmiar tekstu,</li>
			<li>&laquo;<a href="#" class="element_hi_rend"><span class="tag">hi</span> <span class="attribute">rend</span>="<span class="value">superscript</span>"</a>&raquo; &mdash; indeks górny,</li>
			<li>&laquo;<a href="#" class="element_hi_rend"><span class="tag">hi</span> <span class="attribute">rend</span>="<span class="value">subscript</span>"</a>&raquo; &mdash; indeks dolny,</li>
			<li>&laquo;<a href="#" class="element_hi_rend"><span class="tag">hi</span> <span class="attribute">rend</span>="<span class="value">underline</span>"</a>&raquo; &mdash; podkreślonie,</li>
			<li>&laquo;<a href="#" class="element_hi_rend"><span class="tag">hi</span> <span class="attribute">rend</span>="<span class="value">underline-multiple</span>"</a>&raquo; &mdash; wielokrotne podkreślonie,</li>
			<li>&laquo;<a href="#" class="element_hi_rend"><span class="tag">hi</span> <span class="attribute">rend</span>="<span class="value">uppercase</span>"</a>&raquo; &mdash; użycie drukowanych liter,</li>
		</ul>
	</li>
	<li>&laquo;<span class="tag">hyph</span>&raquo; &mdash; znak dzielenia wyrazu między liniami,
		<ul>
			<li>&laquo;<a href="#" class="element_hyph_empty"><span class="tag">hyph</span></a>&raquo; &mdash; bez symbolu przeniesienia,</li>
			<li>&laquo;<a href="#" class="element_hyph"><span class="tag">hyph</span> <span class="value">-</span></a>&raquo; &mdash; dywiz,</li>
			<li>&laquo;<a href="#" class="element_hyph"><span class="tag">hyph</span> <span class="value">=</span></a>&raquo; &mdash; „podwójny dywiz”,</li>
		</ul>
	</li>
	<li>&laquo;<a href="#" class="element_salute"><span class="tag">salute</span></a>&raquo; &mdash; powitanie, pożegnanie,</li>
	<li>&laquo;<a href="#" class="element_p_lb"><span class="tag">lb</span></a>&raquo; &mdash; łamanie linii,</li>				
	<li>&laquo;<span class="tag">unclear</span>&raquo; &mdash; częściowo nieczytelny tekst,
		<ul>
			<li>&laquo;<a href="#" class="element_unclear_cert" title="high"><span class="tag">unclear</span> <span class="attribute">cert</span>="<span class="value">high</span>"</a>&raquo; &mdash; duża pewność,</li>
			<li>&laquo;<a href="#" class="element_unclear_cert" title="normal"><span class="tag">unclear</span> <span class="attribute">cert</span>="<span class="value">normal</span>"</a>&raquo; &mdash; średnia pewność,</li>
			<li>&laquo;<a href="#" class="element_unclear_cert" title="low"><span class="tag">unclear</span> <span class="attribute">cert</span>="<span class="value">low</span>"</a>&raquo; &mdash; niska pewność,</li>
			<li>&laquo;<a href="#" class="element_unclear_cert" title="unknown"><span class="tag">unclear</span> <span class="attribute">cert</span>="<span class="value">unknown</span>"</a>&raquo; &mdash; nieznane,</li>
		</ul>				
	</li>				
    <li>&laquo;<a href="#" class="element_verte"><span class="tag">verte</span></a>&raquo; &mdash; następna strona,</li>
</ul>		
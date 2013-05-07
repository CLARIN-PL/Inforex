{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<td class="table_cell_content" style="font-family: Verdana; font-size: 12px">

<h1>Notatki</h1>

<ul>
	<li><b>Tryb szybkiego ustalania typu dokumentu.</b> Dla dokumentu, który nie ma określonego typu, wyświetlana jest lista (nie combo box, tylko np. lista linków) wszystkich typów dokumentów. Po wyboru jednej z pozycji następuje zmiana i przejście do następnego dokumentu.
	<li>Poszukać terminów <b>proper name categorization</b>, <b>named entity categorization</b>, <b>named entity hierarchy</b>.</li>
</ul>

<div class="ui-state-highlight">
<h2>Task 1</h2>
Oznaczyć następujące typy nazw własnych:
<ul>
	<li>miasta: <code>city</code>,</li>
	<li>państwa: <code>country</code>,</li>
	<li>firmy, zakłady itp.: <code>company</code>. Poprzez nazwę własną firmy rozumiem: pełną nazwę firmy, skróconą nazwę firmy, nazwę będącą akronimem, aliasy obowiązujące w ramach jednego dokumentu.</li>
</ul>

<h2>Struktura adnotacji</h2>
<ul>
	<li>firmy, zakłady, fundusze, spółki, itp.:
		<ul>
			<li>company</li>
			<li>company_alias &mdash; alias zastępczy identyfikujący firmę w obrębie dokumentu, będący pospolitym słowem np. Fundusz, Spółka, itd.</li> 
			<li>company_short &mdash; skrócona nazwa firmy nie będąca aliasem.</li>
		</ul>
	</li>
	<li><i>aliasy</i></li>
		<ul>
			<li>firmy</li>
			<li>dokumenty</li>
		</ul>
	</li>
</ul>

<h2>Zostawione na później:</h2>
<ul>
	<li><a href="http://localhost/gpw/index.php?page=report&id=162">http://localhost/gpw/index.php?page=report&id=162</a></li>
</ul>
</div>

<h1>Next <a href="http://localhost/gpw/index.php?page=report&subpage=annotator&id=399">http://localhost/gpw/index.php?page=report&subpage=annotator&id=399</a></h1>

<br style="clear: both"/>

<h4>Błędy w tekście i wynikające z nich błędy tagera:</h4>
<ul class="samples" style="font-size: 12px">
	<li><code>(...) Polfarma S.A..W kolejnym roku (...)</code> &mdash; brak spacji pomiędzy kropką kończącą zdanie, a początkiem kolejnego zdania.<br/>
	<b>Problem:</b> <code>S.A..W</code> jest wydzielone jako jeden token.</li>
	<li>Unicodowy znak  zamieniany jest przez tager na znak zapytania '?'.</li>
	<li><code>(...) oznaczony kodem PLPCLRT00029.</code> &mdash; kropka kończąca zdanie nie jest oddzielona od kodu.<br/>
	<b>Problem:</b> otrzymana tokenizacja <code>[oznaczony] [kodem] [PLPCLRT00029.]</code> zamiast <code>[oznaczony] [kodem] [PLPCLRT00029][.]</code>.
	<li><code>(...) o godz.10.00 w siedzibie (...)</code> &mdash; brak spacji między skrótem <i>godz.</i> a godziną</i> powoduje złączenie dwóch tokenów w jeden.<br/>
	<b>Problem:</b> otrzymana tokenizacja <code>[o] [godz.10.00] [w] [siedzibie]</code> zamiast <code>[o] [godz.][10.00] [w] [siedzibie]</code></li>
	<li><code>(...) Pekaes Multi-Spedytor Sp.z o.o.( Spółka zależna (...)</code><br/>
	<b>Problem:</b> otrzymana tokenizacja <code>[Pekaes] [Multi-Spedytor] [Sp.z o.o.(] [Spółka] [zależna]</code> zamiast <code>[Pekaes] [Multi-Spedytor] [Sp.z o.o.][(] [Spółka] [zależna]</code></li>
	</li>
	<li><code>(słownie:dwadzieścia trzy miliony sześdziesiąt dziewięć tysięcy dwadzieścia osiem złotych)</code><br/>
	<b>Problem:</b> otrzymano <code>[(][słownie:dwadzieścia] [trzy] [miliony] (...)</code>, spodziewano <code>[(][słownie][:][dwadzieścia] [trzy] [miliony] (...)</code>.
	<li><code>1,30 zł( słownie</code><br/>
	<b>Problem:</b> otrzymano <code>[1,30] [zł(] [słownie]</code>, oczekiwano <code>[1,30] [zł][(] [słownie]</code>.
	<li><code>Telekomunikacji Polskiej S.A.("TP S.A")</code><br/>
	<b>Problem:</b> otrzymano <code>[Telekomunikacji] [Polskiej] [S.A.("TP] [S.A]["][)]</code>, oczekiwano <code>[Telekomunikacji] [Polskiej] [S.A.][(]["][TP] [S.A]["][)]</code>
	<li>
		<b>Text: </b><b><code>/Postanowienia Rady Dyrektorów IVAX Corporation/</code></b><br/>
	    <i>Problem: </i> slashe jako nawiasy<br/>
	    <i>Wynik: </i> <code><span style="color:red">[/Postanowienia]</span> [Rady] [Dyrektorów] [IVAX] <span style="color:red">[Corporation/]</span></code>
	</li>
	<li>
		<b>Text: </b><b><code>pomiędzy BEST S.A.(kredytobiorca) </code></b><br/>
	    <i>Problem: </i> brak spacji<br/>
	    <i>Wynik: </i> <code>[pomiędzy] [BEST]  <span style="color:red">[S.A.(kredytobiorca]</span> [)]</code>
	</li>
	<li>
		<b>Text: </b><b><code>Podstawa prawna:§ 10 ust 1</code></b><br/>
	    <i>Problem: </i> brak spacji<br/>
	    <i>Wynik: </i> <code>[Podstawa]  <span style="color:red">[prawna:§]</span> [10] [ust] [1]</code>
	</li>
</ul>

<h5>Oznaczenia osób</h5>
<ul class="samples" class="samples" style="font-size: 12px">
	<li>
		<b>Text: </b><b><code>Pana Jerzego Koszarnego.Uchwała</code></b><br/>
	    <i>Problem: </i> brak spacji<br/>
	    <i>Wynik: </i> <code>[Pana] [Jerzego] <span style="color:red">[Koszarnego.Uchwała]</span></code>
	</li>
	<li>
		<b>Text: </b><b><code>1.WIESŁAW MAZUR</code></b><br/>
	    <i>Problem: </i> brak spacji; imię i nazwisko drukowanymi literami<br/>
	    <i>Wynik: </i> <code><span style="color:red">[1.WIESŁAW]</span> [MAZUR]</code>
	</li>
	<li>
		<b>Text: </b><b><code>c)Tomasz Czechowicz</code></b><br/>
	    <i>Problem: </i> brak spacji; wypunktowanie<br/>
	    <i>Wynik: </i> <code><span style="color:red">[c)Tomasz]</span> [Czechowicz]</code>
	</li>
	<li>
		<b>Text: </b><b><code>Fundusz Inwestycyjny im.Eugeniusz Kwiatkowskiego</code></b><br/>
	    <i>Problem: </i> brak spacji<br/>
	    <i>Wynik: </i> <code>[Fundusz] [Inwestycyjny] <span style="color:red">[im.Eugeniusz]</span> [Kwiatkowskiego]</code>
	</li>

</ul>

</td>

{include file="inc_footer.tpl"}
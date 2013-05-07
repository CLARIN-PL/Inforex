{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<h1>Automatyczne rozpoznawanie zdarzeń</h1>

<div style="padding: 0pt 0.7em; margin: 10px;" class="ui-state-highlight ui-corner-all"> 
	<p style="padding: 10px"><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>
	<strong>Info</strong> W obecnej wersji system potrafi dokonać analizy raportu informującego o zwołaniu walnego zgromadzenia akcjonariuszy</p>
</div>

<table>
	<tr>
		<td style="vertical-align: top">
			<b>Treść raportu:</b><br/>
			<textarea cols="80" rows="30" id="events-text">
Zarząd Spółki Notoria Serwis S.A. z siedzibą w (00-814) Warszawie przy ul. Miedzianej 3A/17 wpisanej do Rejestru Przedsiębiorców Krajowego Rejestru Sądowego prowadzonego przez Sąd Rejonowy dla m.st. Warszawy - XII Wydział Gospodarczy Krajowego Rejestru Sądowego, pod nr KRS 0000331515, działając na podstawie art. 399 § 1 Kodeksu spółek handlowych informuje o zwołaniu na dzień 10 maja 2011 r. na godz. 11.00 w Warszawie przy ul. Prostej 51, Zwyczajnego Walnego Zgromadzenia Notoria Serwis S.A.
			
Proponowany harmonogram obrad Zwyczajnego Walnego Zgromadzenia akcjonariuszy spółki Notoria Serwis S.A:
1. Otwarcie Zwyczajnego Walnego Zgromadzenia.
2. Wybór Przewodniczącego Zwyczajnego Walnego Zgromadzenia.
3. Wybór Komisji Skrutacyjnej.
4. Sprawdzenie listy obecności i stwierdzenie prawidłowości zwołania Walnego Zgromadzenia oraz jego zdolności do podejmowania uchwał.
5. Przyjęcie porządku obrad.
6. Przedstawienie przez Radę Nadzorczą sprawozdania z wyników oceny sprawozdania Zarządu z działalności Spółki, sprawozdania finansowego Spółki za rok obrotowy 2010 r. oraz wniosku Zarządu w sprawie podziału zysku za rok obrotowy 2010.
7. Rozpatrzenie i podjęcie uchwały w przedmiocie zatwierdzenia sprawozdania Zarządu z działalności Spółki za rok obrotowy 2010.
8. Rozpatrzenie i podjęcie uchwały w przedmiocie zatwierdzenia sprawozdania finansowego za rok obrotowy 2010.
9. Rozpatrzenie i podjęcie uchwały w przedmiocie zatwierdzenia sprawozdania Rady Nadzorczej z działalności w roku obrotowym 2010.
10. Podjęcie uchwały w sprawie podziału zysku wypracowanego przez Spółkę.
11. Podjęcie uchwał w przedmiocie udzielenia absolutorium członkom Rady Nadzorczej Spółki z wykonania przez nich obowiązków w roku obrotowym 2010.
12. Podjęcie uchwał w przedmiocie udzielenia absolutorium członkom Zarządu Spółki z wykonania przez nich obowiązków w roku obrotowym 2010.
13. Podjęcie uchwał w sprawie odwołania członka Rady Nadzorczej Spółki.
14. Podjęcie uchwał w sprawie powołania członka Rady Nadzorczej Spółki.
15. Zamknięcie obrad Zwyczajnego Walnego Zgromadzenia.
			
Dzień rejestracji uczestnictwa w Zwyczajnym Walnym Zgromadzeniu przypada na 16 dni przed datą Zgromadzenia, tj.: 24 kwietnia 2011 roku. 
			</textarea><br/>
			
			<input type="button" value="Analizuj" id="events-process"/>
		</td>
		<td style="vertical-align: top">
			<b>Treść dokumentu z anotacjami:</b><br/>
			<div id="events-html" class="annotations" style="border: 1px solid #555; padding: 5px; background: lightyellow; width: 600px;">
				<i>wprowadź tekst w polu po lewej i wciśnij przycisk <b>Analizuj</b></i>
			</div>
			
		</td>
		<td style="vertical-align: top">
			<b>Zestawienie anotacji:</b><br/>
			<div id="events-struct"></div>
		</td>
	<tr>
</table>
{include file="inc_footer.tpl"}
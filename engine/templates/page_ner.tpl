{include file="inc_header.tpl"}

<h1>Automatic proper names recognition &mdash; <emph>under development</emph></h1>

<div style="padding: 0pt 0.7em; margin: 10px;" class="ui-state-highlight ui-corner-all"> 
	<p style="padding: 10px"><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>
	CRF-based model for <b>recognition of 56 categories of proper names</b> in Polish texts.<br/>
	The description of the base model can be found in <a href="http://nlp.pwr.wroc.pl/en/publications/107/show/publication"><em>Rich Set of Features for Proper Name Recognition in Polish Texts</em></a>.
    <p style="padding: 10px"><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>
Usługa NER jest hostowana na serwerach <a href="http://www.man.poznan.pl">Poznańskie Centrum Superkomputerowo-Sieciowe</a> w ramach współpracy przy realizacji projektu <a href="http://www.synat.pl">SYNAT</a> finansowanego przez Narodowe Centrum Badań i Rozwoju (numer grantu SP/I/1/77065/10).
</div>

<table style="width: 100%">
	<tr>
		<td style="vertical-align: top; border: 1px solid #777; background: #eee; padding: 5px; width: 40%">
			<h3>Text to analyze:</h3>
			<textarea id="ner-text" style="width: 99%" rows="30">Polska i Niemcy uważają, że instytucje europejskie powinny odgrywać ważną rolę w ustalaniu budżetu Unii Europejskiej. Mówił o tym premier Donald Tusk po rozmowie z szefem węgierskiego rządu w Warszawie.</textarea><br/>
			<div id="samples">
				Sample texts: 
				<a href="#" title="Polska i Niemcy uważają, że instytucje europejskie powinny odgrywać ważną rolę w ustalaniu budżetu Unii Europejskiej. Mówił o tym premier Donald Tusk po rozmowie z szefem węgierskiego rządu w Warszawie.">informacja prasowa</a>, 
				<a href="#" title="Zarząd REDAN SA informuje, że w dniu 09 lutego 2005r. Nadzwyczajne Walne Zgromadzenie Akcjonariuszy, podjęło uchwałę o powołaniu dotychczasowego Wiceprezesa Zarządu Piotra Kulawińskiego na Prezesa Zarządu Spółki. Pan Piotr Kulawiński, lat 37, jest związany ze Spółką od 2001r, a funkcję Wiceprezesa Zarządu REDAN SA pełnił od 2003r.">powołanie prezesa</a>,
				<a href="#" title="Zarząd Narodowego Funduszu Inwestycyjnego Progress Spółka Akcyjna z siedzibą w Warszawie informuje, że od dnia 1 stycznia 2005 roku Spółka będzie przekazywała raporty bieżące i okresowe za pomocą Elektronicznego Systemu Przekazywania Informacji (ESPI). Operatorem Systemu jest Agnieszka Gojny.">przystąpienie do ESPI</a>.
			</div>			
			<input type="button" value="Recognize names &raquo;" id="ner-process" style="width: 90%; text-align: center;"/>
		</td>
		<td style="vertical-align: top; border: 1px solid #777; background: #eee; padding: 5px; width: 40%">
			<h3>Text after analysis:</h3>
			<div id="ner-html" class="annotations" style="background: white; border: 1px solid #999; padding: 5px; overflow: auto"></div>
			<small id="ner-duration"></small>
		</td>
		<td style="vertical-align: top; border: 1px solid #777; background: #eee; padding: 5px; width: 300px; ">
			<h3>List of recognized names:</h3>
			<div id="ner-annotations" class="annotations" style="background: white; border: 1px solid #999; padding: 5px; overflow: auto"></div>
		</td>
	</tr>
</table>

{include file="inc_footer.tpl"}
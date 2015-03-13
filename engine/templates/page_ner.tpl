{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<h1>Automatic recognition of proper names, temporal expressions and null verbs for Polish</h1>

<h2>Choose data model</h2>
<div style="padding: 0pt 0.7em; margin: 10px;" class="ui-state-highlight ui-corner-all"> 
    <table>
       {foreach from=$models item=m name=models}
        <tr>
            <td style="vertical-align: top">
                <input type="radio" name="wsdl" value="{$m.wsdl}|{$m.model}" {if $smarty.foreach.models.index == 0}checked="checked"{/if}></td>
            <td>
                <p style="margin-top:3px"><em>{$m.name}</em> <b>({$m.type})</b></p>
                {$m.description}
            </td>            
        </tr>
       {/foreach}
    </table>
</div>

<h2>Submit text</h2>
<table style="width: 100%">
	<tr>
		<td style="vertical-align: top; border: 1px solid #777; background: #eee; padding: 5px; width: 40%">
			<h3>Text to analyze:</h3>
			<textarea id="ner-text" style="width: 99%" rows="30">Polska i Niemcy uważają, że instytucje europejskie powinny odgrywać ważną rolę w ustalaniu budżetu Unii Europejskiej. Mówił o tym premier Donald Tusk po rozmowie z szefem węgierskiego rządu w Warszawie.</textarea><br/>
			<div style="float: right">
            <input type="button" value="Process &raquo;" id="ner-process" class="button"/>
            </div>
			<div id="samples">
				Sample texts: 
				<a href="#" title="Polska i Niemcy uważają, że instytucje europejskie powinny odgrywać ważną rolę w ustalaniu budżetu Unii Europejskiej. Mówił o tym premier Donald Tusk po rozmowie z szefem węgierskiego rządu w Warszawie.">informacja prasowa</a>, 
				<a href="#" title="Zarząd REDAN SA informuje, że w dniu 09 lutego 2005r. Nadzwyczajne Walne Zgromadzenie Akcjonariuszy, podjęło uchwałę o powołaniu dotychczasowego Wiceprezesa Zarządu Piotra Kulawińskiego na Prezesa Zarządu Spółki. Pan Piotr Kulawiński, lat 37, jest związany ze Spółką od 2001r, a funkcję Wiceprezesa Zarządu REDAN SA pełnił od 2003r.">powołanie prezesa</a>,
				<a href="#" title="Zarząd Narodowego Funduszu Inwestycyjnego Progress Spółka Akcyjna z siedzibą w Warszawie informuje, że od dnia 1 stycznia 2005 roku Spółka będzie przekazywała raporty bieżące i okresowe za pomocą Elektronicznego Systemu Przekazywania Informacji (ESPI). Operatorem Systemu jest Agnieszka Gojny.">przystąpienie do ESPI</a>.
			</div>			
			<b>Text size is limited to 10k characters.</b><br/>
		</td>
		<td style="vertical-align: top; border: 1px solid #777; background: #eee; padding: 5px; width: 40%">
			<h3>Text after analysis:</h3>
			<div id="ner-html" class="annotations" style="background: white; border: 1px solid #999; padding: 5px; overflow: auto"></div>
			<small id="ner-duration"></small>
		</td>
		<td style="vertical-align: top; border: 1px solid #777; background: #eee; padding: 5px; width: 300px; ">
			<h3>List of recognized annotations:</h3>
			<div id="ner-annotations" class="annotations" style="background: white; border: 1px solid #999; padding: 5px; overflow: auto"></div>
		</td>
	</tr>
</table>

{include file="inc_footer.tpl"}

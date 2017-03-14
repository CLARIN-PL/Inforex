{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}
<div style="margin: 5px">
<div class="panel panel-default panel-form">
    <div class="panel-heading">
        <div class="panel-title">
            <h4>New request to process with Liner2</h4>
        </div>
    </div>
	<div class="panel-body">
		<table style="width: 100%">
			<tr>
				<td style="width: 150px"><b>Annotation model:</b></td>
				<td style="padding: 5px" id="ner-model">
    					<select name="wsdl" id="wsdl" style="width: 400px">
							{foreach from=$models item=m name=models}
							<option value="{$m.wsdl}|{$m.model}" {if $smarty.foreach.models.index==0}selected="checked"{/if}>
								{$m.name} [{$m.type}] {if $m.description} &mdash; {$m.description}{/if}							
							</option>
							{/foreach}
    					</select>
				</td>
			</tr>

			<tr>
				<td>
					<b>Text to process:</b>
					<div id="samples">
						Sample texts: 
						<a href="#" title="Polska i Niemcy uważają, że instytucje europejskie powinny odgrywać ważną rolę w ustalaniu budżetu Unii Europejskiej. Mówił o tym premier Donald Tusk po rozmowie z szefem węgierskiego rządu w Warszawie.">informacja prasowa</a>, 
						<a href="#" title="Zarząd REDAN SA informuje, że w dniu 09 lutego 2005r. Nadzwyczajne Walne Zgromadzenie Akcjonariuszy, podjęło uchwałę o powołaniu dotychczasowego Wiceprezesa Zarządu Piotra Kulawińskiego na Prezesa Zarządu Spółki. Pan Piotr Kulawiński, lat 37, jest związany ze Spółką od 2001r, a funkcję Wiceprezesa Zarządu REDAN SA pełnił od 2003r.">powołanie
							prezesa</a>, 
						<a href="#" title="Zarząd Narodowego Funduszu Inwestycyjnego Progress Spółka Akcyjna z siedzibą w Warszawie informuje, że od dnia 1 stycznia 2005 roku Spółka będzie przekazywała raporty bieżące i okresowe za pomocą Elektronicznego Systemu Przekazywania Informacji (ESPI). Operatorem Systemu jest Agnieszka Gojny.">przystąpienie
							do ESPI</a>.
					</div>
				</td>
				<td style="padding: 5px">
					<textarea id="ner-text" style="height: 150px; width: 100%; padding: 5px; font-size: 16px; font-family: Slabo">Polska i Niemcy uważają, że instytucje europejskie powinny odgrywać ważną rolę w ustalaniu budżetu Unii Europejskiej. Mówił o tym premier Donald Tusk po rozmowie z szefem węgierskiego rządu w Warszawie.</textarea>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="button" value="Process &raquo;" id="ner-process" class="button" /></td>
			</tr>
		</table>		
	</div>
</div>

<div class="panel panel-info panel-results">
    <div class="panel-heading">
        <div class="panel-title">
            <h4>Results</h4>
        </div>
    </div>
    <div class="panel-body" style="overflow: auto; height: 400px">
    
    	<div class="panel_template" style="display: none">
        <div class="panel panel-default">
        <div class="panel-heading">
                <div class="panel-title">
                    <h4>xxx</h4>
                </div>
            </div>
				<div class="panel-body">
					<table style="width: 100%">
						<tr>
							<td style="vertical-align: top; padding: 5px; width: 400px"><b>List of
									recognized annotations:</b>
								<div class="ner-annotations annotations"
									style="border: 1px solid ccc; padding: 5px;"></div>
							</td>
							<td style="vertical-align: top; padding: 5px; ">
								<b class="model"></b>
								<div class="ner-html annotations"
									style="background: #ffffe0; border: 1px solid #ccc; padding: 10px; font-size: 16px; font-family: Slabo"></div>
								<small class="ner-duration"></small>
							</td>
						</tr>
					</table>
				</div>
			</div>
        </div>
	</div>
</div>
</div>

{include file="inc_footer.tpl"}

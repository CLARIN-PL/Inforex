{include file="inc_header.tpl"}

<td class="table_cell_content">

<input type="hidden" id="report_content" value="{$content|escape:"html"}"/>

<h1 style="color: red">Wersja bardzo alfa</h1>

<h1>Automatyczne rozpoznawanie jednostek identyfikujących &mdash; osoby</h1>

<div>
	Przykładowe dokumenty: 
	<a href="index.php?page=ner&amp;content=Zarząd REDAN SA informuje, że w dniu 09 lutego 2005r. Nadzwyczajne Walne Zgromadzenie Akcjonariuszy, podjęło uchwałę o powołaniu dotychczasowego Wiceprezesa Zarządu Piotra Kulawińskiego na Prezesa Zarządu Spółki. Pan Piotr Kulawiński, lat 37, jest związany ze Spółką od 2001r, a funkcję Wiceprezesa Zarządu REDAN SA pełnił od 2003r.">powołanie prezesa</a>,
	<a href="index.php?page=ner&amp;content=Zarząd Narodowego Funduszu Inwestycyjnego Progress Spółka Akcyjna z siedzibą w Warszawie informuje, że od dnia 1 stycznia 2005 roku Spółka będzie przekazywała raporty bieżące i okresowe za pomocą Elektronicznego Systemu Przekazywania Informacji (ESPI). Operatorem Systemu jest Agnieszka Gojny.">przystąpienie do ESPI</a>.
</div>

<form method="post">
	<textarea name="content" style="width: 99%; height: 120px;">{$content}</textarea>
	<input type="submit" value="Wyślij" name="process">
</form>
{if $content}
<br/>
<div class="ui-widget ui-widget-content ui-corner-all">
	<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Wynik przetwarzania:</div>
	<div style="padding: 5px;">{$result}</div>
</div>
<br/>

<div class="ui-widget ui-widget-content ui-corner-all">
	<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Wynik tagowania:</div>
	<div style="padding: 5px;" id="content" class="takipi" ></div>
</div>
{/if}

<br/>

</td>

{include file="inc_footer.tpl"}
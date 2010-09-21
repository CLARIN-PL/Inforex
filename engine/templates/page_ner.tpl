{include file="inc_header.tpl"}

<input type="hidden" id="report_content" value="{$content|escape:"html"}"/>

<h1 style="color: red">Wersja bardzo alfa</h1>

<h1>Automatyczne rozpoznawanie jednostek identyfikujących &mdash; osoby</h1>

<div>
	Przykładowe dokumenty: 
	<a href="index.php?page=ner&amp;content=Zarząd REDAN SA informuje, że w dniu 09 lutego 2005r. Nadzwyczajne Walne Zgromadzenie Akcjonariuszy, podjęło uchwałę o powołaniu dotychczasowego Wiceprezesa Zarządu Piotra Kulawińskiego na Prezesa Zarządu Spółki. Pan Piotr Kulawiński, lat 37, jest związany ze Spółką od 2001r, a funkcję Wiceprezesa Zarządu REDAN SA pełnił od 2003r.">powołanie prezesa</a>,
	<a href="index.php?page=ner&amp;content=Zarząd Narodowego Funduszu Inwestycyjnego Progress Spółka Akcyjna z siedzibą w Warszawie informuje, że od dnia 1 stycznia 2005 roku Spółka będzie przekazywała raporty bieżące i okresowe za pomocą Elektronicznego Systemu Przekazywania Informacji (ESPI). Operatorem Systemu jest Agnieszka Gojny.">przystąpienie do ESPI</a>.
</div>

<form method="post">
	<b>Zbiór uczący:</b><br/>
	<select name="model">
		{foreach from=$models item=m name=models}
		<option value="{$smarty.foreach.models.index}" {if $model==$smarty.foreach.models.index}selected="selected"{/if}>{$m.description}</option>
		{/foreach}
	</select><br/>
	<b>Tekst do analizy:</b><br/>
	<textarea name="content" style="width: 99%; height: 120px;">{$content_submitted}</textarea>
	<input type="submit" value="Wyślij" name="process">
</form>

{if $content}
	<br/>
	<pre>{$result}</pre>
	<div class="ui-widget ui-widget-content ui-corner-all">
		<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Wynik przetwarzania:</div>
		<div style="padding: 5px;">{$content_marked}</div>
	</div>
	<h4>Legenda</h4>
	<ul>
		<li><span class="person">nazwa osoby</span></li>
		<li><span class="company">nazwa firmy</span></li>
	</ul>
{/if}

{include file="inc_footer.tpl"}
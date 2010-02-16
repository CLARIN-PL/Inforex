{include file="inc_header.tpl"}

<td class="table_cell_content">

<input type="hidden" id="report_content" value="{$content|escape:"html"}"/>

<h1 style="color: red">Wersja bardzo alfa</h1>

<h1>Automatyczne rozpoznawanie jednostek identyfikujących &mdash; osoby</h1>

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
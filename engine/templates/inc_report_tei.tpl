{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{if $structure_corrupted}

<h1 style="color: red">Dokument ma niepoprawną strukturę. Brakuje podziału na akapity (p) i zdania (br).</h1>

<div class="ui-widget ui-widget-content ui-corner-all" style="margin: 5px">			
	<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Treść dokumentu</div>
	<div style="padding: 5px;">
		<code class="html" style="white-space: pre-wrap"><br/>{$row.content|escape:"html"}</code>
	</div>
</div>

{else}
<div class="ui-widget ui-widget-content ui-corner-all" style="margin: 5px">			
	<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">header.xml:</div>
	<div style="padding: 5px;">
		<code class="html" style="white-space: pre-wrap"><br/>{$tei_header|escape:"html"}</code>
	</div>
</div>

<div class="ui-widget ui-widget-content ui-corner-all" style="margin: 5px">			
	<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">text.xml:</div>
	<div style="padding: 5px;">
		<code class="html" style="white-space: pre-wrap"><br/>{$tei_text|escape:"html"}</code>
	</div>
</div>
{/if}
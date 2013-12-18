{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<div class="ui-widget ui-widget-content ui-corner-all" style="margin: 5px">			
	<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Raw document content:</div>
	<div style="padding: 5px;">
		<code class="html" style="white-space: pre-wrap"><br/>{$row.content|escape:"html"}</code>
	</div>
</div>
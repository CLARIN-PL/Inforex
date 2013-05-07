{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<div class="ui-state-highlight ui-corner-all ui-state-info" style="margin: 5px">
	<span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>
	Poniżej znajduje się wynik tagowania treści raportu przy użyciu <a href="http://plwordnet.pwr.wroc.pl/clarin/ws/takipi/" target="_blank">TaKIPI-WS</a>.
</div>

<table style="width: 100%">
<tr>
	<td style="vertical-align: top">
		<div class="ui-widget ui-widget-content ui-corner-all">			
			<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Treść raportu:</div>
			<div id="content" class="takipi" style="padding: 5px;">
			</div>
		</div>	
	</td>
	<td style="width: 300px; vertical-align: top">
		<div class="ui-widget ui-widget-content ui-corner-all">			
			<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Szczegóły tokenu:</div>
			<div id="token" style="padding: 5px;">
				<code></code>			
			</div>
		</div>		
	</td>
</tr>
</table>

<input type="hidden" id="report_content" value="{$row.content|escape}"/>
<input type="hidden" id="report_id" value="{$row.id}"/>
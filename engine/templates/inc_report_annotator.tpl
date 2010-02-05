<div id="dialog" title="Błąd" style="display: none;">
	<p>
		<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 50px 0;"></span>
		<span class="message">Your files have downloaded successfully into the My Downloads folder.</span>
	</p>
	<p><i><a href="">Odświerz stronę.</a></i></p>
</div>

<div class="ui-state-highlight ui-corner-all ui-state-error" id="block_message" style="display: none; margin: 2px 0">
	<p>
		<span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span>
		Możliwość wstawiania anotacji jest zablokowana &mdash; <b><span id="block_reason"></span></b>
	</p>
</div>

<div class="ui-widget ui-widget-content ui-corner-all" id="tag_buttons" style="margin: 5px; background: #F5C95D">
	<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Dodawanie anotacji:</div>
	<div class="ui-state-highlight ui-corner-all ui-state-info" id="block_message_info" style="margin: 2px 2px">
		<p>
			<span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span>
			Aby dodać adnotację, najpierw zaznacz tekst, a następnie kliknij w jeden z przycisków poniżej.
		</p>
	</div>
	<div style="margin: 5px;"
	{foreach from=$annotation_types item=type}
		<input type="button" value="{$type.name}" class="an"/>
	{/foreach}		
	<span id="add_annotation_status"></span>
	<input type="hidden" id="report_id" value="{$row.id}"/>
	</div>
</div>

<table style="width: 100%;">
	<tr>
		<td style="vertical-align: top"> 
			<div class="ui-widget ui-widget-content ui-corner-all">			
			<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Treść raportu:</div>
				<div id="content" style="padding: 5px;">
				{$row.content|format_annotations}
				</div>
			</div>
		</td>
		<td style="width: 300px; vertical-align: top; ">
			{include file="inc_widget_document_metadata.tpl"}
			<br/>
			<div class="ui-widget ui-widget-content ui-corner-all" style="background: PeachPuff">			
			<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Dane adnotacji:</div>
				<table style="font-size: 8pt">
					<tr>
						<th style="text-align: right">Przed:</th>
						<td id="annotation_redo_text">-</td>
					</tr>
					<tr>
						<th style="text-align: right">Po:</th>
						<td id="annotation_text">-</td>
					</tr>
					<tr>
						<th style="text-align: right">Mod:</th>
						<td><small>lewa</small> <span id="annotation_left">-</span>; <small>prawa</small> <span id="annotation_right">-</span></td>					
					</tr>
					<tr>
						<th style="text-align: right">Typ:</th>
						<td>{$select_annotation_types}<span id="annotation_redo_type"></span></td>
					</tr>
					<tr>
						<th></th>
						<td>
							<input type="button" value="zapisz" id="annotation_save" disabled="true"/>
							<input type="button" value="cofnij" id="annotation_redo" disabled="true"/>
						</td>
					</tr>
				</table>
			</div>
			<br/>
			{include file="inc_widget_annotation_list.tpl"}
		</td>
	</tr>
</table>
</div>

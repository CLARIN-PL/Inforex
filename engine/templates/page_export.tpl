{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<input id="newExportButton" type="button" class="button" value="Make new export"/>

<div id="newExportForm" style="display: none">
	<h2>Define new export</h2>
	<table class="tablesorter" style="width: 600px" cellspacing="1">
		<tr>
			<th style="width: 100px; vertical-align: top">Description</th>
			<td colspan="2">
				<textarea name="description" style="width: 98%; height: 50px"></textarea>
			</td>			
		</tr>
		<tr>
			<th style="vertical-align: top">Selectors<br/><small style="font-weight: normal">Definie citeria used to select document to export</small></th>
			<td class="flags"></td>
			<td style="width: 40px; text-align: center; vertical-align: bottom;">
				<div class="flag_template" style="display: none">
				<div style="border: 1px solid #7D7D09; background: #FFFFD8; padding: 5px; margin: 2px;">
					<i class="fa fa-times-circle-o close" aria-hidden="true"></i>
					<div class="flags" style="vertical-align: middle;">
							Flag <select name="corpus_flag_id"
								style="font-size: 12px">
								<option style="font-style: italic" value="">Select flag</option>
								{foreach from=$corpus_flags item=flag}
								<option value="{$flag.corpora_flag_id}"
									{if $flag.corpora_flag_id==$corpus_flag_id}selected=
									"selected"{/if} title="{$flag.name}"><em>{$flag.short}</em></option>
								{/foreach}
							</select> : {foreach from=$flags item=flag}
							<img class="flag" src="gfx/flag_{$flag.flag_id}.png"
								value="{$flag.flag_id}" /> {/foreach}
						</div>
				</div>
				</div>
				<input type="submit" value="+" class="button new_selector"/>
			</td>			
		</tr>
		<tr>
			<th style="vertical-align: top">Extractors<br/><small style="font-weight: normal">Definie what elements should be exported for documents on the basis of their flag values</small></th>
			<td class="extractors">
			</td>
			<td style="text-align: center; vertical-align: bottom;">
				<div class="extractor_template" style="display: none;">
					<div class="extractor" style="border: 1px solid #152C96; background: #C8D0F2; padding: 4px; margin: 2px;">
						<i class="fa fa-times-circle-o close" aria-hidden="true"></i>
						<b>For</b>
						<div style="border: 1px solid #7D7D09; background: #FFFFD8; padding: 5px; margin: 4px;">
							<div class="flags">
								Flag
								<select name="corpus_flag_id" style="font-size: 12px">
									<option style="font-style: italic" value="">Select flag</option>
									{foreach from=$corpus_flags item=flag}
									<option value="{$flag.corpora_flag_id}" {if $flag.corpora_flag_id==$corpus_flag_id}selected="selected"{/if} title="{$flag.name}"><em>{$flag.short}</em></option>
									{/foreach}
								</select>
								:
								{foreach from=$flags item=flag}
									<img class="flag" src="gfx/flag_{$flag.flag_id}.png" value="{$flag.flag_id}"/>
								{/foreach}
							</div>
						</div>
						<b>Export</b>
						<div class="elements" style="margin: 4px;">
							<table style="border: 1px solid #7D7D09; background: #FFFFD8; padding: 5px; width: 100%;">
								<tr>
									<td style="text-align: right; width: 120px;">Annotation layer(s)<br/> and subset(s):</td>
									<td>{include file="inc_widget_annotation_layers_and_subsets.tpl"}</td>
								</tr>
								{*
								<tr>
									<td style="text-align: right">Annotation relation(s):</td>
									<td><a href="#">select</a></td>
								</tr>
								*}																
							</table>
						</div>
					</div>
				</div>
				<input type="submit" value="+" class="button new_extractor"/>
			</td>
		</tr>
		{*
		<tr>
			<th style="vertical-align: top">Indices</th>
			<td><i>To do</i>
			</td>
			<td style="text-align: center; vertical-align: bottom;">
				<input type="submit" value="+" class="button"/>
			</td>
		</tr>
		*}		
		<tr>
			<td></td>
			<td colspan="2" class="buttons">
				<input type="submit" value="Export" class="button" id="export"/>
				<input type="submit" value="Return to the list of exports" class="button warning" id="cancel"/>
			</td>
		</tr>
	</table>
</div>

<div id="history">
    <h2>History of exports</h2>
    <table id="exportHistory" class="tablesorter" cellspacing="1">
        <thead>
            <tr>
            	<th>Id</th>
            	<th style="text-align: center">Status</th>
	            <th>Description</th>
	            <th style="width: 120px">Submitted</th>
	            <th style="width: 120px">Processing started</th>
	            <th style="width: 120px">Processing finished</th>
	            <th>Selectors</th>
	            <th>Extractors</th>
	            <th>Indices</th>
                <th style="text-align: center">Download</th>
	       </tr>
        </thead>
        <tbody>
        {foreach from=$exports item=export}
            <tr>
                <td>{$export.export_id}</td>
                <td style="text-align: center">{$export.status}</td>
                <td>{$export.description}</td>
                <td>{$export.datetime_submit}</td>
                <td>{$export.datetime_start}</td>
                <td>{$export.datetime_finish}</td>
                <td style="white-space: nowrap;">{$export.selectors|trim}</td>
                <td style="white-space: nowrap;">{$export.extractors}</td>
                <td style="white-space: nowrap;">{$export.indices}</td>
                <td style="text-align: center">{if $export.status == "done"}
                	<div class="ui-state-highlight ui-corner-all" style="padding: 1em .7em;">
                	<a href="index.php?page=export_download&amp;export_id={$export.export_id}">download</a>
                	</div>
                {else}
                <i>not ready</i>
				{/if}</td>
            </tr>
        {/foreach}        
        {if $exports|@count==0}
            <tr>
                <td colspan="10"><i>History of exports is empty</i></td>
            </tr>        
        {/if}
        </tbody>
    </table>
</div>

<br style="clear: both;"/>

{include file="inc_footer.tpl"}

		
		<strong>Hey!</strong> Sample ui-state-highlight style.</p>
	</div>
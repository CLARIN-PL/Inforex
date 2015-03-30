{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<table id="metadata" style="width: 100%">
    <tr>
        <td style="vertical-align: top; min-width: 400px; ">
            {assign var="action" value="metadata_save"}
            {assign var="button_text" value="Save"}
            {include file="inc_document_metadata_form.tpl"}
		</td>
		<td style="overflow: scroll; width: 500px; padding-left: 10px; vertical-align: top">
            <h2>Document content</h2>
		      <div style="border: 1px solid #aaa; padding: 5px; background: white;" class="{$report.format}">{$content}</div>
		</td>
	</tr>
</table>


<table id="metadata" style="width: 100%">
    <tr>
        <td style="vertical-align: top; min-width: 400px; ">
            {assign var="action" value="metadata_save"}
            {assign var="button_text" value="Save"}
            {include file="inc_document_metadata_form.tpl"}
		</td>
		<td style="overflow: scroll; width: 500px; padding-left: 10px; vertical-align: top">
            <h1>Document content</h1>
		      <div style="border: 1px solid #aaa; padding: 5px; background: white;">{$content}</div>
		</td>
	</tr>
</table>

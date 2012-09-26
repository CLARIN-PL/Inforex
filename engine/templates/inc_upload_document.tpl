{if $smarty.cookies.accordionActive=="cell_upload_header"}
<h3 id="cell_upload_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
	<span class="ui-icon ui-icon-triangle-1-s"></span>
	<a tabindex="-1" href="#">Upload document</a>
</h3>
<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
{else}
<h3 id="cell_upload_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
	<span class="ui-icon ui-icon-triangle-1-e"></span>
	<a tabindex="-1" href="#">Upload document</a>
</h3>
<div style="vertical-align: top; padding: 5px; display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
{/if}
<form enctype="multipart/form-data" method="POST">
	<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
    <table class="tablesorter" cellspacing="1">
        <tr>
            <th style="width: 100px">Pre-morph</th>
            <td><input type="file" name="pre_morph"/></td>
        </tr>
        <tr>
            <th style="width: 100px">Annotations file</th>
            <td><input type="file" name="annotations_file"/></td>
        </tr>
        <tr>
            <th style="width: 100px">Relations file</th>
            <td><input type="file" name="relations_file"/></td>
        </tr>
    </table>
    
    <input type="submit" value="Upload" style="margin: 10px; margin-left: 120px; padding: 5px 15px"/>
</form>
</div>
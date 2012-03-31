<form method="POST" enctype="multipart/form-data">
	<h1>Upload images</h1>
	<table class="tablesorter" cellspacing="1">
	    <tr>
	        <th style="width: 100px">Image</th>
	        <td><input type="file" id="name" name="image" size="50" maxlength="100000" accept="image/gif,image/jpeg,image/png" /></td>
	    </tr>
	</table>
	
	<input type="submit" value="Upload" style="margin: 5px; padding: 5px 15px"/>
	<input type="hidden" name="action" value="document_image_upload"/>
	<input id="report_id" type="hidden" name="report_id" value="{$row.id}">
</form>
{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class = "{if $flags_active}col-md-11{else}col-md-12{/if} scrollingWrapper">
	<form method="POST" enctype="multipart/form-data" style = "margin-bottom: 50px;">
		<div class = "panel panel-primary" style = "width: 50%;">
			<div class = "panel-heading">Upload image</div>
			<div class = "panel-body text-center scrolling">
					<div class = "form-group">
						<label for = "name" class = "btn btn-danger" id = "upload_label">Choose a file...</label>
						<input class = "inputfile" type="file" id="name" name="image" size="50" maxlength="100000" accept="image/gif,image/jpeg,image/png" />
					</div>
					<input type="hidden" name="action" value="document_image_upload"/>
					<input id="report_id" type="hidden" name="report_id" value="{$row.id}">
			</div>
			<div class = "panel-footer clearfix">
				<input type="submit" id = "upload_btn" class = "btn btn-primary" title = "Select a file" value="Upload" disabled style = "float: right;">
			</div>
		</div>
	</form>
</div>
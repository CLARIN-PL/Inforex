{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class = "{if $flags_active}col-md-11{else}col-md-12{/if} scrollingWrapper">
		<div class = "panel panel-primary">
			<div class = "panel-heading">Upload image</div>
			<div class = "panel-body text-center scrolling">
				{if empty($images)}
					<div class = "row text-center">
						<h3> This document has no images. </h3>
					</div>

				{/if}

				{foreach from = $images item = image_row}
					<div class = "row">
						{foreach from = $image_row item = image}
							<div class = "col-sm-4">
								<div class = "thumbnail">
									<img src="image.php?id={$image.id}_{$image.name}" class="img-rounded" alt="Report image">
									<button type="button" class="close delete_image" name = "{$image.name}" id = "{$image.id}" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
							</div>
						{/foreach}
					</div>
                {/foreach}
			</div>
			<div class = "panel-footer clearfix">
				<button class = "btn btn-primary" style = "float: right;" data-toggle="modal" data-target="#upload_image_modal">Upload image</button>
			</div>
		</div>
</div>

<div class="modal fade" id="upload_image_modal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="POST" enctype="multipart/form-data">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Upload image</h4>
				</div>
				<div class="modal-body text-center">
					<div class = "form-group">
						<label for = "name" class = "btn btn-danger" id = "upload_label">Choose a file...</label>
						<input class = "inputfile" type="file" id="name" name="image" size="50" maxlength="100000" accept="image/gif,image/jpeg,image/png" />
					</div>
					<input type="hidden" name="action" value="document_image_upload"/>
					<input id="report_id" type="hidden" name="report_id" value="{$row.id}">
				</div>
				<div class ="modal-footer clearfix">
					<input type="submit" id = "upload_btn" class = "btn btn-primary" title = "Select a file" value="Upload" disabled style = "float: right;">
				</div>
			</form>
		</div>
	</div>
</div>
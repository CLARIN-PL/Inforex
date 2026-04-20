{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<div id="frame_editor">
	<form method="post" action="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row.id}">
		<div class="panel panel-primary administration-content-panel report-transcription-editor-panel">
			<div class="panel-heading administration-content-heading report-viewer-main-heading">
				<span class="administration-content-heading-icon report-viewer-heading-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
				<span>Document content</span>
			</div>
			<div class="panel-body report-viewer-content-body report-transcription-editor-body">
				<div class="inner_border scrolling report-transcription-editor-scroll">
					<textarea id="report_content" name="content">{$row.content|escape}</textarea>
				</div>
			</div>
			<div class="panel-footer report-transcription-editor-footer">
				<div class="report-transcription-editor-actions height_fix">
					<input class="btn btn-warning report-transcription-validate-button" type="button" value="Validate structure" id="validate"/>
					<input type="submit" class="submit btn btn-primary report-transcription-save-button" name="name" value="Save" id="save" disabled="disabled"/>
					<!--
					<input type="button" value="Waliduj"/>
					<div style="border: 1px solid red; display: inline; width: 10px">&nbsp;!!&nbsp;</div>
					-->
				</div>

				<input type="hidden" value="{$row.id}" name="report_id" id="report_id"/>
				<input type="hidden" value="document_content_update" name="action"/>
			</div>
		</div>
	</form>
</div>

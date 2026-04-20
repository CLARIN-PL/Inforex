{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}


<div id="col-agreement" class="col-main {if $flags_active}col-md-11{else}col-md-12{/if} scrollingWrapper report-viewer-content-column">
	<div class="panel panel-primary administration-content-panel report-viewer-content-panel">
		<div class="panel-heading administration-content-heading report-viewer-main-heading">
			<span class="administration-content-heading-icon report-viewer-heading-icon"><i class="fa fa-eye" aria-hidden="true"></i></span>
			<span>Content Viewer</span>
		</div>
		<div class="panel-body report-viewer-content-body">
			{if $exceptions|@count > 0}
				<div class="infobox-light report-viewer-error">The document could not be displayed due to structure errors.</div>
			{else}
				<div class="row scrollingAccordion report-viewer-grid">
					<div class="col-md-6 report-viewer-pane-column">
						<div class="panel panel-default report-viewer-pane">
							<div class="panel-heading report-viewer-pane-heading">
								<span class="report-viewer-pane-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
								<span>Formatted preview</span>
							</div>
							<div class="panel-body report-viewer-pane-body">
								<div id="leftContent" class="annotations scrolling content report-viewer-document-content report-viewer-scrollable">
									<div class="contentBox report-viewer-content-box">{$content_html|format_annotations}</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6 report-viewer-pane-column">
						<div class="panel panel-default report-viewer-pane">
							<div class="panel-heading report-viewer-pane-heading">
								<span class="report-viewer-pane-icon"><i class="fa fa-code" aria-hidden="true"></i></span>
								<span>Raw preview</span>
							</div>
							<div class="panel-body report-viewer-pane-body">
								<div id="rightContent" class="annotations content rightPanel scrolling report-viewer-scrollable">
									<textarea name="content" id="report_content" class="report-viewer-source">{$content_source|escape}</textarea>
								</div>
							</div>
						</div>
					</div>
				</div>
			{/if}
		</div>
	</div>
</div>

{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}


<div id="col-agreement" class="col-main {if $flags_active}col-md-11{else}col-md-12{/if} scrollingWrapper">
	<div class="panel panel-primary">
		<div class="panel-heading">Document content</div>
		<div class="panel-body" style="">
			{if $exceptions|@count > 0}
				<div class="infobox-light">The document could not be displayed due to structure errors.</div>
			{else}
				<div class="row scrollingAccordion">
					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading">Formated preview</div>
							<div class="panel-body" style="padding: 0">
								<div id="leftContent" class="annotations scrolling content">
									<div style="margin: 5px" class="contentBox">{$content_html|format_annotations}</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading">Raw preview</div>
							<div class="panel-body" style="padding: 0">
								<div id="rightContent" class="annotations content rightPanel scrolling">
									<textarea name="content" id="report_content">{$content_source|escape}</textarea>
								</div>
							</div>
						</div>
					</div>
				</div>
			{/if}
		</div>
	</div>
</div>
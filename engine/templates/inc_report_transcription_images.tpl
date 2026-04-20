{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div id="frame_images">
	<div class="panel panel-info administration-content-panel report-transcription-images-panel">
		<div class="panel-heading administration-content-heading report-viewer-main-heading">
			<span class="administration-content-heading-icon report-viewer-heading-icon"><i class="fa fa-picture-o" aria-hidden="true"></i></span>
			<span>Images</span>
		</div>
		<div class="panel-footer report-transcription-images-toolbar">
			<div class="pagging report-transcription-images-toolbar-inner height_fix">
				<div class="report-transcription-zoom-group">
					<label for="slider-vertical">Zoom</label>
					<div id="slider-vertical" class="report-transcription-zoom-slider"></div>
					<div class="report-transcription-zoom-amount"><span id="zoom_amount">100 %</span></div>
				</div>

				<div class="report-transcription-scans-group">
					<span class="report-transcription-scans-label">Scans</span>
				{foreach from=$images item=image name=scan}
					<a href="#" class="report-transcription-scan-link{if $smarty.foreach.scan.index==0} active{/if}" title="scan{$smarty.foreach.scan.index}">{$smarty.foreach.scan.index+1}</a>
				{/foreach}
				</div>
			</div>
		</div>

		<div id="zoom" class="scans panel-body scrolling report-transcription-images-body">
		{foreach from=$images item=image name=scani}
			<div>
				<img id="scan{$smarty.foreach.scani.index}" style="width: 100%; {if $smarty.foreach.scani.index>0}display: none;{/if}" src="image.php?id={$image.image_id}" title="{$image.original_name}"/>
			</div>
		{/foreach}
		</div>
	</div>
</div>

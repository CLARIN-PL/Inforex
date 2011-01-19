<div style="position: absolute; top: 5px; right: 10px">
	<a href="#" id="transcriber_horizontal" title="Set horizontal layout"><img src="gfx/orientation_vertical.png"/></a>
	<a href="#" id="transcriber_vertical" title="Set vertical layout"><img src="gfx/orientation_horizontal.png"/></a>	
</div>

{if $orientation == "vertical"}
	<div id="transcriber" class="vertical">
{else}
	<div id="transcriber" class="horizontal">
{/if}
		{include file="inc_report_transcription_images.tpl"}
		{include file="inc_report_transcription_elements.tpl"}
		{include file="inc_report_transcription_editor.tpl"}
	</div>
<div style="clear: both"></div>
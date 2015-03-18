{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<div style="position: absolute; top: 5px; right: 10px">
	<a href="#" id="transcriber_horizontal" title="Set horizontal layout"><img src="gfx/orientation_vertical.png"/></a>
	<a href="#" id="transcriber_vertical" title="Set vertical layout"><img src="gfx/orientation_horizontal.png"/></a>	
    <a href="#" id="transcriber_noimages" title="Set layout without images"><img src="gfx/orientation_horizontal.png"/></a>   
</div>

{if $orientation == "vertical"}
	<div id="transcriber" class="vertical">
{elseif $orientation == "noimages"}
    <div id="transcriber" class="noimages">
{else}
	<div id="transcriber" class="horizontal">
{/if}
		{include file="inc_report_transcription_images.tpl"}
		{include file="inc_report_transcription_elements.tpl"}
		{include file="inc_report_transcription_editor.tpl"}
	</div>
<div style="clear: both"></div>
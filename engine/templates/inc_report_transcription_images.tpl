{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<div id="frame_images">
	<div class="pagging" class="height_fix">
		<div id="slider-vertical" style="height: 10px; width: 200px; float: right; margin: 2px 15px 0 15px"></div>
		<div style="float: right"><b>Przybliżenie:</b> <span style="color: rgb(246, 147, 31); font-weight: bold;" id="zoom_amount">100 %</span></div>
		
		<b>Skany: </b>	
		{foreach from=$images item=image name=scan}
			<a href="#" {if $smarty.foreach.scan.index==0} class="active"{/if} title="scan{$smarty.foreach.scan.index}">{$smarty.foreach.scan.index+1}</a>
		{/foreach}
	</div>
					
	<div id="zoom" class="scans" style="overflow: auto">
	{foreach from=$images item=image name=scani}
		<div>
			<img id="scan{$smarty.foreach.scani.index}" style="width: 100%; {if $smarty.foreach.scani.index>0}display: none;{/if}" src="image.php?id={$image.image_id}" title="{$image.original_name}"/>
		</div>
	{/foreach}
	</div>
</div>

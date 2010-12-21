<div style="position: absolute; top: 5px; right: 10px">
	<a href="index.php?page=report&amp;id={$row.id}&amp;orientation=horizontal" title="Set horizontal layout"><img src="gfx/orientation_vertical.png"/></a>
	<a href="index.php?page=report&amp;id={$row.id}&amp;orientation=vertical" title="Set vertical layout"><img src="gfx/orientation_horizontal.png"/></a>	
</div>

{if $orientation == "vertical"}
	<table style="width: 100%" class="vertical" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td style="width: 50%; vertical-align: top">
			{include file="inc_report_transcription_images.tpl"}
			</td>
			<td style="vertical-align: top">
			{include file="inc_report_transcription_editor.tpl"}
			</td>
		</tr>
	</table>
{else}
	<div class="horizontal">
		{include file="inc_report_transcription_images.tpl"}
		<div class="hsplitbar"></div>
		<table cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td style="vertical-align: top; width: 1000px">{include file="inc_report_transcription_editor.tpl"}</td>
				<td style="vertical-align: top; padding-left: 10px">{include file="inc_report_transcription_elements.tpl"}</td>
			</tr>
		</table>
	</div>
{/if}
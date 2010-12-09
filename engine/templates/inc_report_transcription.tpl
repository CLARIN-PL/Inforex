<table style="width: 100%">
	<tr>
		<td style="width: 50%; vertical-align: top">
			<div class="pagging">
				<b>Scans: </b>
				{foreach from=$images item=image name=scan}
					<a href="#" {if $smarty.foreach.scan.index==0} class="active"{/if} title="scan{$smarty.foreach.scan.index}">{$smarty.foreach.scan.index+1}</a>
				{/foreach}
			</div>
							
			<div id="zoom" class="scans">
			{foreach from=$images item=image name=scani}
				<div class="viewer iviewer_cursor">
					<img id="scan{$smarty.foreach.scani.index}" style="width: 100%; {if $smarty.foreach.scani.index>0}display: none;{/if}" src="image.php?id={$image.image_id}"/>
				</div>
			{/foreach}
			</div>
		</td>
		<td style="vertical-align: top">
			<form method="post" action="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row.id}">
				<div><b>Document transcription</b></div>
				<div>
					<textarea id="report_content" name="content">{$row.content|escape}</textarea>
				</div>
				<div style="padding: 5px">
					<input type="submit" class="submit button" name="name" value="Save" id="save" />
				</div>
	
				<input type="hidden" value="{$row.id}" name="report_id" id="report_id"/>
				<input type="hidden" value="document_content_update" name="action"/>
			</form>			
		</td>
	</tr>
</table>

{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<div id="frame_editor">
	<div class="height_fix">
		<b>Treść dokumentu:</b>
	</div>
	
	<form method="post" action="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row.id}">
	
	<div class="inner_border">		
		<textarea id="report_content" name="content">{$row.content|escape}</textarea>
	</div>

		<div style="padding: 5px" class="height_fix">
			<input type="submit" class="submit button" name="name" value="Save" id="save" disabled="disabled"/>
			<!--
			<input type="button" value="Waliduj"/>
			<div style="border: 1px solid red; display: inline; width: 10px">&nbsp;!!&nbsp;</div>
			-->
		</div>
	
		<input type="hidden" value="{$row.id}" name="report_id" id="report_id"/>
		<input type="hidden" value="document_content_update" name="action"/>
	</form>

	<div>
		<form method="post">
		</form>
	</div>

</div>	
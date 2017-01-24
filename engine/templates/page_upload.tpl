{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

{include file="inc_system_messages.tpl"}

<form method="post" action="index.php?corpus={$corpus.id}&page=upload"  enctype="multipart/form-data">
	Plik zip z plikami tekstowymi: 
	<input type="file" name="files"/>
	<input type="hidden" name="action" value="upload"/>
	<input class="button" type="submit" name="upload" value="Upload"/> 
</form>

{include file="inc_footer.tpl"}

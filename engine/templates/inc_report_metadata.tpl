{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div id="col-content" class="col-main {if $flags_active}col-md-11{else}col-md-12{/if}">
	{assign var="action" value="metadata_save"}
	{assign var="button_text" value="Save"}
    {assign var="header" value="Edit metadata"}
	{include file="inc_document_metadata_form.tpl"}
</div>


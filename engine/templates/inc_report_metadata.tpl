{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div id="col-content" class="col-md-7 scrollingWrapper">
	{assign var="action" value="metadata_save"}
	{assign var="button_text" value="Save"}
	{include file="inc_document_metadata_form.tpl"}
</div>

<div id="col-config" class="col-main {if $flags_active}col-md-4{else}col-md-5{/if} scrollingWrapper">
	<div class="panel panel-default">
		<div class="panel-heading">Document content</div>
		<div class="panel-body" style="padding: 5px">
			<div class="{$report.format}">{$content}</div>
		</div>
	</div>
</div>

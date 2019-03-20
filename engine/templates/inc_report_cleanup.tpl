{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div id="col-content" class="col-main {if $flags_active}col-md-4{else}col-md-5{/if} scrollingWrapper">
	<div class="panel panel-primary">
		<div class="panel-heading">Edit content</div>
		<div class="panel-body" style="padding: 0">
			{include file="inc_report_wrong_changes.tpl"}
			<form method="post" action="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row.id}">
				<div id="edit_content">
					<textarea name="content" class="scrolling" id="report_content">{if $wrong_changes}{$wrong_document_content|escape}{else}{$content_edit|escape}{/if}</textarea>
				</div>

				<input type="hidden" value="{$row.id}" name="report_id" id="report_id"/>
				<input type="hidden" value="document_update_content" name="action"/>
				<div class="panel-footer">
                    {if $ex}
						<div class="alert alert-danger">
							The document cannot be modified as an exception raised<br/><b>{$ex->getMessage()}</b>.
						</div>
                    {elseif $annotations_count>0}
						<div class="alert alert-danger">
							This document cannot be edited in this perspective because it contains annotations. Use <a href="index.php?page=report&subpage=edit&id={$report_id}">Content</a> instead.
						</div>
					{else}
						<input type="submit" class="btn btn-primary" value="Save" name="formatowanie" id="formating"/>
                    {/if}
				</div>
			</form>
		</div>
	</div>
</div>

<div id="col-source" class="col-md-7 scrollingWrapper">
	<div class="panel panel-info">
		<div class="panel-heading">Source</div>
		<div class="panel-body" style="padding: 0">
			<iframe src="{$row.source}" style="width: 100%" class="scrolling"></iframe>
		</div>
		<div class="panel-footer">
			Link: <a href="{$row.source}" target="_blank">{$row.source}</a>
		</div>
	</div>
</div>


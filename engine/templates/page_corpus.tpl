{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

{if "admin"|has_role || "manager"|has_corpus_role_or_owner}
	{include file="inc_system_messages.tpl"}
	<div id="corpusId" style="display:none">{$corpus.id}</div>
	<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all" style="margin: 2px 10px; position: relative; background: white; border: 1px solid #667a55">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			{foreach from=$subpages key=perspectiv item=perspectiv_name}
				<li class="ui-state-default ui-corner-top {if $subpage==$perspectiv}ui-state-active ui-tabs-selected{/if}">
					<a href="index.php?page=corpus&amp;corpus={$corpus.id}&amp;subpage={$perspectiv}">{$perspectiv_name}</a>
				</li>
			{/foreach}
			{if isCorpusOwner() || "admin"|has_role}
				<li class="ui-state-default ui-corner-top {if $subpage==corpus_delete}ui-state-active ui-tabs-selected{/if}">
					<a href="index.php?page=corpus&amp;corpus={$corpus.id}&amp;subpage=corpus_delete">Delete corpora</a>
				</li>
			{/if}
		</ul>
	
		<div style="margin: 4px">
			{include file="$subpage_file"}
		</div>
	
	</div>
{/if}

{include file="inc_footer.tpl"}
{include file="inc_header.tpl"}

<td class="table_cell_content">

	{if "admin"|has_role || "manager"|has_corpus_role_or_owner}
		{include file="inc_system_messages.tpl"}
		<div id="corpusId" style="display:none">{$corpus.id}</div>
		<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all" style="background: #f3f3f3; margin-bottom: 5px; position: relative">
			<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
				{foreach from=$subpages key=perspectiv item=perspectiv_name}
					<li class="ui-state-default ui-corner-top {if $subpage==$perspectiv}ui-state-active ui-tabs-selected{/if}">
						<a href="index.php?page=corpus&amp;corpus={$corpus.id}&amp;subpage={$perspectiv}">{$perspectiv_name}</a>
					</li>
				{/foreach}
				{if $corpus.ext}
					<li class="ui-state-default ui-corner-top {if $subpage==corpus_metadata}ui-state-active ui-tabs-selected{/if}">
						<a href="index.php?page=corpus&amp;corpus={$corpus.id}&amp;subpage=corpus_metadata">Metadata</a>
					</li>
				{/if}
				{if isCorpusOwner() || "admin"|has_role}
					<li class="ui-state-default ui-corner-top {if $subpage==corpus_delete}ui-state-active ui-tabs-selected{/if}">
						<a href="index.php?page=corpus&amp;corpus={$corpus.id}&amp;subpage=corpus_delete">Delete corpora</a>
					</li>
				{/if}
			</ul>
		
			<div>
				{include file="$subpage_file"}
			</div>
		
		</div>
	{/if}
</td>

{include file="inc_footer.tpl"}
{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl" content_class="corpus"}

{if "admin"|has_role || "manager"|has_corpus_role_or_owner}
	{include file="inc_system_messages.tpl"}
	<div id="corpusId" style="display:none">{$corpus.id}</div>
    <nav class="navbar navbar-report">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                {foreach from=$subpages key=perspectiv item=perspectiv_name}
                    <li class="{if $subpage==$perspectiv}active{/if}">
                        <a href="index.php?page=corpus_settings&amp;corpus={$corpus.id}&amp;subpage={$perspectiv}">{$perspectiv_name}</a>
                    </li>
                {/foreach}
                {if isCorpusOwner() || "admin"|has_role}
                    <li class="{if $subpage=="corpus_delete"}active{/if}">
                        <a href="index.php?page=corpus_settings&amp;corpus={$corpus.id}&amp;subpage=corpus_delete">Delete corpus</a>
                    </li>
                {/if}
		    </ul>
        </div>
    </nav>
	
    <div style="margin: 4px">
        {include file="$subpage_file"}
    </div>
	
	</div>
{/if}


<div class="modal fade settingsModal" id="deleteModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Are you sure you want to delete this?</h4>
            </div>
            <div class="modal-body" id = "deleteContent">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger confirmDelete" data-dismiss="modal">Delete</button>
            </div>
        </div>
    </div>
</div>


{include file="inc_footer.tpl"}

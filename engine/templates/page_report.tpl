{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}

<div id="main-content">
    <nav class="navbar navbar-report report-subpage-nav">
        <div class="container-fluid">
            <ul class="nav navbar-nav report-subpage-list">
                {foreach from=$subpages item=s}
                    {assign var=tab_title value=$s->title}
                    {if $s->id=="morphodisambagreement"}
                        {assign var=tab_title value="Morph. Agreement"}
                    {elseif $s->id=="diffs"}
                        {assign var=tab_title value="History"}
                    {/if}
                    <li class="{if $subpage==$s->id}active{/if}">
                        <a href="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage={$s->id}&amp;id={$row.id}" title="{$s->title|escape}">{$tab_title}</a></li>
                {/foreach}
            </ul>
            <ul class="nav navbar-nav navbar-right report-subpage-actions">
                <li>
                    <a href="#" id="toogleConfig" title="show/hide document view configuration"><i class="fa fa-cog fa-4" aria-hidden="true"></i></a>
                </li>
                <li>
                    <a href="#" id="toogleFlags" title="show/hide document flags and actions"><i class="fa fa-flag fa-4" aria-hidden="true"></i></a>
                </li>
            </ul>
        </div>
    </nav>
{if $corpus.public || $user}
    {if $invalid_report_id}
        <div style="background: #E03D19; padding: 1px; margin: 10px; ">
            <div style="background: #FFF194; padding: 5px; color: #733B0E; font-size: 16px; font-weight: bold;">
               <img src="gfx/image-missing.png" title="No access" style="vertical-align: middle"/> Document does not exist. Go back to <a href="index.php?page=browse&amp;corpus={$corpus.id}">list of documents</a>
           </div>
        </div>
	{elseif $page_permission_denied}
        <div style="background: #E03D19; padding: 1px; margin: 10px; ">
            <div style="background: #FFF194; padding: 5px; color: #733B0E; font-size: 16px; font-weight: bold;">
               <img src="gfx/lock.png" title="No access" style="vertical-align: middle"/> {$page_permission_denied}
           </div>
        </div>
    {elseif $subpage_file == "inc_report_noaccess.tpl"}
    <div class="container-fluid scrollingWrapper">
        <div class="row row-report scrolling">
            <div class="col-lg-4"></div>
            <div class="col-lg-4">
            {include file="$subpage_file"}
            </div>
            <div class="col-lg-4"></div>
        </div>
    </div>
    {else}
        <div class="container-fluid">
            {include file="inc_system_messages.tpl"}
            <div class="row row-report">
                {include file="$subpage_file"}
                <div id="flagStates" style="display:none; width: 200px">
                    <div>
                        <b>New state:</b>
                        <ul id="list_of_flags">
                            {foreach from=$flags item=flag}
                                <li>
                                  <span class="flagState" flag_id="{$flag.id}" title="{$flag.name}" style="cursor:pointer">
                                    <img src="gfx/flag_{$flag.id}.png"/> {$flag.name}
                                  </span>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                </div>
                <div id="col-flags" class="col-md-1" {if !$flags_active}style="display: none"{/if}>
                    <div class="scrollingWrapper panel-group" id="accordionFlags">
                            <div class="panel panel-info report-flags-side-panel">
                                <div class="panel-heading report-flags-side-heading" id="headingAvailable">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordionFlags" href="#flagList">
                                            <i class="fa fa-flag" aria-hidden="true"></i> Flags
                                        </a>
                                    </h4>
                                </div>
                                <div id="flagList" class="panel-collapse collapse in">
                                    <div class="scrollingAccordion">
                                        <div class="scrolling report-flags-list">
                                        {if $corporaflags|@count==0}
                                            <div class="report-flags-empty"><i class="fa fa-flag-o" aria-hidden="true"></i> no flags</div>
                                        {else}
                                            {foreach from=$corporaflags item=corporaflag}
                                                <span
                                                    class="corporaFlag report-flag-item {if $corporaflag.flag_id}report-flag-state-set{else}report-flag-state-empty{/if}"
                                                    cflag_id="{$corporaflag.id}"
                                                    report_id="{$row.id}"
                                                    title="{$corporaflag.name}: {if $corporaflag.flag_id}{$corporaflag.fname}{else}NIE GOTOWY{/if}">
                                                       <span class="report-flag-icon"><img src="gfx/flag_{if $corporaflag.flag_id}{$corporaflag.flag_id}{else}-1{/if}.png"/></span>
                                                       <span class="report-flag-short">{$corporaflag.short}</span>
                                                </span>
                                            {/foreach}
                                        {/if}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {if "delete_documents"|has_corpus_role_or_owner}
                            <div class="panel panel-info report-flags-side-panel">
                                <div class="panel-heading report-flags-side-heading" id="headingAvailable">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordionFlags" href="#actionList">
                                            <i class="fa fa-bolt" aria-hidden="true"></i> Actions
                                        </a>
                                    </h4>
                                </div>
                                <div id="actionList" class="panel-collapse collapse">
                                    <div class="scrollingAccordion" style="text-align: center; padding: 5px;">
                                        <span style="padding: 0px 2px 0px 2px; cursor:pointer" title="Delete document" corpus={$corpus.id}>
                                            <button type="button" class="delete_document_button btn btn-sm btn-danger report-action-delete-button" title="Delete document"
                                                    data-toggle="modal" data-target="#deleteDocument" report_id="{$row.id}" corpus_id="{$corpus.id}">
                                                <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade settingsModal" id="deleteDocument" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" id="deleteDocumentHeader">Are you sure you want to <b>delete this document</b>?</h4>
                    </div>
                    <div class="modal-body" id="deleteContent">
                        <div class = "delete_info">
                            <label for="deleteDocumentTitle">Title:</label>
                            <p id="deleteDocumentTitle"></p>
                        </div>
                        <div class = "delete_loader text-center" style = "display: none;">
                            <div class = "loader"></div>
                            <h3 style = "margin-top: 30px; margin-bottom: 30px;">Deleting document...</h3>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger confirmDeleteDocument">Delete</button>
                    </div>
                </div>
            </div>
        </div>
	{/if}
{else}
    {include file="inc_no_access.tpl"}
{/if}
</div>

{include file="inc_footer.tpl"}

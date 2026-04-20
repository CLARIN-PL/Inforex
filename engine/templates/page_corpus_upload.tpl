{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

{include file="inc_header2.tpl"}

<div class="corpus-upload-page">
    {if $action_error}
        <div class="alert alert-danger corpus-upload-alert">
            <strong>Error!</strong> {$action_error}
        </div>
    {/if}

    {if $warnings}
        <div class="alert alert-warning corpus-upload-alert">
            <strong>Warning!</strong>
            {if $warnings|@count == 1}
                {$warnings[0]}
            {else}
                <ul>
                    {foreach from=$warnings item=warning}
                        <li>{$warning}</li>
                    {/foreach}
                </ul>
            {/if}
        </div>
    {/if}

    {if $action_performed}
        <div class="alert alert-success corpus-upload-alert">
            <strong>Success!</strong> {$action_performed}
        </div>
    {/if}

    <div class="panel administration-content-panel corpus-upload-panel">
        <div class="panel-heading administration-content-heading">
            <span class="administration-content-heading-icon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
            <span>Upload a set of txt files</span>
        </div>
        <form method="post" action="index.php?corpus={$corpus.id}&amp;page={$page}" enctype="multipart/form-data">
            <input type="hidden" name="action" value="upload"/>
            <div class="panel-body">
                <div class="corpus-upload-grid">
                    <div class="corpus-upload-card corpus-upload-card-primary">
                        <div class="corpus-upload-card-icon">
                            <i class="fa fa-file-archive-o" aria-hidden="true"></i>
                        </div>
                        <div class="corpus-upload-card-content">
                            <h3>Zip package</h3>
                            <p>The archive must contain txt files. Optional ini files with matching names can provide document metadata.</p>
                            <div class="form-group">
                                <label for="corpus-upload-files">Zip file</label>
                                <input type="file" name="files" class="form-control corpus-upload-file-input" id="corpus-upload-files">
                            </div>
                        </div>
                    </div>

                    <div class="corpus-upload-card">
                        <div class="corpus-upload-card-icon">
                            <i class="fa fa-folder-open" aria-hidden="true"></i>
                        </div>
                        <div class="corpus-upload-card-content">
                            <h3>Subcorpus assignment</h3>
                            <p>Select a target subcorpus or split uploaded documents automatically using the filename prefix.</p>
                            <div class="form-group">
                                <label for="listSubcorpora">Subcorpus</label>
                                <select name="subcorpus_id" id="listSubcorpora" class="form-control">
                                    <option value="">none</option>
                                    {foreach from=$subcorpora item=s}
                                        <option value="{$s.subcorpus_id}">{$s.name}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <label class="corpus-upload-checkbox">
                                <input type="checkbox" name="autosplit" id="checkboxSubcorpora" value="option1">
                                <span aria-hidden="true"></span>
                                <span>Split into subcorpora based on the file prefix</span>
                            </label>
                            <code class="corpus-upload-pattern">
                                <span title="Subcorpus name">SUBCORPUS</span><em title="Separator">-</em><span title="Document name">DOCUMENT_NAME</span>.txt
                            </code>
                        </div>
                    </div>
                </div>

                <div class="corpus-upload-format-card">
                    <div class="corpus-upload-format-header">
                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                        <span>INI metadata format</span>
                    </div>
                    <pre>[metadata]
url = "Path to a web with the document source"
publish_date = "Publish date in the format of YYYY-MM-DD"
author = "Author name"
title = "Document title"</pre>
                </div>
            </div>
            <div class="panel-footer administration-content-footer corpus-upload-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-cloud-upload" aria-hidden="true"></i> Upload
                </button>
            </div>
        </form>
    </div>
</div>

{include file="inc_footer.tpl"}

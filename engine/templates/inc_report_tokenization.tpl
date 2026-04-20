{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div id="col-tokens" class="col-tokens col-md-4 scrollingWrapper report-tokenization-tokens-column">
    <div class="panel panel-primary administration-content-panel report-tokenization-panel">
        <div class="panel-heading administration-content-heading report-tokenization-heading">
            <span class="administration-content-heading-icon report-tokenization-heading-icon"><i class="fa fa-list-ol" aria-hidden="true"></i></span>
            <span>Tokens</span>
        </div>
        <div class="panel-body scrolling report-tokenization-table-wrapper">
            <table id="documentTokens" class="table table-striped report-tokenization-table">
                <thead>
                <th>No.</th>
                <th>Id</th>
                <th>From</th>
                <th>To</th>
                <th>Orth</th>
                <th>Text</th>
                <th></th>
                </thead>
                <tbody>
                {foreach from=$tokens item=t name=tokens}
                    <tr class="{if $t.orth != $t.text}mismatch{/if}" tokenId="{$t.token_id}">
                        <td class="col-num tokenNo">{$smarty.foreach.tokens.index+1}</td>
                        <td class="col-num tokenId"><small>{$t.token_id}</small></td>
                        <td class="col-num tokenFrom">{$t.from}</td>
                        <td class="col-num tokenTo">{$t.to}</td>
                        <td class="tokenOrth">{$t.orth}</td>
                        <td class="tokenText">{$t.text}</td>
                        <td class="icons">
                            <span class="hoverIcons">
								<a href="#" class="tokenMergeDown" title="Merge with token below">
									<i class="fa fa-arrow-down" aria-hidden="true"></i></a>
							</span>
                            {if strlen($t.text) > 1}
							<span class="hoverIcons">
								<a href="#" class="tokenSplit" title="Split token">
									<i class="fa fa-expand" aria-hidden="true"></i></a>
							</span>
                            {/if}
                            <span class="hoverIcons">
								<a href="#" class="tokenDelete" title="Delete token">
									<i class="fa fa-trash" aria-hidden="true"></i></a>
							</span>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
        <div id="dialog-split-token" class="report-tokenization-dialog" title="Split token" style="display: none; overflow: hidden">
            <p class="validateTips"></p>
            <form>
                        <div class="form-group row">
                        <label id="lbl_token_txt" for="token_1_t" class="col-sm-4 col-form-label"></label>
                        <div class="col-sm-8">
                        <input type="text" name="token_1_t" id="token_1_txt" value=""
                               class="text ui-widget-content ui-corner-all">
                        </div>
                    </div>
                    <hr style="margin-top: 10px; margin-bottom: 10px; border: 0; border-top: 1px solid #656060;">
                    <div class="form-group row">
                        <label for="token_2_t" class="col-sm-4 col-form-label">New token text:</label>
                        <div class="col-sm-8">
                        <input type="text" name="token_2_t" id="token_2_txt" value=""
                               class="text ui-widget-content ui-corner-all">
                        </div>
                    </div>
                    <!-- Allow form submission with keyboard without duplicating the dialog button -->
                    <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
            </form>
        </div>
    </div>
</div>

<div id="col-content" class="col-main {if $flags_active}col-md-5{else}col-md-6{/if} scrollingWrapper report-preview-content-column report-tokenization-content-column">
    <div class="panel panel-primary administration-content-panel report-preview-content-panel report-tokenization-content-panel">
        <div class="panel-heading administration-content-heading report-preview-panel-heading report-tokenization-heading">
            <span class="administration-content-heading-icon report-preview-heading-icon report-tokenization-heading-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
            <span>Document content</span>
        </div>
        <div class="panel-body report-preview-content-body report-tokenization-content-body">
            <div id="leftContent"
                 style="width: {if $showRight}50%{else}100%{/if};"
                 class="annotations scrolling content report-preview-document-content report-tokenization-document-content">
                <div id="rp-content" class="contentBox {$report.format} report-preview-content-box report-tokenization-content-box">{$content_inline|format_annotations}</div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-2 scrollingWrapper report-tokenization-config-column">
    <div class="panel panel-primary administration-content-panel report-tokenization-panel report-tokenization-config-panel">
        <div class="panel-heading administration-content-heading report-tokenization-heading">
            <span class="administration-content-heading-icon report-tokenization-heading-icon"><i class="fa fa-cogs" aria-hidden="true"></i></span>
            <span>Tokenization</span>
        </div>
        <div class="panel-body scrolling report-tokenization-config-body">
            <div class="panel panel-default report-tokenization-card">
                <div class="panel-heading report-tokenization-card-heading"><i class="fa fa-cloud" aria-hidden="true"></i> Using Web Service</div>
                <div class="panel-body report-tokenization-tagger-body" id="taggers">
                    {foreach from=$tokenization_options item=group}
                        <h6 class="report-tokenization-group-heading"><b>{$group.group}</b></h6>
                        {foreach from=$group.items item=option}
                            <div class="radio report-tokenization-option">
                                <label>
                                    <input {if $option.checked}checked {/if}
                                           type="radio"
                                           name="task"
                                           id="lpmn-postagger-{$option.tagger}-{$option.language}-{$option.tagset}"
                                           data-task="lpmn-postagger"
                                           data-tagger="{$option.tagger}"
                                           data-language="{$option.language}"
                                           data-tagset="{$option.tagset}"/>
                                    {$option.label}
                                </label>
                            </div>
                        {/foreach}
                    {/foreach}
                </div>
                <div class="panel-footer report-tokenization-card-footer">
                    <div class="form-group">
                        <button class="btn btn-primary report-tokenization-run-button" id="tokenizeText"><i class="fa fa-play" aria-hidden="true"></i> Tokenize</button>
                    </div>
                    <div class="form-group">
                        <div id="process_status" class="alert alert-info report-tokenization-status" style="display: none;">
                            <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                            <label for="status">Status:</label>
                            <span id="status">Queued</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

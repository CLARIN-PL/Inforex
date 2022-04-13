{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div id="col-tokens" class="col-tokens col-md-4 scrollingWrapper">
    <div class="panel panel-primary">
        <div class="panel-heading">Tokens</div>
        <div class="panel-body scrolling" style="padding: 0">
            <table id="documentTokens" class="table table-striped">
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
        <div id="dialog-split-token" title="Split token" style="overflow: hidden">
            <p class="validateTips"></p>
            <form>
                        <div class="form-group row">
                        <label id="lbl_token_txt" for="token_1_t" class="col-sm-4 col-form-label"></label>
                        <div class="col-sm-8">
                        <input type="text" name="token_1_t" id="token_1_txt" style="width: 100%;" value=""
                               class="text ui-widget-content ui-corner-all">
                        </div>
                    </div>
                    <hr style="margin-top: 10px; margin-bottom: 10px; border: 0; border-top: 1px solid #656060;">
                    <div class="form-group row">
                        <label for="token_2_t" class="col-sm-4 col-form-label">New token text:</label>
                        <div class="col-sm-8">
                        <input type="text" name="token_2_t" id="token_2_txt"  style="width: 100%;" value=""
                               class="text ui-widget-content ui-corner-all">
                        </div>
                    </div>
                    <!-- Allow form submission with keyboard without duplicating the dialog button -->
                    <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
            </form>
        </div>
    </div>
</div>

<div id="col-content" class="col-main {if $flags_active}col-md-5{else}col-md-6{/if} scrollingWrapper">
    <div class="panel panel-primary">
        <div class="panel-heading">Document content</div>
        <div class="panel-body" style="padding: 0">
            <div id="leftContent"
                 style="float:left; width: {if $showRight}50%{else}100%{/if}; border-right: 1px solid #E0CFC2"
                 class="annotations scrolling content">
                <div id="rp-content" style="margin: 5px" class="contentBox {$report.format}">{$content_inline|format_annotations}</div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-2 scrollingWrapper">
    <div class="panel panel-primary">
        <div class="panel-heading">Tokenization</div>
        <div class="panel-body scrolling">
            <div class="panel panel-default">
                <div class="panel-heading">Using Web Service</div>
                <div class="panel-body" id="taggers">
                    <h6><b>Polish</b></h6>
                    {*
                    <div class="radio">
                        <label><input type="radio" name="task" id="nlprest2-morphodita"/> Morphodita</label>
                    </div>
                    *}
                    <div class="radio">
                        <label><input {if $report.lang == "pol" || !$report.lang}checked {/if}type="radio" name="task"
                                      id="nlprest2-wcrft2-morfeusz1"/> Wcrft2 (Morfeusz1)</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="task" id="nlprest2-wcrft2-morfeusz2"/> Wcrft2
                            (Morfeusz2)</label>
                    </div>
                    <h6>English</h6>
                    <div class="radio">
                        <label><input {if $report.lang == "eng"}checked {/if}type="radio" name="task" id="nlprest2-en"/>
                            spaCy English</label>
                    </div>
                    <h6>German</h6>
                    <div class="radio">
                        <label><input {if $report.lang == "ger"}checked {/if}type="radio" name="task" id="nlprest2-de"/>
                            spaCy German</label>
                    </div>
                    <h6>Russian</h6>
                    <div class="radio">
                        <label><input {if $report.lang == "rus"}checked {/if}type="radio" name="task" id="nlprest2-ru"/>
                            UDPipe Russian</label>
                    </div>
                    <h6>Hebrew</h6>
                    <div class="radio">
                        <label><input {if $report.lang == "heb"}checked {/if}type="radio" name="task" id="nlprest2-he"/>
                            UDPipe Hebrew</label>
                    </div>
                    <h6>Czech</h6>
                    <div class="radio">
                        <label><input {if $report.lang == "cze"}checked {/if} type="radio" name="task"
                                      id="nlprest2-cs"/> UDPipe Czech</label>
                    </div>
                    <h6>Bulgarian</h6>
                    <div class="radio">
                        <label><input {if $report.lang == "bul"}checked {/if} type="radio" name="task"
                                      id="nlprest2-bg"/> UDPipe Bulgarian</label>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group">
                        <button class="btn btn-primary" id="tokenizeText">Tokenize</button>
                    </div>
                    <div class="form-group">
                        <div id="process_status" class="alert alert-info" style="display: none;">
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
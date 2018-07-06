{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}

<div class="panel panel-primary" style="margin: 5px">
    <div class="panel-heading">Corpus dashboard</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-4 scrollingWrapper">
                <div class="panel panel-default">
                    <div class="panel-heading">Corpus structure</div>
                    <div class="panel-body">
                       <div class="scrolling">
                        {if $subcorpora|@count == 0}
                            {capture assign=message}
                            <em>This corpus does not have any documents.</em>
                            {/capture}
                            {include file="common_message.tpl"}
                        {else}
                            <div id="piechart">xxx</div>

                            <script type="text/javascript">
                                var chartDataSubcorpora = [
                                  ['Subcorpus', 'documents']
                                    {foreach from=$subcorpora item=subcorpus}
                                        ,['{$subcorpus.name}', {$subcorpus.count}]
                                    {/foreach}
                                  ];
                            </script>
                        {/if}
                        </div>
                    </div>
                    <div class="panel-footer">
                    {assign var="comma" value=""}
                        Actions:
                        <ul style="margin: 0px; padding-left: 20px;">
                        {if "manager"|has_corpus_role_or_owner}
                            <li><a href="index.php?page=corpus_settings&amp;corpus={$corpus.id}&amp;subpage=subcorpora">add/remove subcorpora</a></li>
                        {/if}
                        {if "add_document"|has_corpus_role_or_owner}
                            <li><a href="index.php?page=corpus_document_add&amp;corpus={$corpus.id}">add document</a></li>
                        {/if}
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-sm-8 scrollingWrapper">
                <div class="panel panel-default">
                    <div class="panel-heading">Document flags</div>
                    <div class="panel-body">
                        <div class="scrolling">
                        <table class="table table-striped" cellspacing="1">
                            <thead>
                                <tr>
                                    <th>Flag (full)</th>
                                    <th>Flag (short)</th>
                                    <th style="text-align:center"><img style="vertical-align: baseline" title="not ready" src="gfx/flag_-1.png"></th>
                                    <th style="text-align:center"><img style="vertical-align: baseline" title="ready" src="gfx/flag_1.png"></th>
                                    <th style="text-align:center"><img style="vertical-align: baseline" title="in progress" src="gfx/flag_2.png"></th>
                                    <th style="text-align:center"><img style="vertical-align: baseline" title="finished" src="gfx/flag_3.png"></th>
                                    <th style="text-align:center"><img style="vertical-align: baseline" title="done" src="gfx/flag_4.png"></th>
                                    <th style="text-align:center"><img style="vertical-align: baseline" title="error" src="gfx/flag_5.png"></th>
                                    <th>Progress</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$flags item=flag}
                                    <tr>
                                        <td title="{$flag.description}"><b>{$flag.name}</b></td>
                                        <td><i>{$flag.short}</i></td>
                                        <td style="text-align: right">
                                            {if $flag.f0==0}-{else}<a href="index.php?page=corpus_documents&amp;corpus={$corpus.id}&amp;reset=1&amp;flag_{$flag.short}=0&filter_order=flag_{$flag.short}">{$flag.f0}{/if}
                                        </td>
                                        <td style="text-align: right">
                                            {if $flag.f1==0}-{else}<a href="index.php?page=corpus_documents&amp;corpus={$corpus.id}&amp;reset=1&amp;flag_{$flag.short}=1&filter_order=flag_{$flag.short}">{$flag.f1}{/if}
                                        </td>
                                        <td style="text-align: right">
                                            {if $flag.f2==0}-{else}<a href="index.php?page=corpus_documents&amp;corpus={$corpus.id}&amp;reset=1&amp;flag_{$flag.short}=2&filter_order=flag_{$flag.short}">{$flag.f2}{/if}
                                        </td>
                                        <td style="text-align: right">
                                            {if $flag.f3==0}-{else}<a href="index.php?page=corpus_documents&amp;corpus={$corpus.id}&amp;reset=1&amp;flag_{$flag.short}=3&filter_order=flag_{$flag.short}">{$flag.f3}{/if}
                                        </td>
                                        <td style="text-align: right">
                                            {if $flag.f4==0}-{else}<a href="index.php?page=corpus_documents&amp;corpus={$corpus.id}&amp;reset=1&amp;flag_{$flag.short}=4&filter_order=flag_{$flag.short}">{$flag.f4}{/if}
                                        </td>
                                        <td style="text-align: right">
                                            {if $flag.f5==0}-{else}<a href="index.php?page=corpus_documents&amp;corpus={$corpus.id}&amp;reset=1&amp;flag_{$flag.short}=5&filter_order=flag_{$flag.short}">{$flag.f5}{/if}
                                        </td>
                                        <td>
                                            {assign var="total" value=$flag.f0|intval+$flag.f1|intval+$flag.f2|intval+$flag.f3|intval+$flag.f4|intval+$flag.f5|intval}
                                            {math assign="total" equation='f1+f2+f3+f4+f5' f0=$flag.f0|intval f1=$flag.f1|intval f2=$flag.f2|intval f3=$flag.f3|intval f4=$flag.f4|intval f5=$flag.f5|intval}
                                            <div class="flag_progressbar" style="width: 105px">
                                                {*<div style="float: left; background: #ddd; width: {$flag.f0*100/$total}px">&nbsp;</div>*}
                                                <div style="float: left; background: #aaa; width: {if $total==0}0{else}{$flag.f1*100/$total}{/if}px">&nbsp;</div>
                                                <div style="float: left; background: #febc7e; width: {if $total==0}0{else}{$flag.f2*100/$total}{/if}px">&nbsp;</div>
                                                <div style="float: left; background: #3daeff; width: {if $total==0}0{else}{$flag.f3*100/$total}{/if}px">&nbsp;</div>
                                                <div style="float: left; background: #6ac855; width: {if $total==0}0{else}{$flag.f4*100/$total}{/if}px">&nbsp;</div>
                                                <div style="float: left; background: #fe493f; width: {if $total==0}0{else}{$flag.f5*100/$total}{/if}px">&nbsp;</div>
                                            </div>
                                        </td>
                                        <td>
                                            {if $flag.description}{$flag.description}{else}<i>n/a</i>{/if}
                                        </td>
                                    </tr>
                                {/foreach}
                                {if $flags|@count == 0}
                                <tr>
                                    <td colspan="10"><i>This corpus does not have any flags defined.</i></td>
                                </tr>
                                {/if}
                            </tbody>
                        </table>
                        </div>
                    </div>
                    <div class="panel-footer">
                        Actions: {if "manager"|has_corpus_role_or_owner}<a href="index.php?page=corpus_settings&amp;corpus={$corpus.id}&amp;subpage=flags">add/remove flags</a>{/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}

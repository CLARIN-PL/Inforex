{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<td class="table_cell_content">

<div style="width: 500px; float: left;">
    <h2>Corpus structure</h2>
    {if $subcorpora|@count == 0}
	    {capture assign=message}
	    <em>This corpus does not have any documents.</em> 
	    {/capture}
	    {include file="common_message.tpl"}     
    {else}
    <div id="piechart" style="width: 500px; height: 500px;"></div>
    
    <script type="text/javascript">
        var data = [
          ['Subcorpus', 'documents']
            {foreach from=$subcorpora item=subcorpus}
                ,['{$subcorpus.name}', {$subcorpus.count}]
            {/foreach}
          ];
        drawChartSubcorpora(data, "Corpus structure");
    </script>
    {/if}

    {assign var="comma" value=""}
    Actions: 
    <ul style="margin: 0px; padding-left: 20px;">
    {if "manager"|has_corpus_role_or_owner}
        <li><a href="index.php?page=corpus&amp;corpus={$corpus.id}&amp;subpage=subcorpora">add/remove subcorpora</a></li>
    {/if}
    {if "add_document"|has_corpus_role_or_owner}
        <li><a href="index.php?page=document_edit&amp;corpus={$corpus.id}">add document</a></li>
    {/if}
    </ul>
</div>

<div style="width: 700px; margin-left: 520px;">
    <h2>Document flags</h2>

    <table class="tablesorter" cellspacing="1">
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
            </tr>
        </thead>
        <tbody>
            {foreach from=$flags item=flag}
                <tr>
                    <td title="{$flag.description}"><b>{$flag.name}</b></td>
                    <td><i>{$flag.short}</i></td>
                    <td style="text-align: right">
                        {if $flag.f0==0}-{else}<a href="index.php?page=browse&amp;corpus={$corpus.id}&amp;reset=1&amp;flag_{$flag.short}=0&filter_order=flag_{$flag.short}">{$flag.f0}{/if}
                    </td>
                    <td style="text-align: right">
                        {if $flag.f1==0}-{else}<a href="index.php?page=browse&amp;corpus={$corpus.id}&amp;reset=1&amp;flag_{$flag.short}=1&filter_order=flag_{$flag.short}">{$flag.f1}{/if}
                    </td>
                    <td style="text-align: right">
                        {if $flag.f2==0}-{else}<a href="index.php?page=browse&amp;corpus={$corpus.id}&amp;reset=1&amp;flag_{$flag.short}=2&filter_order=flag_{$flag.short}">{$flag.f2}{/if}
                    </td>
                    <td style="text-align: right">
                        {if $flag.f3==0}-{else}<a href="index.php?page=browse&amp;corpus={$corpus.id}&amp;reset=1&amp;flag_{$flag.short}=3&filter_order=flag_{$flag.short}">{$flag.f3}{/if}
                    </td>
                    <td style="text-align: right">
                        {if $flag.f4==0}-{else}<a href="index.php?page=browse&amp;corpus={$corpus.id}&amp;reset=1&amp;flag_{$flag.short}=4&filter_order=flag_{$flag.short}">{$flag.f4}{/if}
                    </td>
                    <td style="text-align: right">
                        {if $flag.f5==0}-{else}<a href="index.php?page=browse&amp;corpus={$corpus.id}&amp;reset=1&amp;flag_{$flag.short}=5&filter_order=flag_{$flag.short}">{$flag.f5}{/if}
                    </td>
                </tr>
            {/foreach}
            {if $flags|@count == 0}
            <tr>
                <td colspan="8"><i>This corpus does not have any flags defined.</i></td>
            </tr>
            {/if}
        </tbody>
    </table>
    <br/>
    {*
    <div id="columnchart_values" style="width: 900px; height: 600px;"></div>
    <script type="text/javascript">
        var data = [ ['Flag','Not ready', 'Ready', 'In progress', 'Finished', 'Done', 'Error']
            {foreach from=$flags item=flag}
                ,['{$flag.name}', {$flag.f0}, {$flag.f1}, {$flag.f2}, {$flag.f3}, {$flag.f4}, {$flag.f5}]
            {/foreach}
          ];
        drawChartFlags(data, "Documents flags");
    </script>
    *}
    
    Actions: {if "manager"|has_corpus_role_or_owner}<a href="index.php?page=corpus&amp;corpus={$corpus.id}&amp;subpage=flags">add/remove flags</a>{/if}
         
</div>

<br style="clear: both"/>

{include file="inc_footer.tpl"}
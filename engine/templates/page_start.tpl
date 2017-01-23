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
   <div class="scroll">
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

<div style="margin-left: 520px;">
    <h2>Document flags</h2>
    <div class="scroll">
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
		    <td>
			{assign var="total" value=$flag.f0|intval+$flag.f1|intval+$flag.f2|intval+$flag.f3|intval+$flag.f4|intval+$flag.f5|intval}
			{math assign="total" equation='f1+f2+f3+f4+f5' f0=$flag.f0|intval f1=$flag.f1|intval f2=$flag.f2|intval f3=$flag.f3|intval f4=$flag.f4|intval f5=$flag.f5|intval}
			<div class="flag_progressbar" style="width: 105px">
				{*<div style="float: left; background: #ddd; width: {$flag.f0*100/$total}px">&nbsp;</div>*}
				<div style="float: left; background: #aaa; width: {$flag.f1*100/$total}px">&nbsp;</div>
				<div style="float: left; background: #febc7e; width: {$flag.f2*100/$total}px">&nbsp;</div>
				<div style="float: left; background: #3daeff; width: {$flag.f3*100/$total}px">&nbsp;</div>
				<div style="float: left; background: #6ac855; width: {$flag.f4*100/$total}px">&nbsp;</div>
				<div style="float: left; background: #fe493f; width: {$flag.f5*100/$total}px">&nbsp;</div>


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

{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<div>
{*
    <input type="button" id="corpoGrabberTask" class="button" role="button" value="Execute a new export"/>
*}
    <h2>History of exports</h2>
    <table id="exportHistory" class="tablesorter" cellspacing="1">
        <thead>
            <tr>
            	<th>Id</th>
            	<th>Status</th>
	            <th>Submitted</th>
	            <th>Processing started</th>
	            <th>Processing finished</th>
	            <th>Description</th>
	            <th>Selectors</th>
                <th>Extractors</th>
                <th>Indices</th>
                <th>Download</th>
	       </tr>
        </thead>
        <tbody>
        {foreach from=$exports item=export}
            <tr>
                <td>{$export.export_id}</td>
                <td>{$export.status}</td>
                <td>{$export.datetime_submit}</td>
                <td>{$export.datetime_start}</td>
                <td>{$export.datetime_finish}</td>
                <td>{$export.description}</td>
                <td>{$export.selectors|trim}</td>
                <td>{$export.extractors}</td>
                <td>{$export.indices}</td>
                <td>{if $export.status == "done"}<a href="index.php?page=export_download&amp;export_id={$export.export_id}">download</a>{/if}</td>
            </tr>
        {/foreach}        
        {if $exports|@count==0}
            <tr>
                <td colspan="10"><i>History of exports is empty</i></td>
            </tr>        
        {/if}
        </tbody>
    </table>
</div>

<br style="clear: both;"/>

{include file="inc_footer.tpl"}
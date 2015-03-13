{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<h1>Tasks</h1>

<div id="taskProgress" style="display: none">
    <table>
        <tbody>
            <tr><th>Type:</th><td class="type">-</td></tr>
            <tr><th>Parameters:</th><td class="parameters">-</td></tr>
            <tr><th>Status:</th><td class="status">-</td></tr>
            <tr><th>Position in queue:</th><td class="queue">-</td></tr>
        </tbody>
    </table>
    <hr/>
    <table>
        <tbody>
            <tr><th>Documents to process:</th><td class="documents">-</td></tr>
            <tr><th>Documents processed:</th><td class="processed">-</td></tr>
            <tr><th>Documents with errors:</th><td class="errors">-</td></tr>
            <tr><th>Progress:</th><td><span class="progress"></span>%</td></tr>
        </tbody>
    </table>
    <div id="progressbar" class="ui-progressbar ui-widget ui-widget-content ui-corner-all" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="20"><div class="ui-progressbar-value ui-widget-header ui-corner-left" style="width: 0%;"></div></div>
</div>

<div style="float: left; width: 400px;">
	<h2>New task</h2>
	<form id="task" class="pure-form pure-form-aligned" method="POST">
		<h3>Choose task</h2>
		
		<ul class="tasks">
		   <li><input type="radio" name="task" value="liner2:model=nam" checked="checked"/> Recognize boundaries of named entities with Liner2.</li>
	       <li><input type="radio" name="task" value="liner2:model=top9"/> Recognize top 9 categories of named entities.</li>
		</ul>
		
	    <h3>Choose documents</h2>
	
	    <ul class="documents">
	       <li><input type="radio" name="documents" value="all" checked="checked"/> All documents.</li>
	    </ul>
	
        <input type="button" id="button" class="button" role="button" value="Submit"/>
	</form>
</div>	

<div style="margin-left: 420px; width: 500px;" >
    <h2>History of tasks</h2>
    <table id="taskHistory" class="tablesorter" cellspacing="1">
        <thead>
            <tr>
	            <th>Date and time</th>
	            <th>Task</th>
	            <th>Parameters</th>
	            <th style="width: 40px">Documents</th>
	            <th>User</th>
                <th>Status</th>
	       </tr>
        </thead>
        <tbody>
        {foreach from=$tasks item=task}
            <tr>
                <td>{$task.datetime}</td>
                <td>{$task.type}</td>
                <td>{$task.parameters}</td>
                <td style="text-align: right">{$task.documents}</td>
                <td>{$task.screename}</td>
                <td style="text-align: center"><a href="#" task_id="{$task.task_id}" title="click to see details">{$task.status}</a></td>
            </tr>
        {/foreach}        
        </tbody>
    </table>
</div>


<br style="clear: both;"/>

{include file="inc_footer.tpl"}
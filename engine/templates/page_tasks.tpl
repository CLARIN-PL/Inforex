{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

{* Template of dialog for new task *}
<div id="dialogNewTask" style="display: none">
    <div class="dialogNewTask">
	    <form id="task" class="pure-form pure-form-aligned" method="POST">
	        <h2>Choose task</h2>
	        
	        <ol class="tasks">
		        <li>Named entites
		            <ul>
		               <li><input type="radio" name="task" value="liner2:model=ner-names:annotation_set_id=19" checked="checked"/> Without categorization.</li>
		               <li><input type="radio" name="task" value="liner2:model=ner-top9:annotation_set_id=20"/> Top 9 categories.</li>
	                   <li><input type="radio" name="task" value="liner2:model=ner-n82:annotation_set_id=21"/> 82 fine-grained categories.</li>
		            </ul>	        
		        </li>
	            <li style="margin-top: 10px;">Temporal expressions
	                <ul>
	                   <li><input type="radio" name="task" value="liner2:model=timex1:annotation_set_id=15"/> Without categorization.</li>
	                   <li><input type="radio" name="task" value="liner2:model=timex4:annotation_set_id=15"/> 4 main categories.</li>
	                </ul>           
	            </li>
                <li style="margin-top: 10px;">Other
                    <ul>
                       <li><input type="radio" name="task" value="update-ccl"/> Update ccl files.</li>
                    </ul>           
                </li>
	        </ol>
	        	        
	        <h2>Choose documents</h2>
	    
	        <ul class="documents">
	           <li><input type="radio" name="documents" value="all" checked="checked"/> All documents.</li>
	        </ul>
	    </form>
    </div>
</div>  

<div style="width: 500px; float: left" >
    <input type="button" id="buttonNewTask" class="button" role="button" value="New task" style="display:none"/>
    <input type="button" id="corpoGrabberTask" class="button" role="button" value="New CorpoGrabber task"/>
    <h2>History of tasks</h2>
    <table id="taskHistory" class="tablesorter" cellspacing="1">
        <thead>
            <tr>
	            <th>Date and time</th>
	            <th>Task</th>
	            <th style="width: 40px">Documents</th>
	            <th>User</th>
                <th>Status</th>
	       </tr>
        </thead>
        <tbody>
        {foreach from=$tasks item=task_item}
            <tr{if $task_item.task_id==$task_id} class="selected"{/if}>
                <td>{$task_item.datetime}</td>
                <td>{$task_item.type}</td>
                <td style="text-align: right">{$task_item.documents}</td>
                <td>{$task_item.screename}</td>
                <td style="text-align: center"><a href="index.php?page=tasks&amp;corpus={$corpus.id}&amp;task_id={$task_item.task_id}" title="click to see details">{$task_item.status}</a></td>
            </tr>
        {/foreach}        
        {if $tasks|@count==0}
            <tr>
                <td colspan="6"><i>History of tasks is empty</i></td>
            </tr>        
        {/if}
        </tbody>
    </table>
</div>

{if $task_id>0}
	<div id="taskProgress" task_id="{$task_id}" style="margin-left: 510px;" >
       <h2>Task</h2>
       <table style="width: 99%">
           <tr>
               <th>Date and time:</th>
               <td id="taskDateTime">{$task.datetime}</td>
           </tr>
           <tr>
               <th>Type:</th>
               <td id="taskType">{$task.type}</td>
           </tr>           
           <tr>
               <th>Description:</th>
               <td>{if $task.description != ""}{$task.description}{else}{$task.type}{/if}</td>
           </tr>
       </table>
       
	   <h2>Status</h2>
		<div id="taskError" class="ui-state-error ui-corner-all" style="padding: 0pt 0.7em; margin: 10px; display: none;"> 
		    <p style="padding: 10px"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span>
		    Error: <span class="message" style="font-weight: bold"></span></p>
		</div>
	    <table style="width: 99%" id="taskStatus">
	       <tr id="taskStatusRow">
	           <th><span class="status">-</span></th>
	           <td><span class="status_msg"></span><div id="progressbar" style="display: none" class="ui-progressbar ui-widget ui-widget-content ui-corner-all" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="20"><div id="progressbarValue" class="ui-progressbar-value ui-widget-header ui-corner-left" style="width: 0%;"></div></div></td>
	       </tr>
	    </table>

        <h2>Documents status</h2>
        <table>
            <tbody>
                <tr><th>Documents to process:</th><td class="documents">-</td></tr>
                <tr><th>Documents processed:</th><td class="processed">-</td></tr>
                <tr><th>Documents with errors:</th><td class="errors">-</td></tr>
                </tr>
            </tbody>
        </table>
	    <div id="documents_status" style="height: 300px; overflow: auto;">	    
		    <table class="documents tablesorter" cellspacing="1">
		      <thead>
		          <th style="text-align: left; width: 100px">Document id</th>
		          <th style="text-align: left; width: 100px">Status</th>
		          <th style="text-align: left; width: auto">Message</th>
		          <th style="text-align: left; width: auto">Actions</th>
		      </thead>
		      <tbody>
		      </tbody>
		    </table>
	    </div>
	</div>
{/if}

<br style="clear: both;"/>

{include file="inc_footer.tpl"}
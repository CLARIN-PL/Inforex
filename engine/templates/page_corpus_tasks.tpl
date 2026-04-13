{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}

<!-- Modal -->
<div id="dialogNewTask" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Execute a new task</h4>
			</div>
			<div class="modal-body">
                <div style = "display: none;" class="alert alert-danger no_documents_error text-center">
                    <strong>There are no documents meeting the criteria.</strong>
                </div>

				<div class="panel-group" id="accordion">
					{*
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
									Annotate with named entities, temporal expressions or event</a>
							</h4>
						</div>
						<div id="collapse1" class="panel-collapse collapse in">
							<div class="panel-body">Recognize and add annotations of the selected category. The annotations are added with stage <em>new</em> and can be manually verified in the <em>Bootstrap</em> perspective.</div>
							<div class="panel-body">
								<h4>Named entites</h4>
								<div class="radio">
									<label><input type="radio" name="task" id="liner2:model=ner-names:annotation_set_id=19"/> Without categorization.</label>
								</div>
								<div class="radio">
									<label><input type="radio" name="task" id="liner2:model=ner-top9:annotation_set_id=1"/> Top 9 categories.</label>
								</div>
								<div class="radio">
									<label><input type="radio" name="task" id="liner2:model=ner-n82:annotation_set_id=21"/> 82 fine-grained categories.</label>
								</div>

								<h4>Temporal expressions</h4>
								<div class="radio">
									<label><input type="radio" name="task" id="liner2:model=timex1:annotation_set_id=15"/> Without categorization.</label>
								</div>
								<div class="radio">
									<label><input type="radio" name="task" id="liner2:model=timex4:annotation_set_id=15"/> 4 main categories.</label>
								</div>

								<h4>Other</h4>
								<div class="radio">
									<label><input type="radio" name="task" id="liner2:model=event8:annotation_set_id=15"/> TimeML events</label>
								</div>
							</div>
						</div>
					</div>
					*}
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapse2">
									Morphological tagging</a>
							</h4>
						</div>
							<div id="collapse2" class="panel-collapse collapse in">
								<div class="panel-body">
									Divide text into sentences and tokens. For each token assign a base form and a morphological analysis.</div>
								<div class="panel-body">
									<h4>MorphoDita</h4>
									<div class="radio">
										<label><input type="radio" name="task" id="lpmn-postagger-morphodita-pl-nkjp" data-task="lpmn-postagger" data-tagger="morphodita" data-language="pl" data-tagset="nkjp"/> Polish, NKJP tagset</label>
									</div>
									<div class="radio">
										<label><input type="radio" name="task" id="lpmn-postagger-morphodita-pl-sgjp" data-task="lpmn-postagger" data-tagger="morphodita" data-language="pl" data-tagset="sgjp"/> Polish, SGJP tagset</label>
									</div>
									<h4>PTag</h4>
									<div class="radio">
										<label><input type="radio" name="task" id="lpmn-postagger-ptag-pl-nkjp" data-task="lpmn-postagger" data-tagger="ptag" data-language="pl" data-tagset="nkjp"/> Polish, NKJP tagset</label>
									</div>
									<h4>Archeopteryx</h4>
									<div class="radio">
										<label><input type="radio" name="task" id="lpmn-postagger-archeopteryx-pl-nkjp" data-task="lpmn-postagger" data-tagger="archeopteryx" data-language="pl" data-tagset="nkjp"/> Polish, NKJP tagset</label>
									</div>
									<h4>LLM POS Tagger</h4>
									<div class="radio">
										<label><input type="radio" name="task" id="lpmn-postagger-llm-pos-tagger-pl-nkjp" data-task="lpmn-postagger" data-tagger="llm-pos-tagger" data-language="pl" data-tagset="nkjp"/> Polish, NKJP tagset</label>
									</div>
									<h4>spaCy UD</h4>
									<div class="radio">
										<label><input type="radio" name="task" id="lpmn-postagger-spacy-pl-ud" data-task="lpmn-postagger" data-tagger="spacy" data-language="pl" data-tagset="ud"/> Polish</label>
									</div>
									<div class="radio">
										<label><input type="radio" name="task" id="lpmn-postagger-spacy-en-ud" data-task="lpmn-postagger" data-tagger="spacy" data-language="en" data-tagset="ud"/> English</label>
									</div>
									<div class="radio">
										<label><input type="radio" name="task" id="lpmn-postagger-spacy-de-ud" data-task="lpmn-postagger" data-tagger="spacy" data-language="de" data-tagset="ud"/> German</label>
									</div>
									<div class="radio">
										<label><input type="radio" name="task" id="lpmn-postagger-spacy-ru-ud" data-task="lpmn-postagger" data-tagger="spacy" data-language="ru" data-tagset="ud"/> Russian</label>
									</div>
									<div class="radio">
										<label><input type="radio" name="task" id="lpmn-postagger-spacy-pt-ud" data-task="lpmn-postagger" data-tagger="spacy" data-language="pt" data-tagset="ud"/> Portuguese</label>
									</div>
									<div class="radio">
										<label><input type="radio" name="task" id="lpmn-postagger-spacy-fr-ud" data-task="lpmn-postagger" data-tagger="spacy" data-language="fr" data-tagset="ud"/> French</label>
									</div>
									<div class="radio">
										<label><input type="radio" name="task" id="lpmn-postagger-spacy-es-ud" data-task="lpmn-postagger" data-tagger="spacy" data-language="es" data-tagset="ud"/> Spanish</label>
									</div>
								</div>
							</div>
						</div>
					</div>

				<div class="panel panel-info">
					<div class="panel-heading">Select document to process</div>
					<div class="panel-body documents">
						<div class="radio">
							<label><input type="radio" name="documents" value="all" class="all_documents"> All documents</label>
						</div>
						<div class="radio">
							<label><input type="radio" name="documents" class="documents_by_flag_radio"> Select by flag status</label>
							<br/>
							<div class = "documents_by_flag" style = "display: none;">
								<select class="selectpicker" id="selected_flags">
									<option value="none" selected="selected">-Flag-</option>
									{foreach from=$flags_names  item="set"}
										<option value="{$set.corpora_flag_id}">{$set.short}</option>
										</optgroup>
									{/foreach}
								</select>
								<select class="selectpicker" id="selected_action" name="selected_flags">
									<option value="none" selected="selected">-Status-</option>
									{foreach from=$flags  item="set"}
										<option value="{$set.flag_id}">{$set.name}</option>
										</optgroup>
									{/foreach}
								</select>
								<br/>
								&nbsp;<span class="badge" id = "num_of_selected">0</span>&nbsp;document(s)
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				{*<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>*}
				<button class="btn btn-primary" id="dialogNewTaskExecute">Execute</button>
			</div>
		</div>

	</div>
</div>

<div class="row">
	<div class="col-md-6 scrollingWrapper">
		<div class="panel panel-primary">
			<div class="panel-heading">List of executed tasks</div>
			<div class="panel-body scrolling" style="padding: 0">
				<table id="taskHistory" class="table table-striped" cellspacing="1">
					<thead>
						<tr>
							<th class="td-center">Date and time</th>
							<th class="td-center">Task</th>
							<th class="td-right">Documents</th>
							<th class="td-center">Executed by</th>
							<th class="td-center">Status</th>
							<th class="td-center">Details</th>
					   </tr>
					</thead>
					<tbody>
					{foreach from=$tasks item=task_item}
						<tr{if $task_item.task_id==$task_id} class="selected"{/if}>
							<td class="td-center">{$task_item.datetime}</td>
							<td class="td-center">{$task_item.type}</td>
							<td class="td-right">{$task_item.documents}</td>
							<td class="td-center">{$task_item.screename}</td>
							<td class="td-center">{$task_item.status}</td>
							<td style="text-align: center"><a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;task_id={$task_item.task_id}" title="click to see details">show details</a></td>
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
			<div class="panel-footer">
				<input type="button" id="buttonNewTask" class="btn btn-primary" role="button" value="New task" data-toggle="modal" data-target="#dialogNewTask"/>
				<input type="button" id="corpoGrabberTask" class="btn btn-primary" role="button" value="New CorpoGrabber task"/>
			</div>
		</div>
	</div>

	<div class="col-md-6 scrollingWrapper">
	{if $task_id>0}
		<div id="taskProgress" task_id="{$task_id}">
			   <div class="panel panel-info">
				   <div class="panel-heading">Task details</div>
				   <div class="panel-body" style="padding: 0">
					   <table class="table table-striped">
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
						   <tr>
							   <th>Status:</th>
							   <td><span class="status"><i>checking...</i></span></td>
						   </tr>
						   <tr id="taskStatusRow">
							   <th style="vertical-align: middle">Progress:</th>
							   <td style="vertical-align: middle; height: 50px">
								   <span class="status_msg"></span><div id="progressbar" style="display: none" class="ui-progressbar ui-widget ui-widget-content ui-corner-all" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="20"><div id="progressbarValue" class="ui-progressbar-value ui-widget-header ui-corner-left" style="width: 0%;"></div></div>
							   </td>
						   </tr>
					   </table>
				   </div>
				   <div id="taskError" class="ui-state-error ui-corner-all" style="padding: 0pt 0.7em; margin: 10px; display: none;">
					   <p style="padding: 10px"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span>
						   Error: <span class="message" style="font-weight: bold"></span></p>
				   </div>
			   </div>

				<div class="panel panel-info">
					<div class="panel-heading">Documents</div>
					<div class="panel-footer" style="padding: 0">
						<table class="table table-striped">
							<tbody>
							<tr>
								<th style="width: 150px">Documents to process:</th><td><span class="badge documents">-</span></td>
								<th style="width: 150px">Documents processed:</th><td><span class="badge processed">-</span></td>
								<th style="width: 200px">Documents with errors:</th><td><span class="badge errors">-</span></td>
							</tr>
							</tbody>
						</table>
					</div>
					<div class="panel-body">
						<div id="documents_status" class="scrolling">
							<table class="documents table table-striped" cellspacing="1">
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
				</div>
		</div>
	{/if}
	</div>

</div>

{include file="inc_footer.tpl"}

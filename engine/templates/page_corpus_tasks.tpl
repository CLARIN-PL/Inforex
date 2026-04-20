{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}

<!-- Modal -->
<div id="dialogNewTask" class="modal fade corpus-tasks-modal" role="dialog">
	<div class="modal-dialog corpus-tasks-modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><i class="fa fa-cogs" aria-hidden="true"></i> Execute a new task</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body corpus-tasks-modal-body">
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
								<div class="panel-body corpus-tasks-modal-intro">
									Divide text into sentences and tokens. For each token assign a base form and a morphological analysis.</div>
								<div class="panel-body corpus-tasks-modal-task-body">
									<div class="corpus-tasks-task-section">
										<div class="corpus-tasks-task-section-heading">Polish taggers</div>
										<div class="corpus-tasks-task-options">
											<label class="corpus-tasks-task-option">
												<input type="radio" name="task" id="lpmn-postagger-morphodita-pl-nkjp" data-task="lpmn-postagger" data-tagger="morphodita" data-language="pl" data-tagset="nkjp"/>
												<span class="corpus-tasks-task-option-card">
													<span class="corpus-tasks-task-option-title corpus-tasks-task-option-title-inline">MorphoDita <span class="corpus-tasks-task-badge">NKJP</span></span>
												</span>
											</label>
											<label class="corpus-tasks-task-option">
												<input type="radio" name="task" id="lpmn-postagger-morphodita-pl-sgjp" data-task="lpmn-postagger" data-tagger="morphodita" data-language="pl" data-tagset="sgjp"/>
												<span class="corpus-tasks-task-option-card">
													<span class="corpus-tasks-task-option-title corpus-tasks-task-option-title-inline">MorphoDita <span class="corpus-tasks-task-badge">SGJP</span></span>
												</span>
											</label>
											<label class="corpus-tasks-task-option">
												<input type="radio" name="task" id="lpmn-postagger-ptag-pl-nkjp" data-task="lpmn-postagger" data-tagger="ptag" data-language="pl" data-tagset="nkjp"/>
												<span class="corpus-tasks-task-option-card">
													<span class="corpus-tasks-task-option-title corpus-tasks-task-option-title-inline">PTag <span class="corpus-tasks-task-badge">NKJP</span></span>
												</span>
											</label>
											<label class="corpus-tasks-task-option">
												<input type="radio" name="task" id="lpmn-postagger-archeopteryx-pl-nkjp" data-task="lpmn-postagger" data-tagger="archeopteryx" data-language="pl" data-tagset="nkjp"/>
												<span class="corpus-tasks-task-option-card">
													<span class="corpus-tasks-task-option-title corpus-tasks-task-option-title-inline">Archeopteryx <span class="corpus-tasks-task-badge">NKJP</span></span>
												</span>
											</label>
											<label class="corpus-tasks-task-option">
												<input type="radio" name="task" id="lpmn-postagger-llm-pos-tagger-pl-nkjp" data-task="lpmn-postagger" data-tagger="llm-pos-tagger" data-language="pl" data-tagset="nkjp"/>
												<span class="corpus-tasks-task-option-card">
													<span class="corpus-tasks-task-option-title corpus-tasks-task-option-title-inline">LLM POS Tagger <span class="corpus-tasks-task-badge">NKJP</span></span>
												</span>
											</label>
										</div>
									</div>
									<div class="corpus-tasks-task-section">
										<div class="corpus-tasks-task-section-heading">spaCy UD</div>
										<div class="corpus-tasks-task-options corpus-tasks-task-options-languages">
											<label class="corpus-tasks-task-option">
												<input type="radio" name="task" id="lpmn-postagger-spacy-pl-ud" data-task="lpmn-postagger" data-tagger="spacy" data-language="pl" data-tagset="ud"/>
												<span class="corpus-tasks-task-option-card">
													<span class="corpus-tasks-task-option-title corpus-tasks-task-option-title-inline">Polish <span class="corpus-tasks-task-badge">UD</span></span>
												</span>
											</label>
											<label class="corpus-tasks-task-option">
												<input type="radio" name="task" id="lpmn-postagger-spacy-en-ud" data-task="lpmn-postagger" data-tagger="spacy" data-language="en" data-tagset="ud"/>
												<span class="corpus-tasks-task-option-card">
													<span class="corpus-tasks-task-option-title corpus-tasks-task-option-title-inline">English <span class="corpus-tasks-task-badge">UD</span></span>
												</span>
											</label>
											<label class="corpus-tasks-task-option">
												<input type="radio" name="task" id="lpmn-postagger-spacy-de-ud" data-task="lpmn-postagger" data-tagger="spacy" data-language="de" data-tagset="ud"/>
												<span class="corpus-tasks-task-option-card">
													<span class="corpus-tasks-task-option-title corpus-tasks-task-option-title-inline">German <span class="corpus-tasks-task-badge">UD</span></span>
												</span>
											</label>
											<label class="corpus-tasks-task-option">
												<input type="radio" name="task" id="lpmn-postagger-spacy-ru-ud" data-task="lpmn-postagger" data-tagger="spacy" data-language="ru" data-tagset="ud"/>
												<span class="corpus-tasks-task-option-card">
													<span class="corpus-tasks-task-option-title corpus-tasks-task-option-title-inline">Russian <span class="corpus-tasks-task-badge">UD</span></span>
												</span>
											</label>
											<label class="corpus-tasks-task-option">
												<input type="radio" name="task" id="lpmn-postagger-spacy-pt-ud" data-task="lpmn-postagger" data-tagger="spacy" data-language="pt" data-tagset="ud"/>
												<span class="corpus-tasks-task-option-card">
													<span class="corpus-tasks-task-option-title corpus-tasks-task-option-title-inline">Portuguese <span class="corpus-tasks-task-badge">UD</span></span>
												</span>
											</label>
											<label class="corpus-tasks-task-option">
												<input type="radio" name="task" id="lpmn-postagger-spacy-fr-ud" data-task="lpmn-postagger" data-tagger="spacy" data-language="fr" data-tagset="ud"/>
												<span class="corpus-tasks-task-option-card">
													<span class="corpus-tasks-task-option-title corpus-tasks-task-option-title-inline">French <span class="corpus-tasks-task-badge">UD</span></span>
												</span>
											</label>
											<label class="corpus-tasks-task-option">
												<input type="radio" name="task" id="lpmn-postagger-spacy-es-ud" data-task="lpmn-postagger" data-tagger="spacy" data-language="es" data-tagset="ud"/>
												<span class="corpus-tasks-task-option-card">
													<span class="corpus-tasks-task-option-title corpus-tasks-task-option-title-inline">Spanish <span class="corpus-tasks-task-badge">UD</span></span>
												</span>
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

				<div class="panel panel-info corpus-tasks-documents-panel">
					<div class="panel-heading">Select document to process</div>
					<div class="panel-body documents">
						<div class="radio">
							<label class="corpus-tasks-documents-option"><input type="radio" name="documents" value="all" class="all_documents"> <span>All documents</span></label>
						</div>
						<div class="radio">
							<label class="corpus-tasks-documents-option"><input type="radio" name="documents" class="documents_by_flag_radio"> <span>Select by flag status</span></label>
							<div class = "documents_by_flag" style = "display: none;">
								<div class="corpus-tasks-documents-selects">
									<select class="selectpicker" id="selected_flags">
										<option value="none" selected="selected">Flag</option>
										{foreach from=$flags_names  item="set"}
											<option value="{$set.corpora_flag_id}">{$set.short}</option>
											</optgroup>
										{/foreach}
									</select>
									<select class="selectpicker" id="selected_action" name="selected_flags">
										<option value="none" selected="selected">Status</option>
										{foreach from=$flags  item="set"}
											<option value="{$set.flag_id}">{$set.name}</option>
											</optgroup>
										{/foreach}
									</select>
								</div>
								<div class="corpus-tasks-documents-count">
									<span class="badge" id="num_of_selected">0</span>
									<span>document(s)</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer corpus-tasks-modal-footer">
				{*<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>*}
				<button class="btn btn-primary corpus-tasks-execute-button" id="dialogNewTaskExecute"><i class="fa fa-play" aria-hidden="true"></i> Execute</button>
			</div>
		</div>

	</div>
</div>

<div class="container-fluid admin_tables corpus-tasks-page">
	<div class="row corpus-tasks-grid">
	<div class="col-md-6 corpus-tasks-column">
		<div class="panel panel-primary scrollingWrapper administration-content-panel corpus-tasks-panel">
			<div class="panel-heading administration-content-heading corpus-tasks-heading">
				<span class="administration-content-heading-icon"><i class="fa fa-tasks" aria-hidden="true"></i></span>
				<span>List of executed tasks</span>
				<span class="home-corpora-counter corpus-tasks-counter">{$tasks|@count}</span>
			</div>
			<div class="panel-body corpus-tasks-panel-body">
				<div class="administration-table-wrapper corpus-tasks-table-wrapper">
				<table id="taskHistory" class="table table-striped table-hover administration-table corpus-tasks-table" cellspacing="1">
					<thead>
						<tr>
							<th class="td-center corpus-tasks-datetime-column">Date and time</th>
							<th class="td-center">Task</th>
							<th class="td-right corpus-tasks-docs-column">Documents</th>
							<th class="td-center">Executed by</th>
							<th class="td-center corpus-tasks-status-column">Status</th>
					   </tr>
					</thead>
					<tbody>
					{foreach from=$tasks item=task_item}
						<tr{if $task_item.task_id==$task_id} class="selected corpus-tasks-row-selected"{/if} data-href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;task_id={$task_item.task_id}">
							<td class="td-center">
								<span class="administration-activities-time corpus-tasks-time" title="{$task_item.datetime|escape}">
									<i class="fa fa-clock-o" aria-hidden="true"></i>
									<span>{$task_item.datetime|date_format:"%Y-%m-%d"}</span>
									<small>{$task_item.datetime|date_format:'%H:%M'}</small>
								</span>
							</td>
							<td class="td-center">{$task_item.type}</td>
							<td class="td-right"><span class="home-corpora-documents-badge corpus-tasks-documents-badge">{$task_item.documents}</span></td>
							<td class="td-center">{$task_item.screename}</td>
							<td class="td-center"><span class="corpus-tasks-status-badge corpus-tasks-status-{$task_item.status|regex_replace:"/[^a-zA-Z0-9_-]+/":"-"|lower}">{$task_item.status}</span></td>
						</tr>
					{/foreach}
					{if $tasks|@count==0}
						<tr>
							<td colspan="5">
								<div class="home-corpora-empty corpus-tasks-empty">
									<i class="fa fa-inbox" aria-hidden="true"></i>
									<span>History of tasks is empty.</span>
								</div>
							</td>
						</tr>
					{/if}
					</tbody>
				</table>
				</div>
				<div class="home-corpora-pagination corpus-tasks-pagination" id="task_history_pagination">
					<div class="home-corpora-pagination-info" id="task_history_pagination_info"></div>
					<div class="home-corpora-pagination-controls" id="task_history_pagination_controls"></div>
				</div>
			</div>
			<div class="panel-footer administration-content-footer corpus-tasks-footer">
				<input type="button" id="buttonNewTask" class="btn btn-primary corpus-tasks-primary-button" role="button" value="New task" data-toggle="modal" data-target="#dialogNewTask"/>
			</div>
		</div>
	</div>

	<div class="col-md-6 corpus-tasks-column">
	{if $task_id>0}
		<div id="taskProgress" task_id="{$task_id}" class="corpus-tasks-progress">
			   <div class="panel panel-info administration-content-panel corpus-tasks-panel">
				   <div class="panel-heading administration-content-heading corpus-tasks-heading">
					   <span class="administration-content-heading-icon"><i class="fa fa-info-circle" aria-hidden="true"></i></span>
					   <span>Task details</span>
				   </div>
				   <div class="panel-body corpus-tasks-panel-body">
					   <div class="administration-table-wrapper corpus-tasks-detail-table-wrapper">
					   <table class="table table-striped administration-table corpus-tasks-detail-table">
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
							   <td class="corpus-tasks-progress-cell" style="vertical-align: middle; height: 50px">
								   <span class="status_msg corpus-tasks-status-message"></span><div id="progressbar" style="display: none" class="ui-progressbar ui-widget ui-widget-content ui-corner-all corpus-tasks-progressbar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="20"><div id="progressbarValue" class="ui-progressbar-value ui-widget-header ui-corner-left corpus-tasks-progressbar-value" style="width: 0%;"></div></div>
							   </td>
						   </tr>
					   </table>
					   </div>
				   </div>
				   <div id="taskError" class="ui-state-error ui-corner-all corpus-tasks-error-box" style="display: none;">
					   <p style="padding: 10px"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span>
						   Error: <span class="message" style="font-weight: bold"></span></p>
				   </div>
			   </div>

				<div class="panel panel-info administration-content-panel corpus-tasks-panel">
					<div class="panel-heading administration-content-heading corpus-tasks-heading">
						<span class="administration-content-heading-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
						<span>Documents</span>
					</div>
					<div class="panel-body corpus-tasks-panel-body">
						<div class="corpus-tasks-document-stats">
							<div class="corpus-tasks-stat-card corpus-tasks-stat-card-pending">
								<div class="corpus-tasks-stat-icon"><i class="fa fa-hourglass-start" aria-hidden="true"></i></div>
								<div class="corpus-tasks-stat-content">
									<div class="corpus-tasks-stat-label">To process</div>
									<div class="corpus-tasks-stat-value corpus-tasks-stat-documents">-</div>
								</div>
							</div>
							<div class="corpus-tasks-stat-card corpus-tasks-stat-card-done">
								<div class="corpus-tasks-stat-icon"><i class="fa fa-check-circle" aria-hidden="true"></i></div>
								<div class="corpus-tasks-stat-content">
									<div class="corpus-tasks-stat-label">Processed</div>
									<div class="corpus-tasks-stat-value corpus-tasks-stat-processed">-</div>
								</div>
							</div>
							<div class="corpus-tasks-stat-card corpus-tasks-stat-card-error">
								<div class="corpus-tasks-stat-icon"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></div>
								<div class="corpus-tasks-stat-content">
									<div class="corpus-tasks-stat-label">Errors</div>
									<div class="corpus-tasks-stat-value corpus-tasks-stat-errors">-</div>
								</div>
							</div>
						</div>
						<div id="documents_status" class="scrolling">
							<div class="administration-table-wrapper corpus-tasks-document-table-wrapper">
							<table class="documents table table-striped administration-table corpus-tasks-document-table" cellspacing="1">
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
							<div class="home-corpora-pagination corpus-tasks-pagination" id="task_documents_pagination">
								<div class="home-corpora-pagination-info" id="task_documents_pagination_info"></div>
								<div class="home-corpora-pagination-controls" id="task_documents_pagination_controls"></div>
							</div>
						</div>
					</div>
				</div>
		</div>
	{/if}
	</div>

</div>
</div>

{include file="inc_footer.tpl"}

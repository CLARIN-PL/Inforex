{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
<div id="col-content" class="col-main {if $flags_active}col-md-8{else}col-md-9{/if} scrollingWrapper">
	<div class="panel panel-primary">
		<div class="panel-heading">Document content</div>
		<div class="panel-body" style="padding: 0">
			<div id="leftContent" style="float:left; width: {if $showRight}50%{else}100%{/if}; border-right: 1px solid #E0CFC2" class="annotations scrolling content">
				<div style="margin: 5px" class="contentBox">
					{$content_inline}
				</div>
			</div>
		</div>
	</div>
</div>
<div class = "col-md-3 scrollingWrapper">
	<div class = "panel panel-primary">
		<div class = "panel-heading">Tokenization</div>
		<div class = "panel-body scrolling">
			{*
			<div class = "panel panel-default">
				<div class = "panel-heading">From CCL file</div>
				<div class = "panel-body">
					<form method="POST" action="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage=tokenization&amp;id={$report_id}" enctype="multipart/form-data">
						<div class = "form-group">
							Select and upload XCES file:
						</div>
						<div class = "form-group">
							<input class="btn btn-default" type="file" name="xcesFile" />
							<input type="hidden" name="action" value="report_set_tokens"/>
							<input type="hidden" id="report_id" value="{$row.id}"/>
						</div>
						<div class = "form-group">
							<input class="btn btn-primary" type="submit" value="Submit"/>
						</div>
					</form>
				</div>
			</div>
			*}
			<div class = "panel panel-default">
				<div class = "panel-heading">Using Web Service</div>
				<div class = "panel-body">
					<div class = "panel panel-default">
						<div class="panel-heading">
							<a data-toggle="collapse" href="#token_options">Options</a>
						</div>
						<div class="panel-body panel-collapse collapse" id = "token_options">
							<h4>Polish</h4>
							<div class="radio">
								<label><input type="radio" name="task" id="nlprest2-morphodita"/> Morphodita</label>
							</div>
							<div class="radio">
								<label><input checked type="radio" name="task" id="nlprest2-wcrft2-morfeusz1"/> Wcrft2 (Morfeusz1)</label>
							</div>
							<div class="radio">
								<label><input type="radio" name="task" id="nlprest2-wcrft2-morfeusz2"/> Wcrft2 (Morfeusz2)</label>
							</div>
							<h4>English</h4>
							<div class="radio">
								<label><input type="radio" name="task" id="nlprest2-en"/> spaCy English</label>
							</div>
							<h4>German</h4>
							<div class="radio">
								<label><input type="radio" name="task" id="nlprest2-de"/> spaCy German</label>
							</div>
							<h4>Russian</h4>
							<div class="radio">
								<label><input type="radio" name="task" id="nlprest2-ru"/> UDPipe Russian</label>
							</div>
							<h4>Hebrew</h4>
							<div class="radio">
								<label><input type="radio" name="task" id="nlprest2-he"/> UDPipe Hebrew</label>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class = "form-group">
						<button class="btn btn-primary" id="tokenizeText">Tokenize</button>
					</div>
					<div class = "form-group">
						<div id = "process_status" style = "display: none;">
							<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
							<label for = "status">Status:</label>
							<span id = "status">Queued</span>
						</div>
					</div>
					{if $message}<div id="messageBox">{$message}{/if}</div>
				</div>
			</div>
		</div>
	</div>
</div>
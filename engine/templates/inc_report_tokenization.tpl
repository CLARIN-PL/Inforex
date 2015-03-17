{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<table style="width: 100%">
	<tr>
		<td style="vertical-align: top">
			<div class="column" id="widget_text">
				<div class="ui-widget ui-widget-content ui-corner-all">			
					<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Content</div>
					<div id="content" style="padding: 5px;" class="annotations scrolling">{$content_inline}</div>
					<pre>
					<div id="tmp">
						
					</div>
					</pre>
				</div>
			</div>
		</td>
		<td style="vertical-align: top; width: 400px;">
			<div class="column" id="widget_annotation">
				<div class="ui-widget ui-widget-content ui-corner-all">			                    
					<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Tokenize document</div>
					<div style="padding: 2px;">
						<div class="scrolling" style="overflow: auto">				
						      <h2>From CCL file</h2>
							<form method="POST" action="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage=tokenization&amp;id={$report_id}" enctype="multipart/form-data">
							     Select and upload XCES file:
								<input class="button" type="file" name="xcesFile" />
								<input type="hidden" name="action" value="report_set_tokens"/>
								<input type="hidden" id="report_id" value="{$row.id}"/>
								<input class="button" type="submit" value="Submit"/>
							</form>
							<h2>Using Web Service</h2>
							<button class="button" id="takipiwsProcess">Run WCRFT</button>
							<div id="messageBox" style="border: 1px solid yello; background: " >

								{if $message}
									{$message}
								{/if}
							</div>
						</div>
					</div>
				</div>
			</div>
		</td>
	</tr>
</table>



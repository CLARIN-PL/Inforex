<div id="dialog" title="Błąd" style="display: none;">
	<p>
		<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 50px 0;"></span>
		<span class="message"></span>
	</p>
	<p><i><a href="">Refresh page.</a></i></p>
</div>
 
<table style="width: 100%; margin-top: 5px;">
	<tr>
		<td style="vertical-align: top"> 
			<div class="column" id="widget_text">
				<div class="ui-widget ui-widget-content ui-corner-all">			
				<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Document content:</div>
					<div id="content" style="padding: 5px;" class="annotations scrolling">{$content_inline|format_annotations}</div>
				</div>
			</div>
		</td>
		<td style="width: 280px; vertical-align: top; overflow: auto; ">
			<div id="cell_annotation_wait" style="display: none;width: 280px">
				Trwa wczytywanie danych
				<img src="gfx/ajax.gif" />
			</div>
			
		 	<div id="rightPanelAccordion" class="ui-accordion ui-widget ui-helper-reset">
		 		{if $smarty.cookies.accordionActive=="cell_annotation_layers_header"}
		 		<h3 id="cell_annotation_layers_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
		 			<span class="ui-icon ui-icon-triangle-1-s"></span>
		 			<a tabindex="-1" href="#">Annotation layers</a>
		 		</h3>
				<div style="width: 280px; vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
		 		{else}
		 		<h3 id="cell_annotation_layers_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
		 			<span class="ui-icon ui-icon-triangle-1-e"></span>
		 			<a tabindex="-1" href="#">Annotation layers</a>
		 		</h3>
				<div style="width: 280px; vertical-align: top; padding: 5px; display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
		 		{/if}
					<div id="annotation_layers">
						<table class="tablesorter" cellspacing="1">
							<thead>
							<tr>
								<th>Layer</th>
								<th style="text-align:center" title="Dynamically show/hide layer" >Display</th>
								<th style="text-align:center" title="Physically show/hide layer -- reload page is required to rebuild document structure" >Enable</th>
							</tr>
							</thead>
							<tbody>
						    {foreach from=$annotation_types item=set key=k name=groups}
						    <tr>
						    	<td style="vertical-align: middle"><span class="layerName">{$k}</span></td>
						    	<td style="text-align:center"><input name="layerId{$set.groupid}" type="checkbox" class="hideLayer" /> </td>
						    	<td style="text-align:center"><input name="layerId{$set.groupid}" type="checkbox" class="clearLayer"/></td>
						    </tr>  
						    {/foreach}
						    </tbody>
				    
				    	</table>
			    	</div>		 		
				</div>
		 		{if $smarty.cookies.accordionActive=="cell_annotation_ner_header"}
		 		<h3 id="cell_annotation_ner_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
		 			<span class="ui-icon ui-icon-triangle-1-s"></span>
		 			<a tabindex="-1" href="#">NER module</a>
		 		</h3>
				<div style="width: 280px; vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
				{else}
		 		<h3 id="cell_annotation_ner_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
		 			<span class="ui-icon ui-icon-triangle-1-e"></span>
		 			<a tabindex="-1" href="#">NER module</a>
		 		</h3>
				<div style="width: 280px; vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
				{/if}					
					<div style="padding: 0pt 0.7em; margin: 10px;" class="ui-state-highlight ui-corner-all"> 
						<p style="padding: 10px"><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>
						<strong>Select model</strong>: 
							<select name="model" id="ner-model">
								{foreach from=$models item=item key=key}
									<option value="{$key}" title="{$item.description}">{$item.name}</option>
								{/foreach}
							</select>
					</div>				
					<div id="nerModule">
						<button id="runNerModule">Run</button>	
						<div id="message"></div>
					</div>
				</div>
				
		 		{if $smarty.cookies.accordionActive=="cell_annotation_list_header"}
		 		<h3 id="cell_annotation_list_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
		 			<span class="ui-icon ui-icon-triangle-1-s"></span>
		 			<a tabindex="-1" href="#">Annotation list</a>
		 		</h3>
				<div style="width: 280px; vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
				{else}
		 		<h3 id="cell_annotation_list_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
		 			<span class="ui-icon ui-icon-triangle-1-e"></span>
		 			<a tabindex="-1" href="#">Annotation list</a>
		 		</h3>
				<div style="width: 280px; vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
				{/if}	
					<form method="POST" action="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage=autoextension&amp;id={$report_id}">									
					<div>				
						<div id="annotationList" class="annotations scrolling">
							<var id="annotationsCount">0</var> annotation(s) on disabled/hidden layers
							<br/>
							<b>New annotations:</b>
							<table class="tablesorter" cellspacing="1">
								<thead>
								<tr>
									<th>Type</th>
									<th>Text</th>
									<th>Stage</th>
								</tr>
								</thead>
								<tbody>												
								{foreach from=$anns key=annkey item=ann}		
									{if $ann.stage=="new" && $ann.source=="bootstrapping"}									
									<tr>
										<td>{$ann.type}</td>
										<td>
											<span class="{$ann.type}" title="an#{$ann.id}:{$ann.type}">{$ann.text}</span>
										</td>
										<td>
											<input type="radio" name="annSub[{$ann.id}]" value="accept" checked="checked"/> Accept
											<input type="radio" name="annSub[{$ann.id}]" value="discard"/> Discard
										</td>
									</tr>
									{/if}
								{/foreach}
								</tbody>
							</table>
						</div>
					</div>
					<div>
						<input type="submit" value="Confirm verification" />
						<input type="hidden" name="action" value="report_set_annotations_stage"/>
					</div>
					</form>
					
				</div>
				
								
				<h3 style="display:none"><a>Tmp</a></h3>
				<div style="display:none">
					<div id="report_id">{$report_id}</div>
					<div id="corpus_id">{$row.corpora}</div>
				</div>
				
			</div>
		</td>
	</tr>
</table>


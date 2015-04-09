{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

{if $annotation_sets|@count==0}
    {capture assign=message}
    There are no annotations in this document to verify.
    {/capture}    
    {include file="common_message.tpl"}
{else}

 
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
					<div id="content" style="padding: 5px;" class="annotations scrolling">{$content|format_annotations}</div>
				</div>
			</div>
		</td>
		
		<td style="width: 550px; vertical-align: top; overflow: auto; ">
			<div id="cell_annotation_wait" style="display: none;">
				Trwa wczytywanie danych
				<img src="gfx/ajax.gif" />
			</div>
			
		 	<div id="rightPanelAccordion" class="ui-accordion ui-widget ui-helper-reset">
            {* Perspektywa została dostosowana na szybko do automatycznego rozpoznawania nazw własnych, więc pierwsze dwa panele zostały ukryte. *} 
            {*
		 		{if $smarty.cookies.accordionActive=="cell_annotation_layers_header"}
		 		<h3 id="cell_annotation_layers_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
		 			<span class="ui-icon ui-icon-triangle-1-s"></span>
		 			<a tabindex="-1" href="#">Annotation layers</a>
		 		</h3>
				<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
		 		{else}
		 		<h3 id="cell_annotation_layers_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
		 			<span class="ui-icon ui-icon-triangle-1-e"></span>
		 			<a tabindex="-1" href="#">Annotation layers</a>
		 		</h3>
				<div style="vertical-align: top; padding: 5px; display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
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
				<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
				{else}
		 		<h3 id="cell_annotation_ner_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
		 			<span class="ui-icon ui-icon-triangle-1-e"></span>
		 			<a tabindex="-1" href="#">NER module</a>
		 		</h3>
				<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
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
				*}
				
		 		<h3 id="cell_annotation_list_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
		 			<span class="ui-icon ui-icon-triangle-1-s"></span>
		 			<a tabindex="-1" href="#">Annotations to verify</a>
		 		</h3>
				<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
	                <div id="annotation_sets" style="margin-bottom: 10px">
					    <table class="tablesorter" cellspacing="1">
					        <tr>
					            <th>Annotation set</th>
					            <th>New</th>
					            <th>Final</th>
					            <th>Discarded</th>
					    {foreach from=$annotation_sets item=set}
					        <tr{if $set.annotation_set_id==$annotation_set_id} class="selected"{/if}>
					            <td><a href="?page=report&amp;corpus={$corpus.id}&amp;=autoextension&amp;id={$report.id}&amp;annotation_set_id={$set.annotation_set_id}">{$set.annotation_set_name}</a></td>				            
	                            <td style="width: 50px; text-align: right">{$set.count_new}</td>                            
	                            <td style="width: 50px; text-align: right">{$set.count_final}</td>                            
	                            <td style="width: 50px; text-align: right">{$set.count_discarded}</td>                            
					        </tr>
	                    {/foreach}
	                    </table>
                    </div>
				
					<form method="POST" action="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage=autoextension&amp;id={$report_id}&amp;annotation_set_id={$annotation_set_id}">									
					<div id="annotationList" class="annotations scrolling">
					{*
					{if !$verify}
					   <input type="button" value="Recognize proper names ..." id="recognize"/>
					{/if}
					*}
					
					{if $annotations|@count > 0 }
									
						<div>
							<table class="tablesorter bootstraped-annotations" cellspacing="1">
								<thead>
								<tr>
									<th>Type</th>
									<th style="width: 200px">Text</th>
                                    <th>Later</th>
									<th>Accept</th>
	                                <th>Discard</th>
                                    <th colspan="2">Change&nbsp;to</th>
								</tr>
								</thead>
								<tbody>												
								{foreach from=$annotations item=ann}		
									<tr>
										<td>{$ann.type}</td>
										<td>
											<span class="{$ann.type}" title="an#{$ann.id}:{$ann.type}">{$ann.text}</span>
										</td>
                                        <td style="text-align: center; background: #ccc">
                                            <input type="radio" name="annSub[{$ann.id}]" value="later" checked="checked"/>
                                        </td>
										<td style="text-align: center; background: #A5FF8A">
											<input type="radio" name="annSub[{$ann.id}]" value="accept" />
										</td>
										<td style="text-align: center; background: #FFBBBB">
											<input type="radio" name="annSub[{$ann.id}]" value="discard"/>
										</td>
										<td style="text-align: center; background: lightyellow">
                                            <input type="radio" name="annSub[{$ann.id}]" value="change" style="display: none"/>
					                          <select name="annChange[{$ann.id}]" size="1">
					                               <option value="-">-</option>
					                               {foreach from=$annotation_types[$ann.group_id] item=type}
					                                   <option value="{$type.annotation_type_id}">{$type.name}</option>                               
					                               {/foreach}
					                            </select>    
										</td>
									</tr>
								{/foreach}
								</tbody>
							</table>
							<input type="submit" class="button" value="Save verification" style="width: 99%"/>
							<input type="hidden" name="action" value="report_set_annotations_stage"/>
                        </div>
				    {/if}
					</div>
					</form>
					
				</div>
																
			</div>
		</td>
	</tr>
</table>

{/if}

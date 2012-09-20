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
					<div id="content">
						<div id="leftContent" style="float:left; width: {if $showRight}50%{else}100%{/if}; border-right: 1px solid #E0CFC2" class="annotations scrolling content">
						      <div style="margin: 5px" class="contentBox">{$content_inline|format_annotations}</div>
						</div>
						<div id="rightContent" style="{if !$showRight}display: none{/if};" class="annotations scrolling content rightPanel">
						      <div style="margin: 5px" class="contentBox">{$content_inline2|format_annotations}</div>
						</div>
						<div style="clear:both"></div>
					</div>
				</div>
			</div>
		</td>
		<td style="width: 300px; vertical-align: top; overflow: auto; ">
			<div id="cell_annotation_wait" style="display: none;">
				Trwa wczytywanie danych
				<img src="gfx/ajax.gif" />
			</div>
			<div id="rightPanelAccordion" class="ui-accordion ui-widget ui-helper-reset">
		 		
		 		{include file="inc_report_annotator_configuration.tpl"}
				
                {include file="inc_report_annotator_annotations.tpl"}
				
		 		{if $smarty.cookies.accordionActive=="cell_relation_list_header"}
		 		<h3 id="cell_relation_list_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
		 			<span class="ui-icon ui-icon-triangle-1-s"></span>
		 			<a tabindex="-1" href="#">Relation list</a>
		 		</h3>
				<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
				{else}
		 		<h3 id="cell_relation_list_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
		 			<span class="ui-icon ui-icon-triangle-1-e"></span>
		 			<a tabindex="-1" href="#">Relation list</a>
		 		</h3>
				<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
				{/if}					
					<div id="relationList" class="annotations">
						<table class="tablesorter" cellspacing="1" style="font-size: 8pt">
							<thead>
								<tr>
									<th>Jednostka źródłowa</th>
									<th>Nazwa relacji</th>
									<th>Jednostka docelowa</th>
								</tr>
							</thead>
							<tbody>
							{foreach from=$allrelations item=relation}
								<tr>
									<td sourcegroupid={$relation.source_group_id} sourcesubgroupid={$relation.source_annotation_subset_id}><span class="{$relation.source_type}" title="an#{$relation.source_id}:{$relation.source_type}">{$relation.source_text}</span></td>
									<td class="relation_type_switcher" id="{$relation.id}">{$relation.name}</td>
									<td targetgroupid={$relation.target_group_id} targetsubgroupid={$relation.target_annotation_subset_id}><span class="{$relation.target_type}" title="an#{$relation.target_id}:{$relation.target_type}">{$relation.target_text}</span></td>
								</tr>
							{/foreach}							
							</tbody>
						</table>	
					</div>
				</div>
				
				<h3 style="display:none"><a>Tmp</a></h3>
				<div style="display:none">
					Tmp
				</div>				
			</div>
		</td>
	</tr>
</table>
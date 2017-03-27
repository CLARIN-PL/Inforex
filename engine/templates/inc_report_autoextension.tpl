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

<div id="col-content" class="col-md-8 scrollingWrapper">
	<div class="panel panel-primary">
		<div class="panel-heading">Document content</div>
		<div class="panel-body" style="padding: 0">
			<div class="column" id="widget_text">
				<div id="content" style="padding: 5px;" class="annotations scrolling">{$content|format_annotations}</div>
			</div>
		</div>
	</div>
</div>

<div id="col-configuration" class="col-md-3 scrollingWrapper">
	<div class="panel panel-primary">
		<div class="panel-heading">View configuration</div>
		<div class="panel-body" style="padding: 0">

			<div id="cell_annotation_wait" style="display: none;">
				Trwa wczytywanie danych
				<img src="gfx/ajax.gif" />
			</div>

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
</div>

{/if}

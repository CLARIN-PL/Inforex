{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl" content_class="no_padding"}
<div style="background: #eee; border-bottom: 1px solid #aaa; padding-left: 5px;">
	{*<input type="button" id="export_by_subcorpora" style="float: right" value="Export frequency distribution to a CSV file" class="button"/>*}
	{*<input type="button" id="export_selected" style="float: right" value="Export current frequency list to a CSV file" class="button"/>*}
	<form method="GET" action="index.php">
		<input type="hidden" name="page" value="{$page}"/>
		<input type="hidden" name="corpus" value="{$corpus.id}"/>
		<b>Filters:</b>
		<span style="margin-left: 20px;">Annotation type:</span>
		<select name="annotation_type_id" style="vertical-align: middle;">
			<option value="">all</a>	
		    {foreach from=$annotation_types item=at}
	           	<option value="{$at.annotation_type_id}" {if $at.annotation_type_id==$annotation_type_id}selected="selected"{/if}>{$at.name} ({$at.c})</a>
	    	{/foreach}			    	
		</select>
		
		<span style="margin-left: 20px;">Subcorpus:</span>
		<select name="subcorpus_id" style="vertical-align: middle;">
			<option value="">all</a>	
		    {foreach from=$subcorpora item=s}
	           	<option value="{$s.subcorpus_id}" {if $s.subcorpus_id==$subcorpus_id}selected="selected"{/if}>{$s.name}</a>
	    	{/foreach}			    	
		</select>
		<span style="margin-left: 20px;">Phrase:</span>
		<input type="text" name="phrase" value="{$phrase}"/>
				
		<input type="submit" class="button" value="Apply">
		
	</form>
</div>

<div id="annotation_frequency" style="float: left; width: 400px; padding-left: 5px; padding-bottom: 5px;">
    <h2>Annotation frequency</h2>    
    <div class="flexigrid">
        <table id="annotation_frequency_table">
          <tr>
              <td style="vertical-align: middle"><div>Loading ... </div></td>
          </tr>
        </table>
    </div>
</div>

<div id="annotation_distribution" style="margin-left: 420px; padding-right: 5px">
	<div id="countby">Count: <a href="#" class="active words" type="words">words</a>/<a href="#" class="documents" type="documents">documents</a></div>
	<h2>Annotation distribution across subcorpora</h2>
	<div id="annotations_per_subcorpus">Loading data ... <img src="gfx/ajax.gif" class="ajax_loader" /></div>
</div>

<br style="clear: both; padding-bottom: 5px;"/>
</div>

{include file="inc_footer.tpl"}

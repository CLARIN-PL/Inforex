{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl" content_class="no_padding"}
<div style="background: #eee; border-bottom: 1px solid #aaa; padding-left: 5px;">
	<div style="float: right">
		<input type="button" id="export_by_subcorpora" value="Export frequency distribution to a CSV file" class="button"/>
		<input type="button" id="export_selected" value="Export current frequency list to a CSV file" class="button"/>
	</div>
	
	<form method="GET" action="index.php">
		<input type="hidden" name="page" value="{$page}"/>
		<input type="hidden" name="corpus" value="{$corpus.id}"/>
		<div class="filter">
		<b>Filters:</b>
		</div>
		
		<div class="filter">
		<span>Part of speech:</span>
		<select name="ctag" style="vertical-align: middle;">
			<option value="">all</a>	
		    {foreach from=$classes item=class}
	           	<option value="{$class}" {if $class==$ctag}selected="selected"{/if}>{$class}</a>
	    	{/foreach}			    	
		</select>
		</div>
		
		<div class="filter">
		<span>Subcorpus:</span>
		<select name="subcorpus_id" style="vertical-align: middle;">
			<option value="">all</a>	
		    {foreach from=$subcorpora item=s}
	           	<option value="{$s.subcorpus_id}" {if $s.subcorpus_id==$subcorpus_id}selected="selected"{/if}>{$s.name}</a>
	    	{/foreach}			    	
		</select>
		</div>

		<div class="filter">
		<span style="margin-left: 20px;">Phrase:</span>
		<input type="text" name="phrase" value="{$phrase}"/>
		</div>

		<div class="filter" style="padding: 0">
		<input type="submit" class="button" value="Apply"/>
		</div>
		<div style="clear: both;"></div>
	</form>
</div>

<div id="words_frequency" style="float: left; width: 400px; padding-left: 5px; padding-bottom: 5px;">
    <h2>Words frequency</h2>    
    <div class="flexigrid">
        <table id="words_frequences">
          <tr>
              <td style="vertical-align: middle"><div>Loading ... </div></td>
          </tr>
        </table>
    </div>
</div>

<div id="words_distribution" style="margin-left: 420px; padding-right: 5px">
	<div id="countby">Count: <a href="#" class="active words" type="words">words</a>/<a href="#" class="documents" type="documents">documents</a></div>
	<h2>Words distribution across subcorpora</h2>
	<div id="words_per_subcorpus">There are no words to display</div>
	<div id="chart_link" target="_blank" style="text-align: right"></div>
</div>

<br style="clear: both; padding-bottom: 5px;"/>
</div>

{include file="inc_footer.tpl"}

{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl" content_class="no_padding"}
<div style="background: #eee; border-bottom: 1px solid #aaa; padding-left: 5px;">
	<input type="button" id="export_selected" style="float: right" value="Export current frequency list to a CSV file" class="button"/>
	<form method="GET" action="index.php">
		<input type="hidden" name="page" value="{$page}"/>
		<input type="hidden" name="corpus" value="{$corpus.id}"/>
		<b>Filters:</b>
		<span style="margin-left: 20px;">Part of speech:</span>
		<select name="ctag" style="vertical-align: middle;">
			<option value="">all</a>	
		    {foreach from=$classes item=class}
	           	<option value="{$class}" {if $class==$ctag}selected="selected"{/if}>{$class}</a>
	    	{/foreach}			    	
		</select>
		
		<span style="margin-left: 20px;">Subcorpus:</span>
		<select name="subcorpus_id" style="vertical-align: middle;">
			<option value="">all</a>	
		    {foreach from=$subcorpora item=s}
	           	<option value="{$s.subcorpus_id}" {if $s.subcorpus_id==$subcorpus_id}selected="selected"{/if}>{$s.name}</a>
	    	{/foreach}			    	
		</select>
		<input type="submit" class="button" value="Apply">
		
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
        
	{*<div id="wf_loader">Loading data ... <img src="gfx/ajax.gif" class="ajax_loader" /></div>*}
	
	{*
    <table id="words_frequences" class="tablesorter" cellspacing="1" style="width: 400px">
    <thead>
        <tr>
            <th>No.</th>
            <th>Word</th>
            <th>Count</th>
            <th>Documents</th>
            <th title="% of documents containing the word">Doc.&nbsp;%</th>
            <th title="proportion of documents to word count">Doc./Count</th>
        </tr>
    </thead>
    <tbody>
        
    </tbody>
    </table>    
     <div class="pagging">
    	Pages:
        	<span class="pagedisplay pagging"></span>
        	<input type="hidden" class="pagesize" value="" />
    </div>
    <div style="padding: 10px;display:none;" id="nowords">
    	<i>There are no words for these criteria</i>
    </div>
    *}
</div>

<div id="words_distribution" style="margin-left: 420px; padding-right: 5px">
	<h2>Words distribution across subcorpora</h2>
	<div id="words_per_subcorpus">Loading data ... <img src="gfx/ajax.gif" class="ajax_loader" /></div>
</div>

<br style="clear: both; padding-bottom: 5px;"/>
</div>

{include file="inc_footer.tpl"}

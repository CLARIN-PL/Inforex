{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<input type="hidden" id="subcorpus_id" value="{$subcorpus}"/>

<h1>PCSN statistics</h1>

<div style="float: left; width: 400px;">
{include file="inc_document_filter.tpl"}
</div>

<div style="margin-left: 420px">
	<div id="tabs">
	    <ul style="clear: none; height: 27px">
	        <li><a href="#tabs-2">Znaczniki</a></li>
	        <li><a href="#tabs-3">Błędy</a></li>
	        <li><a href="#tabs-4">Korelacja błędów</a></li>
	        <li><a href="#tabs-5">Interpunkcja</a></li>
	    </ul>
	    <div id="tabs-2">
	        {include file="inc_lps_stats_tags.tpl"}    
	    </div>
	    <div id="tabs-3">
	        {include file="inc_lps_stats_errors.tpl"}    
	    </div>
	    <div id="tabs-4">
	        {include file="inc_lps_stats_errors_matrix.tpl"}    
	    </div>
	    <div id="tabs-5">
	        {include file="inc_lps_stats_interpunction.tpl"}    
	    </div>
	</div>
</div>

<br style="clear: both"/>

{include file="inc_footer.tpl"}
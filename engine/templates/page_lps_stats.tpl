{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}

<input type="hidden" id="subcorpus_id" value="{$subcorpus}"/>

<h1>PCSN statistics</h1>

<div class="row">

	<div class="col-md-9">
		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#menu1">Tags</a></li>
			<li><a data-toggle="tab" href="#menu2">Errors</a></li>
			<li><a data-toggle="tab" href="#menu3">Error correlation</a></li>
			<li><a data-toggle="tab" href="#menu4">Interpunction</a></li>
		</ul>

		<div class="tab-content">
			<div id="menu1" class="tab-pane fade in active">
				{include file="inc_lps_stats_tags.tpl"}
			</div>
			<div id="menu2" class="tab-pane fade">
				{include file="inc_lps_stats_errors.tpl"}
			</div>
			<div id="menu3" class="tab-pane fade">
				{include file="inc_lps_stats_errors_matrix.tpl"}
			</div>
			<div id="menu4" class="tab-pane fade">
				{include file="inc_lps_stats_interpunction.tpl"}
			</div>
		</div>
	</div>

	<div class="col-md-3">
		{include file="inc_document_filter.tpl"}
	</div>

</div>
{include file="inc_footer.tpl"}
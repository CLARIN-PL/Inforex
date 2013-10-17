{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

{if $corpus.public || $user}
	<div id="filter_menu" style="float: right; margin-left: 10px;overflow-x:hidden;overflow-y:auto;">
		{*<h2>Subcorpus filter:</h2>
		<input type="checkbox" name="subcorpuses[]" value="all" /> All
		<div class="scrolling" style="height:100px">		
			<table id="subcorpusTable" class="tablesorter">
				<thead>
					<tr>
						<th>id</th>
						<th>name</th>
						<th>description</th>
						<th>show</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$subcorpuses item=subcorpus}
						<tr>
							<td>{$subcorpus.subcorpus_id}</td>
							<td>{$subcorpus.name}</td>
							<td>{$subcorpus.description}</td>
							<td>
								<input type="checkbox" name="subcorpuses[]" value="{$subcorpus.subcorpus_id}" />
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>	
		</div>		*}
		
		
		<h2>Applied filters:</h2>
	
		{if $filter_order|@count>0}
			{foreach from=$filter_order item=filter_type}
				{include file="inc_filter.tpl"}
			{/foreach}
		{else}		
			<div class="total_count">
				<small><i>filter criteria not set</i></small>
			</div>
		{/if}
	
		{*
		<div>Number of displayed documents: <b>{$rows|@count|number_format:0:".":" "}</b>{if $total_count!=$rows|@count} from <b>{$total_count|number_format:0:".":" "}</b>{/if}</div>
		{if $base_found_sentences}<div>Number of displayed sentences: <b>{$base_found_sentences}</b></div>{/if}
		*}

		<h2>Available filters:</h2>
		{foreach from=$filter_notset item=filter_type}
			{include file="inc_filter.tpl"}
		{/foreach}
	</div>
	
	{*
	{capture name=pagging}
		<div class="pagging">
		Strony:
		{foreach from=$page_map item=page}
			{if $page.nolink}
				<span>{$page.text}</span>
			{else}
		    	<a {if $page.selected} class="active"{/if} href="index.php?page=browse&amp;corpus={$corpus.id}&amp;p={$page.p}">{$page.text}</a>
		    {/if}
		{/foreach}
		</div>
	{/capture}
        *}
        
	<div style="padding-right: 280px">	
		<table id="table-documents"></table>
				<script type="text/javascript">
				
				var colModel = [
				{foreach from=$columns item=c key=k}
					{if preg_match("/^flag/",$k)}
						{literal}{{/literal}display: "{$c.short|lower}", name : "{$k|lower}", width : 40, sortable : true, align: 'center'{literal}}{/literal},
					{else}
						{if preg_match("/found_base_form/", $k)}
								{literal}{{/literal}display: "{$c|lower}", name : "{$k|lower}", width : 200, sortable : true, align: 'center'{literal}}{/literal},	
						{else}

							{if !preg_match("/lp/", $k)}
								{literal}{{/literal}display: "{$c|lower}", name : "{$k|lower}", 
								width: {if !preg_match("/title/", $k) && !preg_match("/tokenization/", $k)}50{else}150{/if}, 
								sortable : true, align: 'center'{literal}}{/literal},
							{/if}
						{/if}

					{/if}						
				{/foreach}
				];
				
				</script>

        <div style="clear: both; margin-bottom: 5px;"></div>
	</div>
{else}
    {include file="inc_no_access.tpl"}
{/if}
{include file="inc_footer.tpl"}
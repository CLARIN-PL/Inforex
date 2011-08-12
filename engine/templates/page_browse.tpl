{include file="inc_header.tpl"}

{if $corpus.public || $user}
	<div id="filter_menu" style="float: right; margin-left: 10px; ">
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
	
		<div>Number of displayed documents: <b>{$total_count}</b></div>
	
		<h2>Available filters:</h2>
		{foreach from=$filter_notset item=filter_type}
			{include file="inc_filter.tpl"}
		{/foreach}
	</div>
	
	{capture name=pagging}
		<div class="pagging">
		Strony:
		{foreach from=$page_map item=page}
			{if $page.nolink}
				<span>{$page.text}</span>
			{else}
		    	<a {if $page.selected} class="active"{/if}href="index.php?page=browse&amp;corpus={$corpus.id}&amp;p={$page.p}">{$page.text}</a>
		    {/if}
		{/foreach}
		</div>
	{/capture}
	
	<div style="padding-right: 280px">	
		{$smarty.capture.pagging}
		
		<table id="table-documents" class="tablesorter" cellspacing="1">
			<thead>
				<tr>
				{foreach from=$columns item=c key=k}
					{if preg_match("/^flag/",$k)}
					<th title="{$c.name}">{$c.short}</th>
					{else}
					<th>{$c}</th>
					{/if}
						
				{/foreach}
				</tr>
			</thead>
			<tbody>
		{foreach from=$rows item=r name=list}
			<tr class="{if $smarty.foreach.list.index%2==0}even{else}odd{/if}">
				{foreach from=$columns item=c key=k}
					{if $k=="lp"}
					<td style="text-align: right">{$smarty.foreach.list.index+$from}.</td>
					{elseif $k=="id"}
					<td style="text-align: right; color: grey"><small>{$r.id}</small></td>
					{elseif $k=="title"}
					<td style="max-width: 200px; overflow: hidden; "><a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$r.id}">{$r.title|default:"<i>brak</i>"}</a></td>
					{elseif $k=="type_name"}
					<td style="{if $r.type==1}color: #777;{/if}; text-align: center;">{$r.type_name|default:"---"|replace:" ":"&nbsp;"}</td>
					{elseif preg_match("/^flag/",$k)}
					<td style="text-align: center;">
						<img src="gfx/flag_{$r.$k.flag_id}.png" title="{$r.$k.name}" style="vertical-align: baseline"/>
					</td>					
                    {elseif $k=="status_name"}
                    <td style="text-align: center;" class="status_{$r.status}">{$r.$k}</td>                    
					{else}					
					<td style="text-align: center;">{$r.$k}</td>					
					{/if}			
				{/foreach}
			</tr>
		{/foreach}
			</tbody>
		</table>
		{$smarty.capture.pagging}
		<div style="clear: both; margin-bottom: 5px;"></div>
	</div>
{else}
    {include file="inc_no_access.tpl"}
{/if}
{include file="inc_footer.tpl"}
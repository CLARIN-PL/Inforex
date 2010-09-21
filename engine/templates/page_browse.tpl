{include file="inc_header.tpl"}

{if $corpus.public || $user}
<table>
	<tr>
		<td style="vertical-align: top; width: 250px">
			<div id="filter_menu">
		 		
				<h2>Applied filters:</h2>
		
				{if $filter_order|@count>0}
					{foreach from=$filter_order item=filter_type}
						{include file="inc_filter.tpl"}
					{/foreach}
				{else}		
					<div class="total_count">
						<small><i>brak ustawionych kryteriów</i></small>
					</div>
				{/if}

				<div>Number of displayed documents:</small> <b>{$total_count}</b></div>
		
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
	
		</td>
		<td style="vertical-align: top">

			{$smarty.capture.pagging}
			
			<table style="width: 100%" class="formated" cellspacing="1">
				<thead>
					<tr style="border: 1px solid #999;">
						<th style="width: 5px;">Lp.</th>
						<th style="width: 20px">Id</th>
						<th style="text-align: left">Nazwa&nbsp;raportu</th>
						<th style="width: 100px">Typ&nbsp;raportu</th>
						<th style="width: 100px">Dodany przez</th>
					</tr>
				</thead>
			{foreach from=$rows item=r name=list}
				<tr class="row_{if ($smarty.foreach.list.index%2==0)}even{else}odd{/if}{if $r.status==2}_ok{/if}{if $r.status==5}_notok{/if}">
					<td style="text-align: right">{$smarty.foreach.list.index+$from}.</td>
					<td style="text-align: right; color: grey">{$r.id}</td>
					<td><a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$r.id}">{$r.title|default:"<i>brak</i>"}</a></td>
					<td style="{if $r.type==1}color: #777;{/if}; text-align: center;">{$r.type_name|default:"---"|replace:" ":"&nbsp;"}</td>
					<td>{$r.screename}</td>
				</tr>
			{/foreach}
			</table>
			{$smarty.capture.pagging}
		</td>
	<tr>
</table>
	
{else}
	<h1>Korpus <i>{$corpus.name}</i> jest korpusem <span style="color: red">prywatnym</span>.</h1>
{/if}

{include file="inc_footer.tpl"}
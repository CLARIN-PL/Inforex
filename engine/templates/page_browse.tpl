{include file="inc_header.tpl"}

<td style="vertical-align: top; background: ; border: 1px solid rgb(68, 68, 68); background: linen; width: 250px">
<div class="filter_menu">
	 		
	<div class="total_count"><small>liczba raportów spełniających kryteria:</small><br/>{$total_count}</div>
	<h2>Kryteria filtrowania:</h2>
	<div class="filter_box">
		<small class="toggle">
			{if $search}
				<a class="cancel" href="index.php?page=browse&amp;search=">anuluj</a>
			{else}
				<a class="toggle" label="#filter_search" href="">pokaż/ukryj</a>
			{/if}
		</small>
		<h2 {if $search}class="active"{/if}>Szukaj <small>w tytule</small></h2>
		<div id="filter_search" class="options" {if !$search}style="display: none"{/if}>
			<form action="index.php?page=browse">
				<input type="text" name="search" value="{$search}" style="width: 150px"/>
				<input type="hidden" name="page" value="browse"/> 
				<input type="submit" value="szukaj"/>
			</form>
		</div>
	</div> 
	
	{if !$IS_RELEASE}
	<div class="filter_box">
		{assign var="is_any_inactive" value="0"}
		{capture name=filter_box}
			<ul>
			{foreach from=$statuses item="status"}
				<li>
					<span class="num">&nbsp;{$status.count}</span>
					<span style="width: 80px; float: left"><a href="index.php?page=browse&amp;status={$status.id}"{if $status.selected} class="selected"{/if}>{$status.name|default:"<i>brak</i>"}</a></span>
					[<a href="index.php?page=browse&amp;status={$status.link}"{if $status.selected} class="selected"{/if}>{if $status.selected}&ndash;{else}+{/if}</a>]					
				</li>
				{if !$status.selected}{assign var="is_any_inactive" value="1"}{/if}
			{/foreach}
			</ul>
		{/capture}
		<small class="toggle">
			{if $is_any_inactive}
				<a class="cancel" href="index.php?page=browse&amp;status=">anuluj</a>
			{else}
				<a class="toggle" label="#filter_status" href="">pokaż/ukryj</a>
			{/if}
		</small>
		<h2 {if $is_any_inactive}class="active"{/if}>Status</h2>
		<div id="filter_status" {if !$is_any_inactive}style="display: none"{/if}> 
		{$smarty.capture.filter_box}
		</div>
	</div>
	{/if}
		
	<div class="filter_box">
		{assign var="is_any_inactive" value="0"}
		{capture name=filter_box}
			<ul>
			{foreach from=$types item="type"}
				<li>
					<span class="num">&nbsp;{$type.count}</span>
					<a href="index.php?page=browse&amp;type={$type.link}"{if $type.selected} class="selected"{/if}>{$type.name|default:"<i>brak</i>"}</a>
				</li>
				{if !$type.selected}{assign var="is_any_inactive" value="1"}{/if}
			{/foreach}
			</ul>
		{/capture}
		<small class="toggle">
			{if $type_set}
				<a class="cancel" href="index.php?page=browse&amp;type=">anuluj</a>
			{else}
				<a class="toggle" label="#filter_type" href="">pokaż/ukryj</a>
			{/if}
		</small>
		<h2>Typ zdarzenia</h2>
		<div id="filter_type" {if !$type_set}style="display: none"{/if}>
		{$smarty.capture.filter_box}
		</div>
	</div>
	
	{if !$IS_RELEASE}
	<div class="filter_box">
		{assign var="is_any_active" value="0"}
		{capture name="filter_box"}
			<ul>
			{foreach from=$years item="year"}
				<li>
					<span class="num">&nbsp;{$year.count}</span>
					<span style="width: 30px; text-align: right; display: block; float: left; margin-right: 5px"><a href="index.php?page=browse&amp;year={$year.year}"{if $year.selected} class="selected"{/if}>{$year.year}</a></span>
					[<a href="index.php?page=browse&amp;year={$year.link}"{if $year.selected} class="selected"{/if}>{if $year.selected}&ndash;{else}+{/if}</a>]
					</li>
				{if !$year.selected}{assign var="is_any_inactive" value="1"}{/if}
			{/foreach}
			</ul>		
		{/capture}		
		<small class="toggle">
			{if $is_any_inactive}
				<a class="cancel" href="index.php?page=browse&amp;year=">anuluj</a>
			{else}
				<a class="toggle" label="#filter_year" href="">pokaż/ukryj</a>
			{/if}
		</small>
		<h2 {if $is_any_inactive}class="active"{/if}>Rok</h2>
		<div id="filter_year" {if !$is_any_inactive}style="display: none"{/if}>
		{$smarty.capture.filter_box}
		</div>
	</div>
	
	<div class="filter_box">
		{assign var="is_any_inactive" value="0"}
		{capture name="filter_box"}
			<ul>
			{foreach from=$months item="month"}
				<li>
					<span class="num">&nbsp;{$month.count}</span>
					<span style="width: 30px; text-align: right; display: block; float: left; margin-right: 5px"><a href="index.php?page=browse&amp;month={$month.month}"{if $month.selected} class="selected"{/if}>{$month.month}</a></span>
					[<a href="index.php?page=browse&amp;month={$month.link}"{if $month.selected} class="selected"{/if}>{if $month.selected}&ndash;{else}+{/if}</a>]
				</li>
				{if !$month.selected}{assign var="is_any_inactive" value="1"}{/if}
			{/foreach}
			</ul>		
		{/capture}
		<small class="toggle">
			{if $is_any_inactive}
				<a class="cancel" href="index.php?page=browse&amp;month=">anuluj</a>
			{else}
				<a class="toggle" label="#filter_month" href="">pokaż/ukryj</a>
			{/if}
		</small>
		<h2>Miesiąc</h2>
		<div id="filter_month" {if !$is_any_inactive}style="display: none"{/if}>
		{$smarty.capture.filter_box}
		</div>
	</div>
	
	<div class="filter_box">	
		{capture name="filter_box"}
			<ul>
			{foreach from=$annotations item="annotation"}
				<li>
					<span class="num">&nbsp;{$annotation.count}</span>
					<a href="index.php?page=browse&amp;annotation={$annotation.link}"{if $annotation.selected} class="selected"{/if}>{$annotation.name}</a>
				</li>
			{/foreach}
			</ul>		
		{/capture}
		<small class="toggle">
			{if $annotation_set}
				<a class="cancel" href="index.php?page=browse&amp;annotation=">anuluj</a>
			{else}
				<a class="toggle" label="#filter_annotation" href="">pokaż/ukryj</a>
			{/if}
		</small>
		<h2 {if $annotation_set}class="active"{/if}>Adnotacje</h2>
		<div id="filter_annotation" {if !$annotation_set}style="display: none"{/if}>
		{$smarty.capture.filter_box}
		</div>
	</div>

	<div class="filter_box">
		<small class="toggle"><a href="">pokaż/ukryj</a></small>
		<h2>Treść</h2>
		<ul>
		{foreach from=$content item="content"}
			<li>
				<span class="num">&nbsp;{$content.count}</span>
				<a href="index.php?page=browse&amp;content={$content.link}"{if $content.selected} class="selected"{/if}>{$content.name}</a>
			</li>
		{/foreach}
		</ul>
	</div>
{/if}
</div>

</td>

<td class="table_cell_content">

<table style="width: 100%">
	<tr style="border: 1px solid #999;">
		<th>Lp.</th>
		<th>Id</th>
		<th>Nazwa&nbsp;raportu</th>
		<th>Typ&nbsp;raportu</th>
		{* <th>Status</th> *}
		{* <th colspan="2"> </th>*}
	</tr>
{foreach from=$rows item=r name=list}
	<tr class="row_{if ($smarty.foreach.list.index%2==0)}even{else}odd{/if}{if $r.status==2}_ok{/if}">
		<td style="text-align: right">{$smarty.foreach.list.index+$from}.</td>
		<td style="text-align: right"><b>{$r.id}</b></td>
		<td><a href="index.php?page=report&amp;id={$r.id}">{$r.title}</a></td>
		<td style="{if $r.type==1}color: #777;{/if}; text-align: center;">{$r.type_name|default:"---"|replace:" ":"&nbsp;"}</td>
		{* <td style="{if $r.status==1}color: #777;{/if}; text-align: center;">{($r.status_name|default:"---")}</td> *}
		{*
		<td>{if $r.status==2}<div style="width: 10px; height: 10px; background: #3366FF"> </div>
			{else}<div style="width: 10px; height: 10px; background: #ddd"> </div>{/if}</td>
		<td>{if $r.formated==1}<div style="width: 10px; height: 10px; background: orange"> </div>
			{else}<div style="width: 10px; height: 10px; background: #ddd"> </div>{/if}</td>
		*}
	</tr>
{/foreach}
</table>

<hr/>

<div id="pagging">
Liczba raportów: <b>{$total_count}</b>, Strony:
{section name=foo loop=$pages}
    <a {if $p==$smarty.section.foo.iteration-1} class="active"{/if}href="index.php?page=browse&amp;p={$smarty.section.foo.iteration-1}">{$smarty.section.foo.iteration}</a>
{/section}
</div>

{include file="inc_footer.tpl"}
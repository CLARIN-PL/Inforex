{if $filter_type == "text"}
	<div class="filter_box">
		{if $search}
			<a class="cancel" href="index.php?page=browse&amp;corpus={$corpus.id}&amp;search="><small class="toggle">cancel</small>
		{else}
			<a class="toggle_simple" label="#filter_search" href=""><small class="toggle">show/hide</small>
		{/if}
			<h2 {if $search}class="active"{/if}>Search text</h2>
		</a>
		<div id="filter_search" class="options" {if !$search}style="display: none"{/if}>
			<form action="index.php?page=browse">
				<input type="hidden" name="corpus" value="{$corpus.id}"/>
				<input type="checkbox" name="search_field[]" value="title" style="vertical-align: middle" {if $search_field_title}checked="checked"{/if}> in title,
				<input type="checkbox" name="search_field[]" value="content" style="vertical-align: middle" {if $search_field_content || !$search_field_title}checked="checked"{/if}> in content<br/>				
				<input type="text" name="search" value="{$search|escape:'html'}" style="width: 150px"/>
				<input type="hidden" name="page" value="browse"/> 
				<input type="submit" value="search"/>
			</form>
		</div>
	</div>
{/if} 

{if $filter_type == "base"}
    <div class="filter_box">
        {if $base}
            <a class="cancel" href="index.php?page=browse&amp;corpus={$corpus.id}&amp;base="><small class="toggle">cancel</small>
        {else}
            <a class="toggle_simple" label="#filter_base" href=""><small class="toggle">show/hide</small>
        {/if}
            <h2 {if $base}class="active"{/if}>Base form</h2>
        </a>
        <div id="filter_base" class="options" {if !$base}style="display: none"{/if}>
            <form action="index.php?page=browse">
                <input type="hidden" name="corpus" value="{$corpus.id}"/>
                <input type="text" name="base" value="{$base|escape:'html'}" style="width: 150px"/>
                <input type="hidden" name="page" value="browse"/> 
                <input type="submit" value="search"/>
            </form>
        </div>
    </div>
{/if} 


{if $filter_type == "status"}
	{assign var="attribute_options" value=$statuses}
	{include file="inc_filter_attribute.tpl"}
{/if}
	
{if $filter_type == "type"}
	{assign var="attribute_options" value=$types}
	{include file="inc_filter_attribute.tpl"}
{/if}

{if $filter_type == "year"}
	{assign var="attribute_options" value=$years}
	{include file="inc_filter_attribute.tpl"}
{/if}

{if $filter_type == "annotation"}
	{assign var="attribute_options" value=$annotations}
	{include file="inc_filter_attribute.tpl"}
{/if}

{if $filter_type == "subcorpus"}
	{assign var="attribute_options" value=$subcorpuses}
	{include file="inc_filter_attribute.tpl"}
{/if}

{if $filter_type == "flag"}
	{foreach from=$flag key=key1 item=value1}
		<div class="filter_box">
			{if $is_any_inactive}
				<a class="cancel" href="index.php?page=browse&amp;corpus={$corpus.id}&amp;{$filter_type}="><small class="toggle">cancel</small>
			{else}
				<a class="toggle_simple" label="#filter_{$filter_type}" href=""><small class="toggle">show/hide</small>
			{/if}
				<h2 {if $is_any_inactive}class="active"{/if}>{$key1}</h2>
			</a>
			<ul>
			{foreach from=$value1 key=key2 item=row}
				<li{if $row.selected} class="active"{/if}>
					<span class="num">&nbsp;{$row.count}</span>
					[<a href="index.php?page=browse&amp;corpus={$corpus.id}&amp;{$filter_type}={$row.link}">{if $row.selected}&ndash;{else}+{/if}</a>]					
					<a href="index.php?page=browse&amp;corpus={$corpus.id}&amp;{$filter_type}={$row.id}&amp;filter_order={$row.filter_order}">{$row.name|default:"<i>brak</i>"}</a>
				</li>
				{if !$row.selected}{assign var="is_any_inactive" value="1"}{/if}				
			{/foreach}
			</ul>
		</div>
	{/foreach}
{/if}
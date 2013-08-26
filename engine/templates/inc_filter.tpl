{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{if $filter_type == "text" }   
	<div class="filter_box">
		{if $search}
			<a class="cancel" href="index.php?page=browse&amp;corpus={$corpus.id}&amp;search="><small class="toggle">cancel</small>
		{else}
			<a class="toggle_simple" label="#filter_search" href=""><small class="toggle">show/hide</small>
		{/if}
			<h2 {if $search}class="active"{/if}>Search text</h2>
		</a>
		<div id="filter_search" need_order_and_results_limit="1" class="options" {if !$search}style="display: none"{/if}>
			<form action="index.php?page=browse">
				<input type="hidden" name="corpus" value="{$corpus.id}"/>
				<input type="checkbox" name="search_field[]" value="title" style="vertical-align: middle" {if $search_field_title}checked="checked"{/if}> in title,
				<input type="checkbox" name="search_field[]" value="content" style="vertical-align: middle" {if $search_field_content || !$search_field_title}checked="checked"{/if}> in content<br/>				
				<input type="text" name="search" value="{$search|escape:'html'}" style="width: 150px"/>
				<input type="hidden" name="page" value="browse"/> 
				<input type="submit" value="search"/>
                                <div style="border: none; display: none;">
                                    <input type="checkbox" id="filter_search_random_order" name="random_order" value="1" style="vertical-align: middle" {if $random_order == 1}checked="checked"{/if}> <label for="filter_search_random_order">random order</label><br>
                                    Show {html_options name=results_limit options=$results_limit_options selected=$default_results_limit_for_search_in_text} results.
                                </div>
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
        <div id="filter_base" need_order_and_results_limit="1" class="options" {if !$base}style="display: none"{/if}>
            <form action="index.php?page=browse">
                <input type="hidden" name="corpus" value="{$corpus.id}"/>
                <input type="text" name="base" value="{$base|escape:'html'}" style="width: 150px"/>
                <input type="hidden" name="page" value="browse"/> 
                <input type="submit" value="search"/>
                <div style="border: none; display: none;">
                    <input type="checkbox" id="filter_base_random_order" name="random_order" value="1" style="vertical-align: middle" {if $random_order == 1}checked="checked"{/if}> <label for="filter_search_random_order">random order</label><br>
                    Show {html_options name=results_limit options=$results_limit_options selected=$default_results_limit_for_search_in_text} results.
                </div>
            </form>
        </div>
    </div>
{/if} 

{if $filter_type == "order_and_results_limit"}
    <div class="filter_box">
        {if ($results_limit < $max_results_limit || $random_order)}
            <a class="cancel" href="index.php?page=browse&amp;corpus={$corpus.id}&amp;results_limit={$max_results_limit}&amp;random_order=0"><small class="toggle">cancel</small>
        {else}
            <a class="toggle_simple" label="#filter_order_and_results_limit" href=""><small class="toggle">show/hide</small>
        {/if}
            <h2 {if ($results_limit < $max_results_limit || $random_order)}class="active"{/if}>Ordering and limit of results</h2>
        </a>
        <div id="filter_order_and_results_limit" class="options" {if !(($results_limit < $max_results_limit || $random_order))}style="display: none"{/if}>
            <form action="index.php?page=browse">
                <input type="hidden" name="corpus" value="{$corpus.id}"/>
                <input type="hidden" name="page" value="browse"/> 
                <input type="checkbox" id="filter_base_random_order" name="random_order" value="1" style="vertical-align: middle" {if $random_order == 1}checked="checked"{/if}> <label for="filter_search_random_order">random order</label><br>
                Show {html_options name=results_limit options=$results_limit_options selected=$results_limit} results.
                <input type="submit" value="search"/>
            </form>
        </div>
    </div>
{/if} 

{if $filter_type == "annotation_value"}
    <div class="filter_box">
    	{if $annotation_value}
			<a class="cancel" href="index.php?page=browse&amp;corpus={$corpus.id}&amp;annotation_value="><small class="toggle">cancel</small>
		{else}
			<a class="toggle_simple" label="#filter_ann_val" href=""><small class="toggle">show/hide</small>
		{/if}
			<h2 {if $annotation_value}class="active"{/if}>Annotation value</h2>
		</a>
        <div id="filter_ann_val" class="options" {if !$annotation_value}style="display: none"{/if}>
            <form action="index.php?page=browse">
            	<select name="annotation_type">
            		{if empty($annotation_type)}
            		<option value="" disabled selected>Select annotation</option>
            		{/if}
            		{foreach from=$annotation_types item="set"}
            			<optgroup label="{$set.name}">
            			{foreach from=$set item="subset" key="ssid"}
            			{if $ssid != "name"}
            				<optgroup label="   {$subset.name}">
    							{foreach from=$subset item="type" key="type_name"}
            					{if $type_name != "name"}
            						<option value="{$type}" {if $annotation_type == $type}selected="selected"{/if}>{$type}</option>
            					{/if}
            					{/foreach}
            				</optgroup>
            			{/if}
            			{/foreach}
            			</optgroup>
            		{/foreach}
            	</select>
                <input type="hidden" name="corpus" value="{$corpus.id}"/>
                <input type="text" name="annotation_value" value="{$annotation_value|escape:'html'}" style="width: 150px"/>
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
 
 
{if preg_match("/^flag_/",$filter_type)}
	{assign var="attribute_options" value=$corpus_flags.$filter_type}
	{include file="inc_filter_flag_attribute.tpl"}
{/if}
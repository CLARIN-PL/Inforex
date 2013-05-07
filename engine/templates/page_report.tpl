{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

{if $corpus.public || $user}

    {if $invalid_report_id}
	<div style="background: #E03D19; padding: 1px; margin: 10px; ">
	    <div style="background: #FFF194; padding: 5px; color: #733B0E; font-size: 16px; font-weight: bold;"> 
	       <img src="gfx/image-missing.png" title="No access" style="vertical-align: middle"/> Document does not exist. Go back to <a href="index.php?page=browse&amp;corpus={$corpus.id}">list of documents</a>
	   </div>
	</div>
	{elseif $page_permission_denied}
    <div style="background: #E03D19; padding: 1px; margin: 10px; ">
        <div style="background: #FFF194; padding: 5px; color: #733B0E; font-size: 16px; font-weight: bold;"> 
           <img src="gfx/lock.png" title="No access" style="vertical-align: middle"/> {$page_permission_denied}
       </div>
    </div>	        
    {else}
	<div style="text-align: center" class="pagging">
		<span title="Liczba raportów znajdujących się przed aktualnym raportem"> ({$row_prev_c}) </span>	 
		{if $row_first}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_first}">|< pierwszy</a>{else}<span class="inactive">|< pierwszy</span>{/if} ,
		{if $row_prev_100}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_prev_100}">-100</a>{else}<span class="inactive">-100</span>{/if} ,
		{if $row_prev_10}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_prev_10}">-10</a> {else}<span class="inactive">-10</span>{/if} ,
		{if $row_prev}<a id="article_prev" href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_prev}">< poprzedni</a>{else}<span class="inactive">< poprzedni</span>{/if}
		| <b>{$row_number}</b> z <b>{$row_prev_c+$row_next_c+1}</b> |
		{if $row_next}<a id="article_next" href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_next}">następny ></a>{else}<span class="inactive">następny ></span>{/if} ,
		{if $row_next_10}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_next_10}">+10</a> {else}<span class="inactive">+10</span>{/if} ,
		{if $row_next_100}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_next_100}">+100</a>{else}<span class="inactive">+100</span>{/if} ,
		{if $row_last}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_last}">ostatni >|</a>{else}<span class="inactive">ostatni >|</span>{/if}
		<span title"Liczba raportów znajdujących się po aktualnym raporcie">({$row_next_c})</span>
	</div>
	
	<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all" style="background: #f3f3f3; margin-bottom: 5px; position: relative">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			{foreach from=$subpages item="s"}
			<li class="ui-state-default ui-corner-top {if $subpage==$s->id}ui-state-active ui-tabs-selected{/if}">
				<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage={$s->id}&amp;id={$row.id}">{$s->title}</a></li>		
			{/foreach}
		</ul>
	
		<div>
			{include file="$subpage_file"}	
		</div>
	
		{include file="inc_system_messages.tpl"}
	
	</div>
	{/if}
{else}
    {include file="inc_no_access.tpl"}
{/if}
{include file="inc_footer.tpl"}
{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

{if $corpus.public || $user}
<table style="width: 100%">
<tr>
<td style="vertical-align: top">
            
    <div class="flexigrid">
        <table id="table-documents">
          <tr>
              <td style="vertical-align: middle"><div>Loading ... <img style="vertical-align: baseline" title="" src="gfx/flag_4.png"></div></td>
          </tr>
        </table>
        <script type="text/javascript">
        
        var init_from = {$from};

        var colModel = [
        {foreach from=$columns item=c key=k}
            {if preg_match("/^flag/",$k)}
                {literal}{{/literal}display: "{$c.short}", name : "{$k|lower}", width : 40, sortable : true, align: 'center'{literal}}{/literal},
            {elseif preg_match("/found_base_form/", $k)}
                {literal}{{/literal}display: "{$c}", name : "{$k|lower}", width : 200, sortable : true, align: 'center'{literal}}{/literal},
            {elseif $c=="Subcorpus"}
                {literal}{{/literal}display: "{$c}", name : "{$k|lower}", width : 100, sortable : true, align: 'left'{literal}}{/literal},
            {else}
                {if !preg_match("/lp/", $k)}
                    {literal}{{/literal}display: "{$c}", name : "{$k|lower}", 

                    {if preg_match("/title/", $k)}
                        width: 50, align: 'left',
                    {elseif preg_match("/tokenization/", $k)}
                        width: 150, align: 'center',
                    {elseif preg_match("/suicide_place/", $k)}
                        width: 120, align: 'center',
                    {elseif preg_match("/source/", $k)}
                        width: 60, align: 'center',
                    {else}
                        width: 50, align: 'center',
                    {/if}
                    
                    sortable : true{literal}}{/literal},
                {/if}

            {/if}                       
        {/foreach}
        ];      
        </script>
    </div>
</td>
<td style="width: 270px; vertical-align: top; padding-left: 10px; ">    
	<div id="filter_menu" style="overflow-y:auto;">
		
		{if $filter_order|@count>0}
            <h2 style="margin-top: 0">Applied filters:</h2>
            <div style="margin-bottom: 10px"> 
			{foreach from=$filter_order item=filter_type}
				{include file="inc_filter.tpl"}
			{/foreach}
			</div>
		{/if}
	
		<h2 style="margin-top: 0">Available filters:</h2>
		{foreach from=$filter_notset item=filter_type}
			{include file="inc_filter.tpl"}
		{/foreach}
	</div>
</td>
</tr>
</table>
{else}
    {include file="inc_no_access.tpl"}
{/if}
{include file="inc_footer.tpl"}
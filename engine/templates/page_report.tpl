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
        	
    <table style="width: 100%">
    <tr>
    <td style="vertical-align: top">                	        	
		<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all" style="background: white; margin-bottom: 5px; position: relative; border: 1px solid #667a55">
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
	</td>
    <td style="width: 100px; vertical-align: top;">
    <div id="flagsContainer" style="border: 1px solid #667a55; padding: 2px; background: white;">
        <div id="flagStates" style="display:none; width: 200px">
            <div>
                <b>New state:</b>
                <ul id="list_of_flags">
                {foreach from="$flags" item=flag}
                   <li>
                      <span class="flagState" flag_id="{$flag.id}" title="{$flag.name}" style="cursor:pointer">
                        <img src="gfx/flag_{$flag.id}.png"/> {$flag.name}                               
                      </span>
                   </li>
                {/foreach}
                </ul>
            </div>
        </div>          
        <b>Flags </b>:<br/>
        {if $corporaflags|@count==0}
            <i>no flags</i>
        {else}
	        {foreach from=$corporaflags item=corporaflag}
	            <span 
	                class="corporaFlag" 
	                cflag_id="{$corporaflag.id}" 
	                report_id="{$row.id}"  
	                style="padding: 0px 2px 0px 2px; cursor:pointer; overflow: hidden; width: 90px; display: block; white-space: nowrap"
	                title="{$corporaflag.name}: {if $corporaflag.flag_id}{$corporaflag.fname}{else}NIE GOTOWY{/if}">
	                   <img src="gfx/flag_{if $corporaflag.flag_id}{$corporaflag.flag_id}{else}-1{/if}.png" style="padding-top: 1px"/>
	                   <span style="font-size: 8x; padding: 2px 0;">{$corporaflag.short}</span>
	            </span>
	        {/foreach}
	    {/if}     
        {if "delete_documents"|has_corpus_role_or_owner}
            <hr/>
            <b>Options: </b><br/>
                <span class="optionsDocument" report_id="{$row.id}" style="padding: 0px 2px 0px 2px; cursor:pointer" title="Delete document" corpus={$corpus.id}>
                    <span style="font-size: 12px; padding: 2px 0; color: red;">delete</span>                       
                </span>
        {/if}   
             
    </div>  
    </td>
	</tr>
	</table>
	{/if}
{else}
    {include file="inc_no_access.tpl"}
{/if}
{include file="inc_footer.tpl"}
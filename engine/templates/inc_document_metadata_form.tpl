{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<h1>{$button_text}</h1>
 
<form method="POST">
	{if $add_content}
		<div style="float: left; border-right-width: 5px; border-right-style: solid; border-right-color: #FFFFFF; overflow-x: auto; overflow-y: auto; height: 417px; width: 50%; ">
	{/if}
    <h2>General metadata</h2>
    <table class="tablesorter" cellspacing="1">
        <tr>
            <th style="width: 100px">Title</th>
            <td><input type="text" name="title" style="width: 99%" value="{$row.title}" tabindex="0" /></td>
        </tr>
        <tr>
            <th style="width: 100px">Author</th>
            <td><input type="text" name="author" style="width: 99%" value="{$row.author}" tabindex="0" /></td>
        </tr>
        <tr>
            <th style="width: 100px">Source</th>
            <td><input type="text" name="source" style="width: 99%" value="{$row.source}"/></td>
        </tr>
        <tr>
            <th style="width: 100px">Subcorpus</th>
            <td>
                <select name="subcorpus_id">
                {foreach from=$subcorpora item=sub}
                    <option value="{$sub.subcorpus_id}" {if $sub.subcorpus_id==$row.subcorpus_id}selected="selected"{/if}>{$sub.name}</option>
                {/foreach}
                </select>
            </td>
        </tr>
        <tr>
            <th style="width: 100px">Status</th>
            <td>
                <select name="status">
                {foreach from=$statuses item=status}
                    <option value="{$status.id}" {if $status.id==$row.status}selected="selected"{/if}>{$status.status}</option>
                {/foreach}
                </select>
            </td>
        </tr>
        <tr>
            <th style="width: 100px">Date</th>
            <td>
                <input type="text" name="date" style="width: 100px" value="{$row.date}"/>
                <br/><span style="color: green">released, published or created</span>                
            </td>
        </tr>
        <tr>
            <th style="width: 100px">Format</th>
            <td>
                <select name="format">
                {foreach from=$formats item=format}
                    <option value="{$format.id}" {if $format.id==$row.format_id}selected="selected"{/if}>{$format.format}</option>
                {/foreach}
                </select>
            </td>
        </tr>
    </table>
    
    <h2>Custom metadata</h2>
    <table class="tablesorter" cellspacing="1">
        {foreach from=$features item=f}
        {if $f.value}
        	{assign var="value" value=$f.value}
        {else}
        	{assign var="value" value=$metadata_values[$f.field]}
        {/if}
        <tr>
            <th style="width: 100px; vertical-align: top">{$f.field}</th>
            <td>
               {if $f.field_type == "enum"}
                <select name="ext_{$f.field}">
                    {foreach from=$f.field_values item=v}
                    	<option value="{$v}" {if $v==$value}selected="selected"{/if}>{$v}</option>
                    {/foreach}
                </select>                  
               {else}
                   <input type="text" name="ext_{$f.field}" style="width: 99%" value="{$value}"/>
               {/if}
               {if $f.comment}
                <br/><span style="color: green">{$f.comment}</span>
               {/if}
            </td>
        </tr>
        {/foreach}
    </table>
    
    <hr/>
    
    {if $add_content}
    </div>
    <div style="overflow-x: auto; overflow-y: auto; height: 417px; ">
    <h2>Content</h2>
        <div style="border-top: 1px solid black; border-bottom: 1px solid black; background: white;" id="add_content">
			<textarea name="content" id="{$add_content}">{$row.content}</textarea>
		</div>
	</div>
    {/if}
    
    <input type="submit" value="{$button_text}" class="button"/>
    <input type="hidden" name="action" value="{$action}"/>
    <input id="report_id" type="hidden" name="report_id" value="{$row.id}">
</form>

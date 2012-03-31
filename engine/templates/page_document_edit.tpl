{include file="inc_header.tpl"}

<form method="POST">
    <h1>General metadata</h1>
	<table class="tablesorter" cellspacing="1">
	    <tr>
	        <th style="width: 100px">Title</th>
	        <td><input type="text" name="title" style="width: 99%" value="{$f.value}"/></td>
	    </tr>
	    <tr>
	        <th style="width: 100px">Source</th>
	        <td><input type="text" name="source" style="width: 99%" value="{$f.value}"/></td>
	    </tr>
	    <tr>
	        <th style="width: 100px">Subcorpus</th>
	        <td>
	            <select name="subcorpus_id">
	            {foreach from=$subcorpora item=sub}
	                <option value="{$sub.subcorpus_id}">{$sub.name}</option>
	            {/foreach}
	            </select>
	        </td>
	    </tr>
        <tr>
            <th style="width: 100px">Status</th>
            <td>
                <select name="status">
                {foreach from=$statuses item=status}
                    <option value="{$status.id}" {if $status.id==2}selected="selected"{/if}>{$status.status}</option>
                {/foreach}
                </select>
            </td>
        </tr>
	    <tr>
	        <th style="width: 100px">Date</th>
	        <td><input type="text" name="date" style="width: 100px" value="{$date}"/></td>
	    </tr>
	</table>
	
	<h1>Custom metadata</h1>
	<table class="tablesorter" cellspacing="1">
	    {foreach from=$features item=f}
	    <tr>
	        <th style="width: 100px">{$f.field}</th>
	        <td>
	           {if $f.field_type == "enum"}
                <select name="ext_{$f.field}">
	                {foreach from=$f.field_values item=v}
	                    <option value="{$v}">{$v}</option>
	                {/foreach}
                </select>	               
	           {else}
	               <input type="text" name="ext_{$f.field}" style="width: 99%" value="{$f.value}"/>
	           {/if}
	        </td>
	    </tr>
	    {/foreach}
	</table>
	
	<input type="submit" value="Create new document" style="margin: 5px; padding: 5px 15px"/>
	<input type="hidden" name="action" value="document_add"/>
	<input id="report_id" type="hidden" name="report_id" value="{$row.id}">
</form>

{include file="inc_footer.tpl"}

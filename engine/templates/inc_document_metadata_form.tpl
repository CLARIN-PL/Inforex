<form method="POST">
    <h1>General metadata</h1>
    <table class="tablesorter" cellspacing="1">
        <tr>
            <th style="width: 100px">Title</th>
            <td><input type="text" name="title" style="width: 99%" value="{$row.title}" tabindex="0" /></td>
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
            <td><input type="text" name="date" style="width: 100px" value="{$row.date}"/></td>
        </tr>
    </table>
    
    <h1>Custom metadata</h1>
    <table class="tablesorter" cellspacing="1">
        {foreach from=$features item=f}
        <tr>
            <th style="width: 100px; vertical-align: top">{$f.field}</th>
            <td>
               {if $f.field_type == "enum"}
                <select name="ext_{$f.field}">
                    {foreach from=$f.field_values item=v}
                        <option value="{$v}" {if $v==$f.value}selected="selected"{/if}>{$v}</option>
                    {/foreach}
                </select>                  
               {else}
                   <input type="text" name="ext_{$f.field}" style="width: 99%" value="{$f.value}"/>
               {/if}
               {if $f.comment}
                <br/><span style="color: green">{$f.comment}</span>
               {/if}
            </td>
        </tr>
        {/foreach}
    </table>
    
    <hr/>
    
    <input type="submit" value="{$button_text}" style="margin: 10px; margin-left: 120px; padding: 5px 15px"/>
    <input type="hidden" name="action" value="{$action}"/>
    <input id="report_id" type="hidden" name="report_id" value="{$row.id}">
</form>

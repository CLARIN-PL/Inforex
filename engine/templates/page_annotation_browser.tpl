{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<td class="table_cell_content">

<div id="annotation_types" style="width: 250px; float: left; height: 500px; overflow: auto; ">
<table class="tablesorter" cellspacing="1">
{foreach from=$annotation_types item=type}
<tr{if $type.annotation_type_id==$annotation_type_id} class="selected"{/if}>
    <td><a href="index.php?corpus={$corpus.id}&amp;page=annotation_browser&amp;annotation_type_id={$type.annotation_type_id}">{$type.name}</a></td>
    <td style="text-align: right">{$type.count}</td>
</tr>
{/foreach}
</table>
</div>

<div style="margin-left: 260px; width: 1100px;">
    <div class="flexigrid">
        <table id="table-annotations">
          <tr>
              <td style="vertical-align: middle"><div>Loading ... <img style="vertical-align: baseline" title="" src="gfx/flag_4.png"></div></td>
          </tr>
        </table>
    </div>

</div>

{include file="inc_footer.tpl"}
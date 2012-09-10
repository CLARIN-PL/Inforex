{include file="inc_header.tpl"}

<td class="table_cell_content">

<h1>Annotations statistics</h1>

<h2>Filter</h2>

<table class="tablesorter" cellspacing="1">
    <tr>
        <th style="width: 100px">Subcorpus:</th>
        <td>
        {assign var=subcorpus_set  value=0}
        {foreach from=$subcorpora item=s}
            {if $s.subcorpus_id==$subcorpus} 
                {assign var=subcorpus_set value=1}
                <em>{$s.name}</em>
            {else}
                <a href="index.php?page=annmap&amp;corpus={$corpus.id}&amp;subcorpus={$s.subcorpus_id}">{$s.name}</a>
            {/if},                
        {/foreach}
        {if $subcorpus_set==0}
            <em>wszystkie</em>
        {else}
            <a href="index.php?page=annmap&amp;corpus={$corpus.id}">wszystkie</a>
        {/if}        
        </td>    
    </tr>
</table>

	<h2>Number of annotations according to categories and subcategories <small>(click the row to expand/roll-back)</h2>

	{*
	<table cellspacing="1" class="formated">
		<tr>
			<th rowspan="2">Anotacja</th>
			<th colspan="2">Liczba</th>
		<tr>
			<th style="text-align: right">unikalnych wartości</th>
			<th style="text-align: right">anotacji</th>
		</tr>
		
		
	{foreach from=$tags item=tag}
	<tr class="annotation_type">
		<td><a href="." class="toggle_simple" label=".annotation_type_{$tag.type}"><b>{$tag.type}</b></a></td>
		<td style="text-align:right">{$tag.unique}</td>
		<td style="text-align:right">{$tag.count}</td>
	</tr>
		<tr class="annotation_type_{$tag.type}" style="display: none">
			<td colspan="3"> 
			<ol>
			{foreach from=$tag.details item=detail}
				<li class="annotation_item"><span style="float: right;">{$detail.count}</span><span style="margin-right: 50px">{$detail.text}</span></li>
			{/foreach}
			</ol>
			</td>
		</tr>
	{/foreach}
	</table>
	*}
	{*kotu*}

	<table cellspacing="1" class="tablesorter" id="annmap" style="width: 800px">
		<thead>
		<tr>
			<th rowspan="2" style="width: 150px">Kategoria</th>
			<th rowspan="2" style="width: 150px">Podkategoria</th>
			<th rowspan="2">Anotacja</th>
			<th colspan="2">Liczba</th>
			<tr>
				<th style="text-align: right; width: 100px">unikalnych</th>
				<th style="text-align: right; width: 100px">anotacji</th>
			</tr>
		</tr>
		</thead>
		<tbody>
	{foreach from=$sets key=setName item=set}
		<tr class="setGroup">
			<td colspan="3">{$setName}</td>
			<td style="text-align:right">{$set.unique}</td>
			<td style="text-align:right">{$set.count}</td>
		</tr>
		{foreach from=$set key=subsetName item=subset}
			{if isset($subset) and is_array($subset)}
				<tr class="subsetGroup"  style="display:none">
					<td class="empty"></td>
					<td colspan="2">{$subsetName}</td>
					<td style="text-align:right">{$subset.unique}</td>
					<td style="text-align:right">{$subset.count}</td>
				</tr>
				
				{foreach from=$subset key=typeName item=tag}
					{if isset($tag) and is_array($tag)}
						<tr class="annotation_type" style="display:none">
							<td colspan="2" class="empty"></td>
							<td><a href="." class="toggle_simple" label=".annotation_type_{$tag.type}"><b>{$tag.type}</b></a></td>
							<td style="text-align:right">{$tag.unique}</td>
							<td style="text-align:right">{$tag.count}</td>
						</tr>
						<tr class="annotation_type_{$tag.type} annotation_type_names" style="display: none">
							<td colspan="2" class="empty2"></td>
							<td colspan="3"> 
							<ol>
							{foreach from=$tag.details item=detail}
								<li class="annotation_item">
									<span style="float: right;">{$detail.count}</span>
									<span style="margin-right: 50px">{$detail.text}</span>
									<div class="annotationItemLinks"></div>
								</li>
							{/foreach}
							</ol>
							</td>
						</tr>
					{/if}
				{/foreach}
			{/if}
		{/foreach}
	{/foreach}
		</tbody>
	</table>
	
	<!--<pre>
		{$tmp|@print_r}
	</pre>!-->
	
	
	
	<br/>
</td>

{include file="inc_footer.tpl"}
{include file="inc_header.tpl"}

<td class="table_cell_content">

<h1>Mapa anotacji</h1>
{*
<ul>
	<li><b>document_name</b> &mdash; nazwa dokumentu, w tym rozporządzenia, ustawy, regulaminu,
		<div><i>Czy data jest częścią nazwy, np. Rozporządzenie xxx z dnia 21 stycznia 2001 r.</i></div>
	</li>
	<li><b>document_reference</b> &mdash; wskazanie na konkretny fragment (rozdział, paragraf, punkt) dokumentu.</li>
	<li><i>organizacje</i>
		<ul>
			<li><b>company</b> &mdash; spółki, firmy, zakłady, itp.</li>
			<li><b>institution</b> &mdash; instytucje krajowe, rządowe, edukacyjne itp.</li>
		</ul>
	</li>
</ul>
*}
<br style="clear: both"/>

	<table>
		<tr>
			<td style="width: 150px">Liczba anotacji:</td>
			<td style="width: 100px; text-align: right"> <b>{$annotation_count}</b></td>
		</tr>
	</table>

	<h2>Liczba anotacji wg. rodzaju</h2>

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

	<table cellspacing="1" class="formated">
		<tr>
			<th rowspan="2">Kategoria</th>
			<th rowspan="2">Podkategoria</th>
			<th rowspan="2">Anotacja</th>
			<th colspan="2">Liczba</th>
			<tr>
				<th style="text-align: right">unikalnych wartości</th>
				<th style="text-align: right">anotacji</th>
			</tr>
		</tr>
		
		
	{foreach from=$sets key=setName item=set}
		<tr class="setGroup">
			<td>{$setName}</td>
			<td></td>
			<td></td>
			<td style="text-align:right">{$set.unique}</td>
			<td style="text-align:right">{$set.count}</td>
		</tr>
		{foreach from=$set key=subsetName item=subset}
			{if isset($subset) and is_array($subset)}
				<tr class="subsetGroup"  style="display:none">
					<td></td>
					<td>{$subsetName}</td>
					<td></td>
					<td style="text-align:right">{$subset.unique}</td>
					<td style="text-align:right">{$subset.count}</td>
				</tr>
				
				{foreach from=$subset key=typeName item=tag}
					{if isset($tag) and is_array($tag)}
						<tr class="annotation_type" style="display:none">
							<td colspan="2"></td>
							<td><a href="." class="toggle_simple" label=".annotation_type_{$tag.type}"><b>{$tag.type}</b></a></td>
							<td style="text-align:right">{$tag.unique}</td>
							<td style="text-align:right">{$tag.count}</td>
						</tr>
						<tr class="annotation_type_{$tag.type}" style="display: none">
							<td colspan="2"></td>
							<td colspan="3"> 
							<ol>
							{foreach from=$tag.details item=detail}
								<li class="annotation_item"><span style="float: right;">{$detail.count}</span><span style="margin-right: 50px">{$detail.text}</span></li>
							{/foreach}
							</ol>
							</td>
						</tr>
					{/if}
				{/foreach}
			{/if}
		{/foreach}
	{/foreach}
	</table>
	<pre>
		{$tmp|@print_r}
	</pre>
	
	
	
	<br/>
</td>

{include file="inc_footer.tpl"}
{include file="inc_header.tpl"}

<td class="table_cell_content">

<h1>Statystyka danych autorów listów</h1>
<div>
	Liczone po 
	{if $count_by <> 'author' } <b>listach</b> {else} <a href="index.php?page=lps_stats&amp;corpus={$corpus.id}&amp;count_by=letter">listach</a>{/if}
	/
	{if $count_by == 'author' } <b>autorach</b> {else} <a href="index.php?page=lps_stats&amp;corpus={$corpus.id}&amp;count_by=author">autorach</a>{/if}
</div>

<table>
	<tr>
		<td style="vertical-align: top">
			<h2>Wiek</h2>
			<table cellspacing="1" class="tablesorter" >
				<thead>
				<tr>
					<th>Wartość</th>
					<th>Liczba</th>
				</tr>
				</thead>
				<tbody>
					{foreach from=$age item=a}
					<tr>
						<th>{if $a.span_from}{$a.span_from} &ndash; {$a.span_to}{/if}</th>
						<td style="text-align: right">{$a.count}</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		
		</td>
		<td style="vertical-align: top; padding: 0 80px;">
			<h2>Płeć</h2>
			<table cellspacing="1" class="tablesorter" >
				<thead>
				<tr>
					<th>Wartość</th>
					<th>Liczba</th>
				</tr>
				</thead>
				<tbody>
				{foreach from=$gender item=g}
					<tr>
						<th>{$g.deceased_gender}</th>
						<td style="text-align: right">{$g.count}</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		
		</td>
		<td style="vertical-align: top; padding-right: 80px">
			<h2>Stan cywilny</h2>
			<table cellspacing="1" class="tablesorter" >
				<thead>
				<tr>
					<th>Wartość</th>
					<th>Liczba</th>
				</tr>
				</thead>
				<tbody>
					{foreach from=$maritial item=m}
					<tr>
						<th>{$m.deceased_maritial}</th>
						<td style="text-align: right">{$m.count}</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		
		</td>
		<td style="vertical-align: top">
			<h2>Rodzaj listu</h2>
			<table cellspacing="1" class="tablesorter" >
				<thead>
				<tr>
					<th>Wartość</th>
					<th>Liczba</th>
				</tr>
				</thead>
				<tbody>
					{foreach from=$source item=s}
					<tr>
						<th>{$s.source}</th>
						<td style="text-align: right">{$s.count}</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		
		</td>

	</tr>
</table>
		
<h1>Cechy parami</h1>

<table>
	<tr>
		<td style="vertical-align: top">
			<h2>Wiek/płeć</h2>
			<table cellspacing="1" class="tablesorter" >
				<thead>
				<tr>
					<th>Wiek</th>
					<th>male</th>
					<th>female</th>
				</tr>
				</thead>
				<tbody>
					{foreach from=$age_gender item=a}
					<tr>
						<th>{if $a.male.span_from}{$a.male.span_from} &ndash; {$a.male.span_to}{/if}</th>
						<td style="text-align: right">{$a.male.count|default:"-"}</td>
						<td style="text-align: right">{$a.female.count|default:"-"}</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		
		</td>
		<td style="vertical-align: top; padding: 0 40px;">
			<h2>Wiek/stan cywilny</h2>
			<table cellspacing="1" class="tablesorter" >
				<thead>
				<tr>
					<th>Wiek</th>
					<th>cohabitant</th>
					<th>single</th>
				</tr>
				</thead>
				<tbody>
					{foreach from=$age_maritial item=a}
					<tr>
						<th>{if $a.span_from}{$a.span_from} &ndash; {$a.span_to}{/if}</th>
						<td style="text-align: right">{$a.cohabitant.count|default:"-"}</td>
						<td style="text-align: right">{$a.single.count|default:"-"}</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		
		</td>
		<td style="vertical-align: top">
			<h2>Stan cywilny/płeć</h2>
			<table cellspacing="1" class="tablesorter" >
				<thead>
				<tr>
					<th>Wiek</th>
					<th>male</th>
					<th>female</th>
				</tr>
				</thead>
				<tbody>
					{foreach from=$maritial_gender item=m key=key}
					<tr>
						<th>{$key}</th>
						<td style="text-align: right">{$m.male.count|default:"-"}</td>
						<td style="text-align: right">{$m.female.count|default:"-"}</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		
		</td>

	</tr>
</table>
		
<br/>

{include file="inc_footer.tpl"}
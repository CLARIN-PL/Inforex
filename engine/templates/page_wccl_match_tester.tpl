{include file="inc_header.tpl"}

<table id="panels" cellspacing="10">
<tbody>
<tr>
<td class="left">
<h2>Rules</h2>
<textarea id="wccl_rules">match_rules (

  // 1.1.2014 r.
  apply(
    match(
      inter(class[0], {ldelim}ign{rdelim}),
      inter(base[0], ['.']),
      inter(class[0], {ldelim}ign{rdelim}),
      inter(base[0], ['.']),
      inter(class[0], {ldelim}ign{rdelim}),
      inter(base[0], ['rok']),
      inter(base[0], ['.'])
    ),
    actions(
      mark(M, "t3_date")
    )
  );


  // godz. 10.30
  apply(
    match(
      inter(base[0], ['godzina']),
      inter(base[0], ['.']),
      inter(class[0], {ldelim}ign{rdelim}),
      optional(inter(base[0], ['.'])),
      optional(inter(class[0], {ldelim}ign{rdelim}))
    ),
    actions(
      mark(M, "t3_time")
    )
  )

)
</textarea>
<div id="form">
	<input type="submit" class="button" id="process" value="Evaluate"/>
	Corpora: 
	<select id="corpus">
	{foreach from=$corpora item=item key=key name=corpus}
		<option value="{$smarty.foreach.corpus.index}">{$item.name}</option>
	{/foreach}
	</select>
</div>
</td>
<td class="right">
<h2>Rules evaluation</h2>
<div id="summary">
{*<small>Kliknij komórkę z liczbą w tabeli, aby wyświetlić tylko zdania zawierające wskazany typ anotacji.</small>*}
<table class="tablesorter" cellspacing="1">
<thead>
	<tr>
		<th>Typ anotacji</th>
		<th><span class="tp">True Positives</span></th>
		<th><span class="fp">False Positives</span></th>
		<th><span class="fn">False Negatives</span></th>
		<th>Precision</th>
		<th>Recall</th>
		<th>F-measure</th>
	</tr>
</thead>
<tbody>
{foreach from=$annotation_types item=type name=type}
<tr>
	<th><span class="{$type}">{$type}</span></th>
	<td id="{$type}-tp" class="click">0</td>
	<td id="{$type}-fp" class="click">0</td>
	<td id="{$type}-fn" class="click">0</td>
	<td id="{$type}-p" class="eval">0</td>
	<td id="{$type}-r" class="eval">0</td>
	<td id="{$type}-f" class="evalf">0</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
<div id="items">
	<div id="error">
		<b>Errors:</b>
		<ol id="errors">
			<li>sample error</li>
		</ol>
	</div>
	<ol id="sentences"></ol>
</div>
<div id="status">
Processed: <em id="count">-</em>
<input type="button" value="Stop" id="interupt" class="button disabled" disabled="disabled" />
</div>
</td>
</tr>
</tbody>
</table>


{include file="inc_footer.tpl"}

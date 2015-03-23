{include file="inc_header.tpl"}

<table id="panels" cellspacing="10">
<tbody>
<tr>
<td class="left">
<h2>Rules</h2>
<textarea id="wccl_rules">match_rules (
 
  apply(
    match(
      is("nam"),
      is("nam")
    ),
    actions(
      mark(M, "aux_subst")
    )
  )

)

</textarea>
<div id="form">
	<input type="submit" class="button" id="process" value="Run"/>
</div>
</td>
<td class="right">
<h2>Matches</h2>
<div id="items">
	<div id="error">
		<b></b>
		<ol id="errors">
		</ol>
	</div>
	<ol id="sentences"></ol>
</div>
<div id="status">
Staus: <em id="count">-</em>
<input type="button" value="Stop" id="interupt" class="button disabled" disabled="disabled" />
</div>
</td>
</tr>
</tbody>
</table>


{include file="inc_footer.tpl"}

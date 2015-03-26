{include file="inc_header.tpl"}

<table id="panels" cellspacing="10">
<tbody>
<tr>
<td class="left">
<div id="toolbox_wrapper">
	<h2>Toolbox</h2>
	<div class="elements" id="toolbox">
	    
	    <ul>
	        <li><a href="#elem-0">Rule</a></li>
	        <li><a href="#elem-1">Match</a></li>
	        <li><a href="#elem-2">Token attributes</a></li>
	    </ul>
	
	    <div id="elem-0">
	        <textarea id="wccl_rule_template">apply(
  match(
    [match_operators]
  ),
  conds(
    [cond_operators]
  ),
  actions(
    [action_operators]
  )
)</textarea>
	    </div>
	        
	    <div id="elem-1">
			<table>
			    <tr><td><a href="#"><span class="tag">equal</span>(arg1, arg2)</a></td><td>— arg1 is equal to arg2 (defined for all data types),</td></tr>
			    <tr><td><a href="#"><span class="tag">inter</span>(arg1, arg2)</a></td><td>— arg1 is part of arg2 (set of strings or set of symbols),</td></tr>
			    <tr><td><a href="#"><span class="tag">regex</span>(arg1, arg2)</a></td><td>— string arg1 matches regular expression arg2,</td></tr>
			    <tr><td><a href="#"><span class="tag">isannpart</span>(arg1)</a></td><td>— current token is a part of annotation of type arg1,</td></tr>
			    <tr><td><a href="#"><span class="tag">isannhead</span>(arg1)</a></td><td>— current token is a head of annotation of type arg1,</td></tr>
			    <tr><td><a href="#"><span class="tag">isannbeg</span>(arg1)</a>, <a href="#"><span class="tag">isannend</span>(arg1)</a></td><td>— current token starts (ends) an annotation
			    of type arg1,</td></tr>
			    <tr><td><a href="#"><span class="tag">not</span>(...)</a>, <a href="#"><span class="tag">or</span>(...)</a>, <a href="#"><span class="tag">and</a>(...)</a></td><td>— Boolean connectives</td></tr>
			</table>
	    </div>
	
	    <div id="elem-2">
	        <a href="http://nkjp.pl/poliqarp/help/ense2.html" target="_blank" style="float: right">tagset description</a>
	        <table>
	            <tr><th>Attribute</th><th>Values</th></tr>
	            <tr><td><span class="tagattr">orth</span>[0]</td><td><span class="string">"<i>string</i>"</span></td></tr>
	            <tr><td><span class="tagattr">base</span>[0]</td><td><span class="string">"<i>string</i>"</span></td></tr>
	            <tr><td><span class="tagattr">class</span>[0]</td><td>adja, adjp, adjc, conj, comp, interp, pred, xxx,
	                        adv, imps, inf, pant, pcon, qub, prep, siebie, subst, depr, ger, ppron12,
	                        ppron3, num, numcol, adj, pact, ppas, winien, praet, bedzie, fin, impt, aglt,
	                        ign, brev, burk, interj</td></tr>
	            <tr><td><span class="tagattr">nmb</span>[0]</td><td>sg, pl</td></tr>
	            <tr><td><span class="tagattr">cas</span>[0]</td><td>nom, gen, dat, acc, inst, loc, voc</td></tr>
	            <tr><td><span class="tagattr">gnd</span>[0]</td><td>m1, m2, m3, f, n</td></tr>
	            <tr><td><span class="tagattr">per</span>[0]</td><td>pri, sec, ter</td></tr>
	            <tr><td><span class="tagattr">deg</span>[0]</td><td>pos, com, sup</td></tr>
	            <tr><td><span class="tagattr">asp</span>[0]</td><td>imperf, perf</td></tr>
	            <tr><td><span class="tagattr">ngt</span>[0]</td><td>aff, neg</td></tr>
	            <tr><td><span class="tagattr">acm</span>[0]</td><td>congr, rec</td></tr>
	            <tr><td><span class="tagattr">acn</span>[0]</td><td>akc, nakc</td></tr>
	            <tr><td><span class="tagattr">ppr</span>[0]</td><td>npraep, praep</td></tr>
	            <tr><td><span class="tagattr">agg</span>[0]</td><td>agl, nagl</td></tr>
	            <tr><td><span class="tagattr">vcl</span>[0]</td><td>nwok, wok</td></tr>
	            <tr><td><span class="tagattr">dot</span>[0]</td><td>pun, npun</td></tr>
	        </table>
	    </div>
	    
	</div>
</div>

<h2>Rules</h2>

<div id="wccl_rules">
<textarea id="wccl_rules_textarea">{if $rules!=""}{$rules}{else}match_rules (
 
  apply(
    match(
      is("nam"),
      is("nam")
    ),
    actions(
      mark(M, "aux_subst")
    )
  )

){/if}
</textarea>
</div>

<div id="form">
	<input type="submit" class="button" id="process" value="Run"/>
</div>
<div>
    <input type="submit" class="button" id="save" value="Save"/>
    <span id="save_status"></span>
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

{include file="inc_header.tpl"}

<table id="panels" cellspacing="10">
<tbody>
<tr>
<td class="left">
<div id="toolbox_wrapper">
    <div id="toolbox_toogle" style="float: right"><a href="#">show/hide</a></div>
	<h2>Toolbox</h2>
	<div class="elements" id="toolbox">
	    
	    <ul>
	        <li><a href="#elem-0">Rule</a></li>
	        <li><a href="#elem-1">Match</a></li>
            <li><a href="#elem-2">Cond</a></li>
            <li><a href="#elem-3">Actions</a></li>
	        <li><a href="#elem-4">Token attributes</a></li>
	    </ul>
	
	    <div id="elem-0">
	        <textarea id="wccl_rule_template">apply(
  // Contains a list of operators matching a sequence of tokens and annotations
  match(
    [match_operators]
  ),
  // Contains a list of additional conditions to be satisfied to accept a completed match.
  // This section is optional
  cond(
    [cond_operators]
  ),
  // Contains a set of actions performed on the matched elements.
  actions(
    [action_operators]
  )
)</textarea>
	    </div>
	        
	    <div id="elem-1">
			<table>
			    <tr><td colspan="3"><h2>Single-token match</h2></td></tr>
			    <tr>
			         <td><a href="#"><span class="tag">equal</span>(arg1, arg2)</a></td>
			         <td>— </td>
			         <td><span class="description">arg1 is equal to arg2 (defined for all data types)</span>
			             <div class="examples">
			             <span class="header">Examples:</span>
			             <ul>
			                 <li><a href="#"><span class="tag">equal</span>(<span class="tagattr">base</span>[0], <span class="string">"miasto"</span>)</a> — matches tokens with base form <span class="string">"miasto"</span></li>
                             <li><a href="#"><span class="tag">equal</span>(<span class="tagattr">class</span>[0], <span class="tagattr">subst</span>)</a> — matches tokens with part of speech <span class="tagattr">subst</span></li>
                             <li><a href="#"><span class="tag">equal</span>(<span class="tagattr">cas</span>[0], <span class="tagattr">nom</span>)</a> — matches tokens with case <span class="tagattr">nom</span></li>
			             </ul>
			             </div>
			         </td>
			    </tr>
			    <tr>
			         <td><a href="#"><span class="tag">inter</span>(arg1, arg2)</a></td>
			         <td>— </td>
			         <td>arg1 is part of arg2 (set of strings or set of symbols),
                         <div class="examples">
                         <span class="header">Examples:</span>
                         <ul>
                             <li><a href="#"><span class="tag">inter</span>(<span class="tagattr">base</span>[0], [<span class="string">"ulica"</span>, <span class="string">"droga"</span>])</a> — matches tokens with base form <span class="string">"miasto"</span></li>
                             <li><a href="#"><span class="tag">equal</span>(<span class="tagattr">class</span>[0], <span class="tagattr">subst</span>)</a> — matches tokens with part of speech <span class="tagattr">subst</span></li>
                         </ul>
                         </div>			         
			         </td>
			     </tr>
			    <tr>
			         <td><a href="#"><span class="tag">regex</span>(arg1, arg2)</a></td>
			         <td>— </td>
			         <td>string arg1 matches regular expression arg2,
                         <div class="examples">
                         <span class="header">Examples:</span>
                         <ul>
                             <li><a href="#"><span class="tag">regex</span>(<span class="tagattr">orth</span>[0], <span class="string">"[0-9]{ldelim}4{rdelim}"</span>)</a> — matches tokens with text form containing exactly four digits</li>
                         </ul>
                         </div>			         
			         </td>
			    </tr>
			    <tr>
			         <td><a href="#"><span class="tag">isannpart</span>(arg1)</a></td>
                     <td>— </td>
			         <td>current token is a part of annotation of type arg1,
                         <div class="examples">
                         <span class="header">Examples:</span>
                         <ul>
                             <li><a href="#"><span class="tag">isannpart</span>(0, <span class="string">"nam_org"</span>)</a> — matches tokens which are part of <span class="string">"nam_org"</span> annotations</li>
                         </ul>
                         </div>                  
			         </td>
			    </tr>
			    {*
			    <tr>
			         <td><a href="#"><span class="tag">isannhead</span>(arg1)</a></td>
                     <td>— </td>
			         <td>current token is a head of annotation of type arg1,</td>
			    </tr>
			    *}
			    <tr>
			         <td><a href="#"><span class="tag">isannbeg</span>(arg1)</a>, <a href="#"><span class="tag">isannend</span>(arg1)</a></td>
                     <td>— </td>
			         <td>current token starts (ends) an annotation of type arg1,</td>
			    </tr>
			    <tr>
			         <td><a href="#"><span class="tag">not</span>(op)</a></td>
                     <td>— </td>
			         <td>matches tokens which do not conform op</td>
			    </tr>
                <tr>
                     <td><a href="#"><span class="tag">or</span>(op1, op2, ...)</a></td>
                     <td>— </td>
                     <td>Boolean connectives</td>
                </tr>
                <tr>
                     <td><a href="#"><a href="#"><span class="tag">and</span>(op1, op2, ...)</a></td>
                     <td>— </td>
                     <td>Boolean connectives</td>
                </tr>
                
                <tr><td colspan="3"><h2>Annotation match</h2></td></tr>
                <tr>
                    <td><a href="#"><span class="tag">is</span>("arg1")</a></td>
                    <td>— </td>
                    <td>matches an annotation of type arg1,</td>
                </tr>
                
                <tr><td colspan="3"><h2>Nested match</h2></td></tr>
                <tr>
                    <td><a href="#"><span class="tag">text</span>(arg1)</a></td>
                    <td>— </td>
                    <td>concatenation of orthographic forms is equal to arg1,</td>
                </tr>
                <tr>
                    <td><a href="#"><span class="tag">optional</span>(match)</a></td>
                    <td>— </td>
                    <td>zero or one match of the parenthesized expression,</td>
                </tr>
                <tr>
                    <td><a href="#"><span class="tag">repeat</span>(match)</a></td>
                    <td>— </td>
                    <td>one or more repetitions of the parenthesized match,</td>
                </tr>
                <tr>
                    <td><a href="#"><span class="tag">longest</span>(variants)</a></td>
                    <td>— </td>
                    <td>choose the longest match,</td>
                </tr>
                <tr>
                    <td><a href="#"><span class="tag">oneof</span>(variants)</a></td>
                    <td>— </td>
                    <td>choose the first matched.</td>
                </tr>    
			</table>
	    </div>

        <div id="elem-2">
            <table>
                <tr>
                    <td><a href="#"><span class="tag">ann</span>(arg1, arg2)</a></td>
                    <td>— </td>
                    <td>test if a sequence of tokens spanning over group with index arg1 is annotated with arg2,</td>
                </tr>
                <tr>
                    <td><a href="#"><span class="tag">ann</span>(arg1, arg2, arg3)</a></td>
                    <td>— </td>
                    <td>test if a sequence of tokens spanning from group arg1 to arg2 (inclusive) is annotated with arg3,</td>
                </tr>
                <tr>
                    <td><a href="#"><span class="tag">annsub</span>(arg1, arg2)</a></td>
                    <td>— </td>
                    <td>test if a sequence of tokens spanning over group with index arg1 is a part of annotation of type arg2,</td>
                </tr>
                <tr>
                    <td><a href="#"><span class="tag">annsub</span>(arg1, arg2, arg3)</a></td>
                    <td>— </td>
                    <td>test if a sequence of tokens spanning from group arg1 to arg2 (inclusive) is part of annotation of type arg3.</td>
                </tr>
            </table>
        </div>

        <div id="elem-3">
            <table>
                <tr>
                    <td><a href="#"><span class="tag">mark</span>(vec, chan)</a></td>
                    <td>— </td>
                    <td>creates an annotation of type chan spanning over tokens belonging to the given vector,</td>
                </tr>
                <tr>
                    <td><a href="#"><span class="tag">mark</span>(vec_from, vec_to, chan)</a></td>
                    <td>— </td>
                    <td>as above, but the annotation will span from the first token of vec_from to the last vector of vec_to,</td>
                </tr>
                <tr>
                    <td><a href="#"><span class="tag">mark</span>(vec_from, vec_to, vec_hd, chan)</a></td>
                    <td>— </td>
                    <td>as above, but the annotation head will be set to the first token of vec_hd.</td>
                </tr>
                <tr>
                    <td><a href="#"><span class="tag">remark</span>(...)</a></td>
                    <td>— </td>
                    <td>as mark but removes any annotations in the given channel that would intersect with the one being added.</td>
                </tr>
                <tr>
                    <td><a href="#"><span class="tag">unmark</span>(vec, chan)</a></td>
                    <td>— </td>
                    <td>removes the annotation matched.</td>
                </tr>
            </table>
        </div>
	
	    <div id="elem-4">
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

    <div id="annotation_types_toogle" style="float: right"><a href="#">show/hide</a></div>
	<h2>Annotations</h2>
	
	<div id="annotation_types">
		{*<pre>// comment
		annotation_name color required</pre>*}
		<textarea id="annotation_types_textarea" style="height: 100px">{if $annotations}{$annotations}{else}// Enter which annotations should be displayed.
// annotation_name color is_required
nam_org green   yes // names of organizations
nam_subst   red yes // substs which are part of organization names{/if}</textarea>
    </div>
    
 </div>

<h2>Rules</h2>

<div id="wccl_rules">
<textarea id="wccl_rules_textarea">{if $rules!=""}{$rules}{else}match_rules (

  apply(
    match(
      and(
        equal(class[0], subst),
        isannpart(0, "nam_org")
      )
    ),
    actions(
      mark(M, "nam_subst")
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

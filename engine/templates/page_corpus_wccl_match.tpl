{include file="inc_header2.tpl"}

<div class="container-fluid admin_tables corpus-wccl-page">
    <div class="row corpus-wccl-grid">
        <div class="col-md-6 corpus-wccl-column">
            <div class="panel scrollingWrapper administration-content-panel corpus-wccl-panel">
                <div class="panel-heading administration-content-heading corpus-wccl-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-code" aria-hidden="true"></i></span>
                    <span>WCCL rules</span>
                </div>
                <div class="panel-body corpus-wccl-panel-body">
                    <div class="panel administration-content-panel corpus-wccl-subpanel" id="toolbox_wrapper">
                        <div class="panel-heading administration-content-heading corpus-wccl-subheading">
                            <span class="administration-content-heading-icon corpus-wccl-subheading-icon"><i class="fa fa-wrench" aria-hidden="true"></i></span>
                            <span>Toolbox</span>
                            <a href="#" id="toolbox_toogle" class="corpus-wccl-toggle-link">show/hide</a>
                        </div>
                        <div class="panel-body elements corpus-wccl-toolbox-body" id="toolbox">
                            <ul class="nav nav-pills corpus-wccl-tabs">
                                <li class="active"><a data-toggle="tab" href="#elem-0">Rule</a></li>
                                <li><a data-toggle="tab" href="#elem-1">Match</a></li>
                                <li><a data-toggle="tab" href="#elem-2">Cond</a></li>
                                <li><a data-toggle="tab" href="#elem-3">Actions</a></li>
                                <li><a data-toggle="tab" href="#elem-4">Token attributes</a></li>
                            </ul>
                            <div class="tab-content corpus-wccl-tab-content">
                                <div id="elem-0" class="tab-pane fade in active">
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

                                <div id="elem-1" class="tab-pane fade">
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
                                            <td><a href="#"><span class="tag">and</span>(op1, op2, ...)</a></td>
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

                                <div id="elem-2" class="tab-pane fade">
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

                                <div id="elem-3" class="tab-pane fade">
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

                                <div id="elem-4" class="tab-pane fade">
                                    <a href="http://nkjp.pl/poliqarp/help/ense2.html" target="_blank" class="corpus-wccl-tagset-link">tagset description</a>
                                    <table>
                                        <tr><th>Attribute</th><th>Values</th></tr>
                                        <tr><td><span class="tagattr">orth</span>[0]</td><td><span class="string">"<i>string</i>"</span></td></tr>
                                        <tr><td><span class="tagattr">base</span>[0]</td><td><span class="string">"<i>string</i>"</span></td></tr>
                                        <tr><td><span class="tagattr">class</span>[0]</td><td>adja, adjp, adjc, conj, comp, interp, pred, xxx, adv, imps, inf, pant, pcon, qub, prep, siebie, subst, depr, ger, ppron12, ppron3, num, numcol, adj, pact, ppas, winien, praet, bedzie, fin, impt, aglt, ign, brev, burk, interj</td></tr>
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
                    </div>

                    <div class="panel administration-content-panel corpus-wccl-subpanel">
                        <div class="panel-heading administration-content-heading corpus-wccl-subheading">
                            <span class="administration-content-heading-icon corpus-wccl-subheading-icon"><i class="fa fa-tags" aria-hidden="true"></i></span>
                            <span>Annotations</span>
                            <a href="#" id="annotation_types_toogle" class="corpus-wccl-toggle-link">show/hide</a>
                        </div>
                        <div class="panel-body corpus-wccl-editor-panel" id="annotation_types">
                            <textarea id="annotation_types_textarea" style="height: 100px">{if $annotations}{$annotations}{else}// Enter which annotations should be displayed.
// annotation_name color is_required
nam_subst green   yes // subst{/if}</textarea>
                        </div>
                    </div>

                    <div class="panel administration-content-panel corpus-wccl-subpanel corpus-wccl-rules-panel">
                        <div class="panel-heading administration-content-heading corpus-wccl-subheading">
                            <span class="administration-content-heading-icon corpus-wccl-subheading-icon"><i class="fa fa-list-alt" aria-hidden="true"></i></span>
                            <span>Rules</span>
                        </div>
                        <div class="panel-body corpus-wccl-editor-panel" id="wccl_rules">
                            <textarea id="wccl_rules_textarea" class="scrolling">{if $rules!=""}{$rules}{else}match_rules (

  apply(
    match(
      and(
        equal(class[0], subst)
      )
    ),
    actions(
      mark(M, "nam_subst")
    )
  )

){/if}
</textarea>
                        </div>
                        <div class="panel-footer administration-content-footer corpus-wccl-footer">
                            <div class="corpus-wccl-footer-left">
                                <input type="submit" class="btn btn-primary corpus-wccl-run-button" id="process" value="Run"/>
                            </div>
                            <div class="corpus-wccl-footer-right">
                                <input type="submit" class="btn btn-success corpus-wccl-save-button" id="save" value="Save"/>
                                <span id="save_status" class="corpus-wccl-save-status"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 corpus-wccl-column">
            <div class="panel administration-content-panel corpus-wccl-panel corpus-wccl-results-panel">
                <div class="panel-heading administration-content-heading corpus-wccl-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-search" aria-hidden="true"></i></span>
                    <span>Matches</span>
                </div>
                <div class="panel-body corpus-wccl-panel-body">
                    <div class="corpus-wccl-results-body scrolling" id="items">
                        <div id="error" class="corpus-wccl-error-box">
                            <b></b>
                            <ol id="errors"></ol>
                        </div>
                        <ol id="sentences" class="corpus-wccl-sentences"></ol>
                        <div class="home-corpora-empty corpus-wccl-empty" id="wccl_empty_state">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <span>Run rules to inspect matches in the corpus.</span>
                        </div>
                    </div>
                </div>
                <div class="panel-footer administration-content-footer corpus-wccl-status-footer" id="status">
                    <input type="button" value="Stop" id="interupt" class="btn btn-warning disabled corpus-wccl-stop-button" disabled="disabled" />
                    <span class="corpus-wccl-status-label">Status:</span>
                    <em id="count">-</em>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}

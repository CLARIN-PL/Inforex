{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

{include file="inc_header2.tpl"}
<div class="panel panel-primary">
    <div class="panel-heading">Data integrity tests</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6 scrollingWrapper">
                <div class="panel panel-default">
                    <div class="panel-heading documents_in_corpus" id={$documents_in_corpus}>List of available tests
                    </div>
                    <div class="panel-body scrolling" style="overflow-x: hidden ">
                        <table class="table table-striped" id="testslist">
                            <thead>
                            <tr>
                                <th>Selected</th>
                                <th>Name and description</th>
                                <th>Status</th>
                                <th>Time</th>
                                <th>Errors</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="group">
                                <td>    </td>
                                <td colspan="5" class="test_name"><b>Testy
                                        dla
                                        lingwistów</b></td>
                            </tr>
                            <tr class="group" id="wrong_annotations_by_annotation">
                                <td class="col-checkbox"><input class="activeTest lin"
                                                                          id="wrong_annotations_by_annotation"
                                                                          type="checkbox"/>
                                </td>
                                <td style="vertical-align: middle" class="test_name">
                                    <b>Anotacje przecinające anotacje</b><br/>
                                    Dla każdej anotacji A1 nie istnieje taka anotacja A2 będąca tego samego typu, dla
                                    której
                                    (A2.from&#160;>&#160;A1.from&#160;AND&#160;A2.from&#160;<&#160;A1.to&#160;AND&#160;A2.to&#160;>&#160;A1.to)
                                    OR (A2.from&#160;<&#160;A1.from&#160;AND&#160;A2.to&#160;>&#160;A1.from&#160;AND&#160;A2.to&#160;<&#160;A1.to)
                                </td>
                                <td style="vertical-align: middle" class="test_process">-</td>
                                <td style="vertical-align: middle" class="test_time">-</td>
                                <td style="vertical-align: middle" class="test_result"><i>-</i></td>
                            </tr>
                            <tr class="group" id="wrong_annotation_chunks_type">
                                <td class="col-checkbox"><input class="activeTest lin"
                                                                          id="wrong_annotation_chunks_type"
                                                                          type="checkbox"/>
                                </td>
                                <td style="vertical-align: middle" class="test_name">
                                    <b>Anotacje składniowe</b><br/>
                                    Frazy „duże” są rozłączne (frazy duże to chunk_np, chunk_adjp, chunk_vp).</br>Frazy
                                    chunk_agp nie mogą przekraczać granic fraz „dużych”.</br>Frazy chunk_qp nie mogą
                                    przekraczać
                                    granic fraz chunk_agp ani granic fraz „dużych”.
                                </td>
                                <td style="vertical-align: middle" class="test_process">-</td>
                                <td style="vertical-align: middle" class="test_time">-</td>
                                <td style="vertical-align: middle" class="test_result"><i>-</i></td>
                            </tr>
                            <tr class="group" id="wrong_annotation_in_annotation">
                                <td class="col-checkbox"><input class="activeTest lin"
                                                                          id="wrong_annotation_in_annotation"
                                                                          type="checkbox"/>
                                </td>
                                <td style="vertical-align: middle" class="test_name">
                                    <b>Anotacje w anotacjach</b><br/>
                                    Dla każdej anotacji A1 nie istnieje anotacja A2 będąca tego samego typu, dla której
                                    (A2.from&#160;>=&#160;A1.from&#160;AND&#160;A2.to&#160;<=&#160;A1.to)
                                </td>
                                <td style="vertical-align: middle" class="test_process">-</td>
                                <td style="vertical-align: middle" class="test_time">-</td>
                                <td style="vertical-align: middle" class="test_result"><i>-</i></td>
                            </tr>
                            <tr class="group" id="wrong_annotations_duplicate">
                                <td class="col-checkbox"><input class="activeTest lin"
                                                                          id="wrong_annotations_duplicate"
                                                                          type="checkbox"/>
                                </td>
                                <td style="vertical-align: middle" class="test_name">
                                    <b>Duplikaty anotacji</b><br/>
                                    Duplikatem jest para anotacji, które posiadają takie same wartości dla atrybutów
                                    `report_id`, `from`, `to`, `type` oraz ustawione są jako stage=`final`
                                </td>
                                <td style="vertical-align: middle" class="test_process">-</td>
                                <td style="vertical-align: middle" class="test_time">-</td>
                                <td style="vertical-align: middle" class="test_result"><i>-</i></td>
                            </tr>
                            <tr class="group" id="wrong_annotations_by_sentence">
                                <td class="col-checkbox"><input class="activeTest lin"
                                                                          id="wrong_annotations_by_sentence"
                                                                          type="checkbox"/>
                                </td>
                                <td style="vertical-align: middle" class="test_name">
                                    <b>Anotacje przekraczające granice zdań</b><br/>
                                    Anotacje wykraczające poza granice zdania
                                </td>
                                <td style="vertical-align: middle" class="test_process">-</td>
                                <td style="vertical-align: middle" class="test_time">-</td>
                                <td style="vertical-align: middle" class="test_result"><i>-</i></td>
                            </tr>
                            <tr class="group" id="wrong_chunk">
                                <td class="col-checkbox"><input class="activeTest lin" id="wrong_chunk"
                                                                          type="checkbox"/></td>
                                <td style="vertical-align: middle" class="test_name">
                                    <b>Struktura dokumentu</b><br/>
                                    Dokumenty zawierające błędy w strukturze dokumentu
                                </td>
                                <td style="vertical-align: middle" class="test_process">-</td>
                                <td style="vertical-align: middle" class="test_time">-</td>
                                <td style="vertical-align: middle" class="test_result"><i>-</i></td>
                            </tr>
                            <tr class="group" id="empty_chunk">
                                <td class="col-checkbox"><input class="activeTest lin" id="empty_chunk"
                                                                          type="checkbox"/></td>
                                <td style="vertical-align: middle" class="test_name">
                                    <b>Wykrywanie pustych chunków</b><br/>
                                    Dokumenty zawierające puste chunki lub chunki zawierające tylko białe znaki
                                </td>
                                <td style="vertical-align: middle" class="test_process">-</td>
                                <td style="vertical-align: middle" class="test_time">-</td>
                                <td style="vertical-align: middle" class="test_result"><i>-</i></td>
                            </tr>
                            <tr class="group">
                                <td style="background: #eee"></td>
                                <td style="vertical-align: middle; background: #eee" colspan="5" class="test_name"><b>Testy
                                        techniczne</b></td>
                            </tr>
                            <tr class="group" id="wrong_tokens">
                                <td class="col-checkbox"><input class="activeTest tech" id="wrong_tokens"
                                                                          type="checkbox"/></td>
                                <td style="vertical-align: middle" class="test_name">
                                    <b>Ciągłość tokenów</b><br/>
                                    Dla każdego tokenu A w dokumencie (oprócz ostatniego) istnieje token B taki, że
                                    (A.to+1&#160;=&#160;B.from)
                                </td>
                                <td style="vertical-align: middle" class="test_process">-</td>
                                <td style="vertical-align: middle" class="test_time">-</td>
                                <td style="vertical-align: middle" class="test_result"><i>-</i></td>
                            </tr>
                            <tr class="group" id="tokens_out_of_scale">
                                <td class="col-checkbox"><input class="activeTest tech"
                                                                          id="tokens_out_of_scale"
                                                                          type="checkbox"/></td>
                                <td style="vertical-align: middle" class="test_name">
                                    <b>Zasięg tokenów</b><br/>
                                    Indeksy tokenów nie mogą wykraczać poza ramy dokumnetu, czyli dla każdego tokenu T w
                                    dokumencie D spełniona jest zależność, (T.from&#160;<=&#160;D.length&#160;AND&#160;T.to&#160;<=&#160;D.length)
                                </td>
                                <td style="vertical-align: middle" class="test_process">-</td>
                                <td style="vertical-align: middle" class="test_time">-</td>
                                <td style="vertical-align: middle" class="test_result"><i>-</i></td>
                            </tr>
                            <tr class="group" id="wrong_annotations">
                                <td class="col-checkbox"><input class="activeTest tech" id="wrong_annotations"
                                                                          type="checkbox"/></td>
                                <td style="vertical-align: middle" class="test_name">
                                    <b>Tokeny przecinające anotacje</b><br/>
                                    Dla każdej anotacji A nie istnieje taki token T, dla którego (T.from&#160;>&#160;A.from&#160;AND&#160;T.from&#160;<&#160;A.to&#160;AND&#160;T.to&#160;>&#160;A.to)
                                    OR (T.from&#160;<&#160;A.from&#160;AND&#160;T.to&#160;>&#160;A.from&#160;AND&#160;T.to&#160;<&#160;A.to)
                                </td>
                                <td style="vertical-align: middle" class="test_process">-</td>
                                <td style="vertical-align: middle" class="test_time">-</td>
                                <td style="vertical-align: middle" class="test_result"><i>-</i></td>
                            </tr>
                            </tbody>
                        </table>
                        {if count($annotations_in_corpus)>0}
                            <hr/>
                            <div class="annotations_in_corpus">
                                <h1>Apply annotation tests for the following sets of annotations</h1>
                                <form class="corpusannotations">
                                    <ul class="list-group row" style="margin-left: 10px">
                                    {foreach from=$annotations_in_corpus item=ann key=k}
                                        <li class="list-group-item col-xs-6" style="border-width: 0; margin: 0; padding: 2px"><input class="activeAnnotation" type="checkbox"
                                               name={$ann.description} value="{$ann.annotation_set_id}"
                                               id="{$ann.annotation_set_id}"/>
                                        <label for="{$ann.annotation_set_id}">{$ann.name}</label>{if $ann.description} &mdash; {$ann.description}{/if}</li>
                                    {/foreach}
                                    </ul>
                                </form>
                            </div>
                        {/if}
                    </div>
                    <div class="panel-footer">
                        <button class="buttonTest stop btn btn-primary">Test start</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 scrollingWrapper">
                <div class="panel panel-default">
                    <div class="panel-heading">List of errors found by the selected test</div>
                    <div class="panel-body scrolling">
                        <table id="tests_document_list" class="table table-striped">
                            <thead>
                            <tr>
                                <th style="width: 40px;">No.</th>
                                <th style="width: 100px;">Document id</th>
                                <th style="width: 40px;">Count</th>
                                <th>Invalid elements</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{include file="inc_footer.tpl"}
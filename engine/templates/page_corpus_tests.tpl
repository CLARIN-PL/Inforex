{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

{include file="inc_header2.tpl"}

<div class="container-fluid admin_tables corpus-tests-page">
    <div class="row corpus-tests-grid">
        <div class="col-md-6 corpus-tests-column">
            <div class="panel scrollingWrapper administration-content-panel corpus-tests-panel">
                <div class="panel-heading administration-content-heading corpus-tests-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-shield" aria-hidden="true"></i></span>
                    <span>Data integrity tests</span>
                    <span class="home-corpora-counter corpus-tests-counter">{$documents_in_corpus}</span>
                </div>
                <div class="panel-body corpus-tests-panel-body">
                    <div class="corpus-tests-toolbar">
                        <span class="corpus-tests-toolbar-label">Available tests</span>
                        <span class="corpus-tests-toolbar-meta">documents: {$documents_in_corpus}</span>
                    </div>
                    <div class="administration-table-wrapper corpus-tests-table-wrapper">
                        <table class="table table-striped table-hover administration-table corpus-tests-table" id="testslist">
                            <thead>
                            <tr>
                                <th class="corpus-tests-select-column">Selected</th>
                                <th>Name and description</th>
                                <th class="corpus-tests-status-column">Status</th>
                                <th class="corpus-tests-time-column">Time</th>
                                <th class="corpus-tests-errors-column">Errors</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="group corpus-tests-group-heading">
                                <td></td>
                                <td colspan="4" class="test_name"><b>Testy dla lingwistów</b></td>
                            </tr>
                            <tr class="group" id="wrong_annotations_by_annotation">
                                <td class="col-checkbox"><input class="activeTest lin" id="wrong_annotations_by_annotation" type="checkbox"/></td>
                                <td class="test_name corpus-tests-description-cell">
                                    <b>Anotacje przecinające anotacje</b><br/>
                                    Dla każdej anotacji A1 nie istnieje taka anotacja A2 będąca tego samego typu, dla której
                                    (A2.from&#160;>&#160;A1.from&#160;AND&#160;A2.from&#160;<&#160;A1.to&#160;AND&#160;A2.to&#160;>&#160;A1.to)
                                    OR (A2.from&#160;<&#160;A1.from&#160;AND&#160;A2.to&#160;>&#160;A1.from&#160;AND&#160;A2.to&#160;<&#160;A1.to)
                                </td>
                                <td class="test_process">-</td>
                                <td class="test_time">-</td>
                                <td class="test_result"><i>-</i></td>
                            </tr>
                            <tr class="group" id="wrong_annotation_chunks_type">
                                <td class="col-checkbox"><input class="activeTest lin" id="wrong_annotation_chunks_type" type="checkbox"/></td>
                                <td class="test_name corpus-tests-description-cell">
                                    <b>Anotacje składniowe</b><br/>
                                    Frazy „duże” są rozłączne (frazy duże to chunk_np, chunk_adjp, chunk_vp).<br/>
                                    Frazy chunk_agp nie mogą przekraczać granic fraz „dużych”.<br/>
                                    Frazy chunk_qp nie mogą przekraczać granic fraz chunk_agp ani granic fraz „dużych”.
                                </td>
                                <td class="test_process">-</td>
                                <td class="test_time">-</td>
                                <td class="test_result"><i>-</i></td>
                            </tr>
                            <tr class="group" id="wrong_annotation_in_annotation">
                                <td class="col-checkbox"><input class="activeTest lin" id="wrong_annotation_in_annotation" type="checkbox"/></td>
                                <td class="test_name corpus-tests-description-cell">
                                    <b>Anotacje w anotacjach</b><br/>
                                    Dla każdej anotacji A1 nie istnieje anotacja A2 będąca tego samego typu, dla której
                                    (A2.from&#160;>=&#160;A1.from&#160;AND&#160;A2.to&#160;<=&#160;A1.to)
                                </td>
                                <td class="test_process">-</td>
                                <td class="test_time">-</td>
                                <td class="test_result"><i>-</i></td>
                            </tr>
                            <tr class="group" id="wrong_annotations_duplicate">
                                <td class="col-checkbox"><input class="activeTest lin" id="wrong_annotations_duplicate" type="checkbox"/></td>
                                <td class="test_name corpus-tests-description-cell">
                                    <b>Duplikaty anotacji</b><br/>
                                    Duplikatem jest para anotacji, które posiadają takie same wartości dla atrybutów
                                    `report_id`, `from`, `to`, `type` oraz ustawione są jako stage=`final`
                                </td>
                                <td class="test_process">-</td>
                                <td class="test_time">-</td>
                                <td class="test_result"><i>-</i></td>
                            </tr>
                            <tr class="group" id="wrong_annotations_by_sentence">
                                <td class="col-checkbox"><input class="activeTest lin" id="wrong_annotations_by_sentence" type="checkbox"/></td>
                                <td class="test_name corpus-tests-description-cell">
                                    <b>Anotacje przekraczające granice zdań</b><br/>
                                    Anotacje wykraczające poza granice zdania
                                </td>
                                <td class="test_process">-</td>
                                <td class="test_time">-</td>
                                <td class="test_result"><i>-</i></td>
                            </tr>
                            <tr class="group" id="wrong_chunk">
                                <td class="col-checkbox"><input class="activeTest lin" id="wrong_chunk" type="checkbox"/></td>
                                <td class="test_name corpus-tests-description-cell">
                                    <b>Struktura dokumentu</b><br/>
                                    Dokumenty zawierające błędy w strukturze dokumentu
                                </td>
                                <td class="test_process">-</td>
                                <td class="test_time">-</td>
                                <td class="test_result"><i>-</i></td>
                            </tr>
                            <tr class="group" id="empty_chunk">
                                <td class="col-checkbox"><input class="activeTest lin" id="empty_chunk" type="checkbox"/></td>
                                <td class="test_name corpus-tests-description-cell">
                                    <b>Wykrywanie pustych chunków</b><br/>
                                    Dokumenty zawierające puste chunki lub chunki zawierające tylko białe znaki
                                </td>
                                <td class="test_process">-</td>
                                <td class="test_time">-</td>
                                <td class="test_result"><i>-</i></td>
                            </tr>
                            <tr class="group corpus-tests-group-heading">
                                <td></td>
                                <td colspan="4" class="test_name"><b>Testy techniczne</b></td>
                            </tr>
                            <tr class="group" id="wrong_tokens">
                                <td class="col-checkbox"><input class="activeTest tech" id="wrong_tokens" type="checkbox"/></td>
                                <td class="test_name corpus-tests-description-cell">
                                    <b>Ciągłość tokenów</b><br/>
                                    Dla każdego tokenu A w dokumencie (oprócz ostatniego) istnieje token B taki, że
                                    (A.to+1&#160;=&#160;B.from)
                                </td>
                                <td class="test_process">-</td>
                                <td class="test_time">-</td>
                                <td class="test_result"><i>-</i></td>
                            </tr>
                            <tr class="group" id="tokens_out_of_scale">
                                <td class="col-checkbox"><input class="activeTest tech" id="tokens_out_of_scale" type="checkbox"/></td>
                                <td class="test_name corpus-tests-description-cell">
                                    <b>Zasięg tokenów</b><br/>
                                    Indeksy tokenów nie mogą wykraczać poza ramy dokumnetu, czyli dla każdego tokenu T w
                                    dokumencie D spełniona jest zależność, (T.from&#160;<=&#160;D.length&#160;AND&#160;T.to&#160;<=&#160;D.length)
                                </td>
                                <td class="test_process">-</td>
                                <td class="test_time">-</td>
                                <td class="test_result"><i>-</i></td>
                            </tr>
                            <tr class="group" id="wrong_annotations">
                                <td class="col-checkbox"><input class="activeTest tech" id="wrong_annotations" type="checkbox"/></td>
                                <td class="test_name corpus-tests-description-cell">
                                    <b>Tokeny przecinające anotacje</b><br/>
                                    Dla każdej anotacji A nie istnieje taki token T, dla którego (T.from&#160;>&#160;A.from&#160;AND&#160;T.from&#160;<&#160;A.to&#160;AND&#160;T.to&#160;>&#160;A.to)
                                    OR (T.from&#160;<&#160;A.from&#160;AND&#160;T.to&#160;>&#160;A.from&#160;AND&#160;T.to&#160;<&#160;A.to)
                                </td>
                                <td class="test_process">-</td>
                                <td class="test_time">-</td>
                                <td class="test_result"><i>-</i></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    {if count($annotations_in_corpus)>0}
                        <div class="corpus-tests-annotations">
                            <div class="corpus-tests-toolbar">
                                <span class="corpus-tests-toolbar-label">Annotation sets</span>
                                <span class="corpus-tests-toolbar-meta">apply linguistic tests selectively</span>
                            </div>
                            <form class="corpusannotations corpus-tests-annotations-form">
                                <ul class="list-group row corpus-tests-annotations-list">
                                {foreach from=$annotations_in_corpus item=ann key=k}
                                    <li class="list-group-item col-xs-6 corpus-tests-annotation-item">
                                        <input class="activeAnnotation" type="checkbox" name="{$ann.description}" value="{$ann.annotation_set_id}" id="{$ann.annotation_set_id}"/>
                                        <label for="{$ann.annotation_set_id}">{$ann.name}</label>{if $ann.description} <span class="corpus-tests-annotation-separator">/</span> {$ann.description}{/if}
                                    </li>
                                {/foreach}
                                </ul>
                            </form>
                        </div>
                    {/if}
                </div>
                <div class="panel-footer administration-content-footer corpus-tests-footer">
                    <button class="buttonTest stop btn btn-primary corpus-tests-run-button"><i class="fa fa-play" aria-hidden="true"></i> Test start</button>
                </div>
            </div>
        </div>

        <div class="col-md-6 corpus-tests-column">
            <div class="panel scrollingWrapper administration-content-panel corpus-tests-panel">
                <div class="panel-heading administration-content-heading corpus-tests-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                    <span>List of errors found by the selected test</span>
                </div>
                <div class="panel-body corpus-tests-panel-body">
                    <div class="administration-table-wrapper corpus-tests-table-wrapper corpus-tests-results-wrapper">
                        <table id="tests_document_list" class="table table-striped table-hover administration-table corpus-tests-results-table">
                            <thead>
                            <tr>
                                <th class="corpus-tests-no-column">No.</th>
                                <th class="corpus-tests-document-column">Document id</th>
                                <th class="corpus-tests-count-column">Count</th>
                                <th>Invalid elements</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="home-corpora-empty corpus-tests-empty corpus-tests-results-empty">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <span>Select a finished test to inspect detected errors.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}

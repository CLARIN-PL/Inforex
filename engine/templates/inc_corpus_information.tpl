{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="container-fluid admin_tables">
    <div class="row">
        <div class="panel panel-primary scrollingWrapper" style="margin: 5px; width: 40%;">
            <div class="panel-heading">Basic information</div>
            <div class="tableContent panel-body scrolling" style="">
                <table class="tablesorter table table-striped" id="corpusElementsContainer" cellspacing="1">
                    <tr>
                        <th id="name"><strong> Name: </strong></th>
                        <td>{$corpus.name}</td>
                        {if isCorpusOwner() || "admin"|has_role}
                            <td>
                                <div class="tableOptions" element="corpus_details" parent="corpusElementsContainer">
                                    <a href="#" class="btn btn-primary edit" style="margin: 2px">edit</a>
                                </div>
                            </td>
                        {/if}
                    </tr>
                    <tr>
                        <th id="user_id"><strong>Owner:</strong></th>
                        <td>{$owner.screename}</td>
                        {if isCorpusOwner() || "admin"|has_role}
                            <td>
                                <div class="tableOptions" element="corpus_details" parent="corpusElementsContainer">
                                    <a href="#" class="btn btn-primary edit" style="margin: 2px">edit</a>
                                </div>
                            </td>
                        {/if}
                    </tr>
                    <tr>
                        <th id="public"><strong>Access:</strong></th>
                        <td>{if $corpus.public}public{else}restricted{/if}</td>
                        {if isCorpusOwner() || "admin"|has_role}
                            <td>
                                <div class="tableOptions" element="corpus_details" parent="corpusElementsContainer">
                                    <a href="#" class="btn btn-primary edit" style="margin: 2px">edit</a>
                                </div>
                            </td>
                        {/if}
                    </tr>
                    <tr>
                        <th id="description"><strong>Description:</strong></th>
                        <td>{$corpus.description}</td>
                        {if isCorpusOwner() || "admin"|has_role}
                            <td>
                                <div class="tableOptions" element="corpus_details" parent="corpusElementsContainer">
                                    <a href="#" type="button" class="btn btn-primary edit" style="margin: 2px">edit</a>
                                </div>
                            </td>
                        {/if}
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
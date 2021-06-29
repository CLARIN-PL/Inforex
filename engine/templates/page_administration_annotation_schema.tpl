{extends file="admin-layout.tpl"}
{block name=body}
    <div class="row border-bottom bd-gray m-1">
        <div class="d-flex flex-align-center">
            <ul class="breadcrumbs bg-transparent">
                <li class="page-item"><a href="#" class="page-link">Administration</a></li>
                <li class="page-item"><a href="#" class="page-link"><span class="mif-anchor"> Annotation schema</a></li>
            </ul>
        </div>
    </div>
    <div class="m-3">
        <div class="row">
            <div class="cell-md-4 mt-1">
                <div data-role="panel"
                     data-title-caption="Annotation sets"
                     data-title-icon="<span class='mif-apps'></span>"
                     data-custom-buttons="annotationSetsButtons">
                    <div class="d-flex flex-wrap flex-nowrap-lg flex-align-center flex-justify-center flex-justify-start-lg mb-2">
                        <div class="w-100 mb-2 mb-0-lg" id="annotationSetsTableSearch"></div>
                        <div class="ml-2 mr-2" style="display: none;" id="annotationSetsTableRows"></div>
                    </div>
                    <table class="table row-border striped row-hover subcompact" id="annotationSetsTable"
                           data-role="table"
                           data-search-wrapper="#annotationSetsTableSearch"
                           data-rows-wrapper="#annotationSetsTableRows"
                           data-info-wrapper="#annotationSetsTableInfo"
                           data-check="true"
                           data-check-type="radio"
                           data-check-col-index="0"
                           data-check-name="chkSets"
                           data-pagination-wrapper="#annotationSetsTablePagination"
                           data-rows="12"
                           data-on-check-click="onCheckClickAnnotationSets"
                           data-cell-wrapper="false"
                           data-horizontal-scroll="true">
                        <thead>
                        <tr>
                            <th data-cls-column="id">Id</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Owner</th>
                            <th>Access</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$annotationSets item=set}
                            <tr>
                                <td class="id">{$set.id}</td>
                                <td>{$set.name}</td>
                                <td>{$set.description}</td>
                                <td>{$set.screename}</td>
                                <td>{if $set.public == 1} public {else} private {/if}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                    <div class="border-top bd-gray">
                        <div class="p-4 d-flex flex-row-r">
                            <button class="tool-button text-button alert"><span class="mif-bin"></span> Delete</button>
                            <button class="tool-button text-button">Corpora</button>
                            <button class="tool-button text-button info"><span class="mif-pencil"></span> Edit</button>
                            <button class="tool-button text-button primary"><span class="mif-plus"></span> Create</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="cell-md-4 mt-1">
                <div data-role="panel" data-title-caption="Annotation subsets"
                     data-custom-buttons="annotationSubsetsButtons"
                     data-title-icon="<span class='mif-apps'></span>">
                    <div class="d-flex flex-wrap flex-nowrap-lg flex-align-center flex-justify-center flex-justify-start-lg mb-2">
                        <div class="w-100 mb-2 mb-0-lg" id="annotationSubsetsTableSearch"></div>
                        <div class="ml-2 mr-2" style="display: none;" id="annotationSubsetsTableRows"></div>
                    </div>
                    <table id="annotationSubsetsTable" class="table row-border striped row-hover subcompact"
                           data-role="table"
                           data-search-wrapper="#annotationSubsetsTableSearch"
                           data-rows-wrapper="#annotationSubsetsTableRows"
                           data-info-wrapper="#annotationSubsetsTableInfo"
                           data-check="true"
                           data-check-type="radio"
                           data-check-col-index="0"
                           data-check-name="chkSubsets"
                           data-on-check-click="onCheckClickAnnotationSubsets"
                           data-pagination-wrapper="#annotationSubsetsTablePagination"
                           data-rows="12"
                           data-cell-wrapper="false"
                           data-horizontal-scroll="true">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                    </table>
                    <div class="border-top bd-gray">
                        <div class="p-4 d-flex flex-row-r">
                            <button class="tool-button text-button alert"><span class="mif-bin"></span> Delete</button>
                            <button class="tool-button text-button info"><span class="mif-pencil"></span> Edit</button>
                            <button class="tool-button text-button primary"><span class="mif-plus"></span> Create</button>
                        </div>
                    </div>
                </div>
                </div>
            <div class="cell-md-4 mt-1">
                <div data-role="panel" data-title-caption="Categories"
                     data-custom-buttons="annotationTypesButtons"
                     data-title-icon="<span class='mif-apps'></span>">
                    <div class="d-flex flex-wrap flex-nowrap-lg flex-align-center flex-justify-center flex-justify-start-lg mb-2">
                        <div class="w-100 mb-2 mb-0-lg" id="annotationTypesTableSearch"></div>
                        <div class="ml-2 mr-2" style="display: none;" id="annotationTypesTableRows"></div>
                    </div>
                    <table id="annotationTypesTable" class="table row-border striped row-hover subcompact"
                           data-role="table"
                           data-search-wrapper="#annotationTypesTableSearch"
                           data-rows-wrapper="#annotationTypesTableRows"
                           data-info-wrapper="#annotationTypesTableInfo"
                           data-check="true"
                           data-check-type="radio"
                           data-check-name="chkTypes"
                           data-pagination-wrapper="#annotationTypesTablePagination"
                           data-rows="12"
                           data-cell-wrapper="false"
                           data-horizontal-scroll="true">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Symbolic name</th>
                            <th>Display name</th>
                            <th>Description</th>
                            <th>Visibility</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="border-top bd-gray">
                        <div class="p-4 d-flex flex-row-r">
                            <button class="tool-button text-button alert"><span class="mif-bin"></span> Delete</button>
                            <button class="tool-button text-button info"><span class="mif-pencil"></span> Edit</button>
                            <button class="tool-button text-button primary"><span class="mif-plus"></span> Create</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/block}
{block name=scripts}
    <script src="js/page_administration_annotation_schema.js"></script>
{/block}

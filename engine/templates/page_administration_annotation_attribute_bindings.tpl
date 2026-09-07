{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<div class="container-fluid administration-attribute-bindings">
    <div class="administration-bindings-intro">
        <span class="administration-bindings-eyebrow">Annotation schema</span>
        <h1>Attribute bindings</h1>
        <p>Choose an annotation type and define which shared attributes users can edit for annotations of that type.</p>
    </div>

    <div class="row administration-bindings-workspace">
        <div class="col-md-4 administration-bindings-column">
            <div class="panel administration-content-panel administration-bindings-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-sitemap" aria-hidden="true"></i></span>
                    <span>1. Select annotation type</span>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="bindingsCorpusId">Corpus</label>
                        <select class="form-control" id="bindingsCorpusId">
                            <option value="">Select corpus</option>
                            {foreach from=$corpora item=corpus}
                                <option value="{$corpus.id}">{$corpus.name|escape}{if $corpus.public == 0} (private){/if}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bindingsAnnotationSetId">Annotation set</label>
                        <select class="form-control" id="bindingsAnnotationSetId" disabled="disabled"><option value="">Select annotation set</option></select>
                    </div>
                    <div class="form-group">
                        <label for="bindingsAnnotationSubsetId">Annotation subset</label>
                        <select class="form-control" id="bindingsAnnotationSubsetId" disabled="disabled"><option value="">Select annotation subset</option></select>
                    </div>
                    <div class="form-group">
                        <label for="bindingsAnnotationTypeId">Annotation type</label>
                        <select class="form-control" id="bindingsAnnotationTypeId" disabled="disabled"><option value="">Select annotation type</option></select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8 administration-bindings-column">
            <div class="panel administration-content-panel administration-bindings-panel">
                <div class="panel-heading administration-content-heading administration-bindings-heading">
                    <span><span class="administration-content-heading-icon"><i class="fa fa-link" aria-hidden="true"></i></span> 2. Assign shared attributes</span>
                    <span class="administration-bindings-count" id="bindingsSelectedCount">0 selected</span>
                </div>
                <div class="panel-body">
                    <div class="administration-bindings-type-summary" id="bindingsTypeSummary">Select an annotation type to load its attribute bindings.</div>
                    <div class="administration-bindings-toolbar">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-search" aria-hidden="true"></i></span>
                            <label class="sr-only" for="bindingsAttributeSearch">Search shared attributes</label>
                            <input type="search" class="form-control" id="bindingsAttributeSearch" placeholder="Filter shared attributes" disabled="disabled">
                        </div>
                        <div class="btn-group" role="group" aria-label="Attribute selection">
                            <button type="button" class="btn btn-default" id="bindingsSelectAll" disabled="disabled">Select all</button>
                            <button type="button" class="btn btn-default" id="bindingsClearAll" disabled="disabled">Clear</button>
                        </div>
                    </div>
                    <div class="administration-bindings-list" id="bindingsAttributeList" aria-live="polite">
                        <div class="administration-bindings-empty">No annotation type selected.</div>
                    </div>
                </div>
                <div class="panel-footer administration-content-footer administration-bindings-footer">
                    <div id="bindingsStatus" class="administration-bindings-status" role="status" aria-live="polite"></div>
                    <button type="button" class="btn btn-primary" id="bindingsSaveButton" disabled="disabled">
                        <i class="fa fa-floppy-o" aria-hidden="true"></i> Save bindings
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}

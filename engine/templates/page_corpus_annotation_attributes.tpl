{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}

<div class="container-fluid admin_tables corpus-annotation-attributes-page">
<div class="row corpus-annotation-attributes-grid">

    <div class="col-md-3 scrollingWrapper corpus-annotation-attributes-sidebar-column">
        <div class="panel administration-content-panel corpus-annotation-attributes-panel">
            <div class="panel-heading administration-content-heading">
                <span class="administration-content-heading-icon"><i class="fa fa-tags" aria-hidden="true"></i></span>
                <span>Attribute values</span>
            </div>
            <div class="panel-body corpus-annotation-attributes-toolbar">
                <div class="input-group corpus-annotation-attributes-search">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span>
                    <input class="form-control" name="search" placeholder="Search values" autocomplete="off" autofocus="autofocus" type="text">
                </div>
            </div>
            <div class="panel-body scrolling corpus-annotation-attributes-table-panel-body">
                <div class="administration-table-wrapper corpus-annotation-attributes-table-wrapper">
                <table id="attribute-values" class="table table-striped table-hover administration-table corpus-annotation-attributes-table">
                    <thead>
                    <tr>
                        <th>Value</th>
                        <th class="num corpus-annotation-attributes-count-column">Count</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$attribute_values item=v}
                        <tr>
                            <td class="value">{$v.value}</td>
                            <td class="num">{$v.c}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
                </div>
            </div>
            <div class="panel-footer corpus-annotation-attributes-footer">
                <button id="download-attribute-values" type="button" class="btn btn-primary corpus-annotation-attributes-download-btn">
                    <i class="fa fa-download" aria-hidden="true"></i>
                    <span>Export CSV</span>
                </button>
            </div>
        </div>
    </div>

    <div class="col-md-6 scrollingWrapper corpus-annotation-attributes-main-column">
        <div class="panel administration-content-panel corpus-annotation-attributes-panel">
            <div class="panel-heading administration-content-heading">
                <span class="administration-content-heading-icon"><i class="fa fa-list-ul" aria-hidden="true"></i></span>
                <span>Annotations with the selected value</span>
            </div>
            <div class="panel-body scrolling corpus-annotation-attributes-table-panel-body" id="panelAnnotations">
                <div id="annotations-loading" class="administration-wsd-loading corpus-annotation-attributes-loading" style="display: none;">
                    <img src="gfx/ajax.gif" alt="Loading"/>
                    <span>Loading annotations...</span>
                </div>
                <div class="administration-table-wrapper corpus-annotation-attributes-table-wrapper">
                <table id="annotations" class="table table-striped table-hover administration-table corpus-annotation-attributes-annotations-table">
                    <thead>
                    <tr>
                        <th class="corpus-annotation-attributes-id-column">Id</th>
                        <th class="corpus-annotation-attributes-type-column">Type</th>
                        <th>Text</th>
                        <th class="corpus-annotation-attributes-lemma-column">Lemma</th>
                        <th class="corpus-annotation-attributes-document-column">Document</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr class="corpus-annotation-attributes-empty-row">
                            <td colspan="5"><div class="corpus-annotation-attributes-empty">Choose an attribute value</div></td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

    <form method="get" action="index.php" class="col-md-3 scrollingWrapper corpus-annotation-attributes-config-column">
        <input type="hidden" name="corpus" value="{$corpus.id}"/>
        <input type="hidden" name="page" value="{$page}"/>
        <div class="panel administration-content-panel corpus-annotation-attributes-panel corpus-annotation-attributes-config-panel">
            <div class="panel-heading administration-content-heading">
                <span class="administration-content-heading-icon"><i class="fa fa-sliders" aria-hidden="true"></i></span>
                <span>View configuration</span>
            </div>
            <div class="panel-body corpus-annotation-attributes-config-body">
                <div class="corpus-annotation-attributes-config-fields">
                    <div class="form-group">
                        <label for="annotation-attribute">Shared attribute</label>
                        <select id="annotation-attribute" name="attribute_id" class="form-control">
                            <option value="" {if ""==$attribute_id}selected="selected"{/if}>All</option>
                            {foreach from=$attributes item=attribute}
                                <option value="{$attribute.id}" {if $attribute.id==$attribute_id}selected="selected"{/if}>{$attribute.description} — {$attribute.name}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="language">Language</label>
                        <select id="annotation-language" name="language" class="form-control">
                            <option value="" {if ""==$language}selected="selected"{/if}>All</option>
                            {foreach from=$languages item=lang}
                                <option value="{$lang.code}" {if $lang.code==$language}selected="selected"{/if}>{$lang.language}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="annotation-subcorpus">Subcorpus</label>
                        <select id="annotation-subcorpus" name="subcorpus_id" class="form-control">
                            <option value="" {if ""==$subcorpus_id}selected="selected"{/if}>All</option>
                            {foreach from=$subcorpora item=subcorpus}
                                <option value="{$subcorpus.subcorpus_id}" {if $subcorpus.subcorpus_id==$subcorpus_id}selected="selected"{/if}>{$subcorpus.name}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="corpus-annotation-attributes-config-actions">
                        <button type="submit" class="btn btn-primary corpus-annotation-attributes-apply-btn">
                            <i class="fa fa-check" aria-hidden="true"></i>
                            <span>Apply</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>
</div>

{include file="inc_footer.tpl"}

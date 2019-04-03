{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}

<div class="row" style="margin-top: 20px">

    <div class="col-md-3 scrollingWrapper">
        <div class="panel panel-primary">
            <div class="panel-heading">Attribute values assigned to annotations</div>
            <div class="panel-body">
                <div class="form-group" style="display:inline;">
                    <div class="input-group" style="display:table;">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span>
                        <input class="form-control" name="search" placeholder="Search Here" autocomplete="off" autofocus="autofocus" type="text">
                    </div>
                </div>
            </div>
            <div class="panel-body scrolling" style="padding: 0">
                <table id="attribute-values" class="table table-striped">
                    <thead>
                    <th>Value</th>
                    <th class="num">Count</th>
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
            {if $attribute_id}
                <div class="panel-footer" style="text-align: right">
                    <button id="download-attribute-values" type="button" class="btn btn-primary"><span class="glyphicon glyphicon-download"></span> Download</button>
                </div>
            {/if}
        </div>
    </div>

    <div class="col-md-6 scrollingWrapper">
        <div class="panel panel-primary">
            <div class="panel-heading">Annotations with the selected value</div>
            <div class="panel-body scrolling" style="padding: 0" id="panelAnnotations">
                <table id="annotations" class="table table-striped">
                    <thead>
                    <th>Id</th>
                    <th>Type</th>
                    <th>Text</th>
                    <th>Lemma</th>
                    <th>Document</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <form method="get" action="index.php">
        <input type="hidden" name="corpus" value="{$corpus.id}"/>
        <input type="hidden" name="page" value="{$page}"/>
        <div class="col-md-3 scrollingWrapper">
            <div class="panel panel-info">
                <div class="panel-heading">View configuration</div>
                <div class="panel-body scrolling">
                    <div class="form-group">
                        <label for="attribute_id">Shared attribute</label>
                        <select id="annotation-attribute" name="attribute_id" class="form-control">
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
                </div>
                <div class="panel-footer">
                    <input type="submit" class="btn btn-primary btn-sm" value="Apply">
                </div>
            </div>
        </div>
    </form>

</div>

{include file="inc_footer.tpl"}
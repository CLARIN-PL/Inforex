{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}

<div class="panel panel-primary">
    <div class="panel-heading">Annotation attribute browser</div>
    <div class="panel-body" style="padding: 0">
        <div style="background: #eee; border-bottom: 1px solid #aaa; padding: 5px;">
            <form method="GET" action="index.php">
                <input type="hidden" name="page" value="{$page}"/>
                <input type="hidden" name="corpus" value="{$corpus.id}"/>

                <div class="filter header">
                    <span><b>Filters:</b></span>
                </div>

                <div class="filter" style="margin-top: 3px;">
                    <span>Attribute:</span>
                    <select id="annotation-attribute" name="attribute_id">
                        {foreach from=$attributes item=attribute}
                            <option value="{$attribute.id}" {if $attribute.id==$attribute_id}selected="selected"{/if}>{$attribute.description} — {$attribute.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="filter apply">
                    <input type="submit" class="btn btn-primary btn-sm" value="Apply">
                </div>

                <div style="clear: both;"></div>
            </form>
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-3">
                <div class="panel panel-default scrollingWrapper">
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
            <div class="col-md-9">
                <div class="panel panel-default scrollingWrapper">
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
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}
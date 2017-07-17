{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<div class="container-fluid admin_tables">
    <div class="row">
        <div class="col-md-6 scrollingWrapper" style="padding: 0">
            <div class="panel panel-primary tableContainer" id="sharedAttributesContainer" style="margin: 5px;">
                <div class="panel-heading">Shared attributes</div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
                        <table id="sharedAttributesTable" class="table table-striped" cellspacing="1">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>name</th>
                                <th>type</th>
                                <th>description</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach from=$sharedAttributes item=shared_attribute}
                                <tr>
                                    <td>{$shared_attribute.id}</td>
                                    <td>{$shared_attribute.name}</td>
                                    <td>{$shared_attribute.type}</td>
                                    <td>{$shared_attribute.description}</td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer" element="annotation_subset" parent="annotationSetsContainer">
                    <button type = "button" id="create_shared_attribute"  class = "btn btn-primary">Create</button>
                    <button style = "display: none;" id="delete_shared_attribute" type = "button" class = "btn btn-primary">Delete</button>
                </div>
            </div>

            <div class="panel panel-primary tableContainer" id="annotationTypesAttachedContainer" style="margin: 5px;">
                <div class="panel-heading">Annotation types attached</div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
                        <table id="annotationTypesAttachedTable" class="table table-striped" cellspacing="1">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>name</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer">
                    <button id="move_detach" type = "button" class = "btn btn-primary move unassign"> >>> </button>
                </div>
            </div>
        </div>

        <div class="col-md-6 scrollingWrapper" style="padding: 0">
            <div class="panel panel-primary tableContainer" id="sharedAttributesEnumContainer" style="margin: 5px; visibility: visible;">
                <div class="panel-heading">Shared attribute values</div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
                        <table id="sharedAttributesEnumTable" class="table table-striped" cellspacing="1">
                            <thead>
                                <tr>
                                    <th>value</th>
                                    <th>description</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer" element="annotation_type" parent="annotationSubsetsContainer">
                    <button style = "display: none;" type = "button" id="create_shared_attribute_enum" class = "btn btn-primary">Create</button>
                    <button style = "display: none;" type = "button" id="delete_shared_attribute_enum" class = "btn btn-primary">Delete</button>
                </div>
            </div>

            <div class="panel panel-primary tableContainer" id="annotationTypesDetachedContainer" style="margin: 5px; visibility: visible;">
                <div class="panel-heading">Annotation types detached</div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
                        <table id="annotationTypesDetachedTable" class="table table-striped" cellspacing="1">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>name</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer">
                    <button id="move_attach" type = "button" class = "btn btn-primary"> <<< </button>
                </div>
            </div>
        </div>
    </div>
</div>
{include file="inc_footer.tpl"}

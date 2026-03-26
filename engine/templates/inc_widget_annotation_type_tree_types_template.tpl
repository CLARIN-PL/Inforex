{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}
{* HTML template to generate dynamically row for annotation type row
    typeRowTpl
   Parameters to set in each row by JS code:
	typeRowId - set checkboxa.name as "typeId-".typeRowId
	            - set typeid attr in tr tag .typelayerRow 
	typeRowName - set as content in .layerName element
*}
<template id="typeRowTpl">
                                        <tr class="typelayerRow" typeid="typeRowId" style="display:none">
                                                <td style="vertical-align: middle;" class="layersList">
                                                        <span class="ui-icon ui-icon-carat-1-sw" style="float:left"></span>
                                                        <span class="ui-icon ui-icon-carat-1-sw" style="float:left"></span>
                                                        <span class="layerName" style="margin-left:20px;clear:both;font-weight:normal;">typeRowName</span>
                                                </td>
                                        <td style="vertical-align: middle;text-align:center">
                                                <input name="typeId-typeRowId" type="checkbox" class="leftLayer type_cb" />
                                        </td>
                                        </tr>
</template> 

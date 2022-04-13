{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}
{* HTML template to generate dynamically row for annotation types subset row
    subsetRowTpl
   Parameters to set in each row by JS code:
	subsetRowId - set checkboxa.name as "subsetId-".subsetRowId
	            - set subsetid attr in tr tag .sublayerRow 
	setRowName - set as content in .layerName element
*}
<template id="subsetRowTpl">
                                        <tr class="sublayerRow" subsetid="subsetRowId" style="display:none">
                                                <td style="vertical-align: middle;" class="layersList">
                                                        <span class="count" title="Number of selected annotation types from this subset" style="float: right; font-size: 10px; color: #445967; font-weight: normal;"></span>
                                                        <span class="ui-icon ui-icon-carat-1-sw" style="float:left"></span>
                                                        <span class="toggleSubLayer ui-icon ui-icon-circlesmall-plus" style="float:left"></span>
                                                        <span class="layerName" style="margin-left:10px;clear:both">subsetRowName</span>
                                                </td>
                                        <td style="vertical-align: middle;text-align:center">
                                                <input name="subsetId-subsetRowId" type="checkbox" class="subset_cb" />
                                        </td>
                                        </tr>
</template> 

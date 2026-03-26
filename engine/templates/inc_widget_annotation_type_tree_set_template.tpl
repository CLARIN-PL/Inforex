{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}
{* HTML template to generate dynamically row for annotation types set row
    setRowTpl
   Parameters to set in each row by JS code:
	setRowId - set in checkbox.name as "layerId-".setRowId
	         - not originally set in place of group.id  ?!?
	setRowName - set as content in .layerName element
*}
<template id="setRowTpl">
                            <tr class="layerRow hiddenRow" setid="">
                                <td style="vertical-align: middle;" class="layersList">
                                        <span class="count" title="Number of selected annotation types from this layer" style="float: right; font-size: 10px; color: #445967; font-weight: normal;"></span>
                                        <span class="toggleLayer ui-icon ui-icon-circlesmall-plus" style="float:left"></span>
                                        <span class="layerName" style="clear:both">setRowName</span>
                                </td>
                                <td style="vertical-align: middle;text-align:center">
                                        <input name="layerId-setRowId" type="checkbox" class="group_cb" />
                                </td>
                            </tr>
</template> 

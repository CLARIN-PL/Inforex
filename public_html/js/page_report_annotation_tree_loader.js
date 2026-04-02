/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var url = $.url(window.location.href);
var corpus_id = url.param("corpus");
var annotationTypeTreeData = {};

function getAnnotationTypeTreeSetData(setId) {
	return annotationTypeTreeData && annotationTypeTreeData[setId]
		? annotationTypeTreeData[setId]
		: null;
}

function isAnnotationTypeTreeSelectionChecked(selectionType, entityId) {
	if (typeof annotationTypeTreeSelection === "undefined" || !annotationTypeTreeSelection[selectionType]) {
		return false;
	}
	return annotationTypeTreeSelection[selectionType][String(entityId)] === true;
}

function createSetRowFromTemplate(container,setId,setName) {

	/* from HTML template in DOM named id='setRowTpl'
	 * creates code of set row for parametres setId,setName
	 * and place it on the end of container element
      	*/
        const setRowTemplateId = "setRowTpl";
        let setRow = null;
        const template = document.querySelector("#"+setRowTemplateId);
        if(template) {
                setRow = template.content.cloneNode(true);
                if(setRow){
                        setRow.querySelector("tr").setAttribute("setid", setId);
                        setRow.querySelector("tr").setAttribute("data-children-loaded", "0");
                        setRow.querySelector("input").setAttribute("name","layerId-"+setId);
                        setRow.querySelector("input").checked = isAnnotationTypeTreeSelectionChecked("layers", setId);
                        setRow.querySelector(".layerName").innerText = setName;
                        if(container) {
                                container.append(setRow);
                        }
                } else {
                        console.log("Cannot clone DOM element for template with id="+setRowTemplateId);
                }
        } else {
                console.log("Cannot create DOM element from template with id="+setRowTemplateId);
        }

        return setRow;
} // createSetRowfromTemplate()

function createSubsetRowFromTemplate(container,subsetId,subsetName) {

        /* from HTML template in DOM named id='subsetRowTpl'
         * creates code of subset row for parametres subsetId, subsetName
 	 * and place it on the end of container element                       
	 */
        const subsetRowTemplateId = "subsetRowTpl";
        let subsetRow = null;
        const template = document.querySelector("#"+subsetRowTemplateId);
        if(template) {
                subsetRow = template.content.cloneNode(true);
                if(subsetRow){
			subsetRow.querySelector("input").setAttribute("name","subsetId-"+subsetId);
                        subsetRow.querySelector("input").checked = isAnnotationTypeTreeSelectionChecked("subsets", subsetId);
                        subsetRow.querySelector("tr").setAttribute("subsetid",subsetId);
                        subsetRow.querySelector("tr").setAttribute("data-children-loaded", "0");
                        subsetRow.querySelector(".layerName").innerText = subsetName;
                        if(container) {
                                container.append(subsetRow);
                        }
                } else {
                        console.log("Cannot clone DOM element for template with id="+subsetRowTemplateId);
                }
        } else {
                console.log("Cannot create DOM element from template with id="+subsetRowTemplateId);
        }

        return subsetRow;
} // createSubsetRowfromTemplate()
 
function createTypeRowFromTemplate(container,typeId,typeName) {

        /* from HTML template in DOM named id='typeRowTpl'
         * creates code of type row for parametres typeId, typeName
         * and place it on the end of container element                      
         */
        const typeRowTemplateId = "typeRowTpl";
	const MAX_TYPES_NAME_LABEL = "...";
        let typeRow = null;
        const template = document.querySelector("#"+typeRowTemplateId);
        if(template) {
                typeRow = template.content.cloneNode(true);
                if(typeRow){
			if(typeName==MAX_TYPES_NAME_LABEL) {
                		/* remove checkbox for limit threshold */
                		elem = typeRow.querySelector("input");
                		if(elem) {
                        		elem.parentElement.removeChild(elem);
                		}
        		} else { /* type under limit */
				typeRow.querySelector("input").setAttribute("name","typeId-"+typeId);
                                typeRow.querySelector("input").checked = isAnnotationTypeTreeSelectionChecked("types", typeId);
			}
                        typeRow.querySelector("tr").setAttribute("typeid",typeId);
                        typeRow.querySelector(".layerName").innerText = typeName;
                        if(container) {
                                container.append(typeRow);
                        }
                } else {
                        console.log("Cannot clone DOM element for template with id="+typeRowTemplateId);
                }
        } else {
                console.log("Cannot create DOM element from template with id="+typeRowTemplateId);
        }

        return typeRow;
} // createTypeRowFromTemplate()

function insertFragmentAfter(referenceRow, fragment) {
	if (!referenceRow || !referenceRow.parentNode || !fragment) {
		return referenceRow;
	}

	var parent = referenceRow.parentNode;
	var nextSibling = referenceRow.nextSibling;
	parent.insertBefore(fragment, nextSibling);

	var lastInserted = referenceRow;
	if (nextSibling) {
		var previous = nextSibling.previousSibling;
		if (previous) {
			lastInserted = previous;
		}
	} else {
		lastInserted = parent.lastChild;
	}
	return lastInserted;
}

function renderAnnotationTypeTreeSetChildren(setRow) {
	var setId = $(setRow).attr("setid");
	var setData = getAnnotationTypeTreeSetData(setId);
	var lastInserted = setRow;
	var setChecked = $(setRow).find("input.group_cb").prop("checked");

	if (!setData || $(setRow).attr("data-children-loaded") === "1") {
		return;
	}

	$.each(setData, function(subsetId, subsetArray){
		var subsetFragment;
		var subsetRow;
		var subsetChecked;

		if (subsetId === "name") {
			return;
		}

		subsetFragment = createSubsetRowFromTemplate(null, subsetId, subsetArray.name);
		lastInserted = insertFragmentAfter(lastInserted, subsetFragment);
		subsetRow = $(lastInserted);
		subsetChecked = setChecked || subsetRow.find("input.subset_cb").prop("checked");
		subsetRow.find("input.subset_cb").prop("checked", subsetChecked);
	});

	$(setRow).attr("data-children-loaded", "1");
	annotationTypeTreeUpdateCounts();
}

function renderAnnotationTypeTreeSubsetChildren(subsetRow) {
	var subsetId = $(subsetRow).attr("subsetid");
	var setId = $(subsetRow).prevAll(".layerRow:first").attr("setid");
	var setData = getAnnotationTypeTreeSetData(setId);
	var subsetData = setData && setData[subsetId] ? setData[subsetId] : null;
	var lastInserted = subsetRow;
	var parentSetChecked = $(subsetRow).prevAll(".layerRow:first").find("input.group_cb").prop("checked");
	var subsetChecked = $(subsetRow).find("input.subset_cb").prop("checked");

	if (!subsetData || $(subsetRow).attr("data-children-loaded") === "1") {
		return;
	}

	$.each(subsetData, function(typeId, typeName){
		var typeFragment;
		var typeRow;
		var typeChecked;

		if (typeId === "name") {
			return;
		}

		typeFragment = createTypeRowFromTemplate(null, typeId, typeName);
		lastInserted = insertFragmentAfter(lastInserted, typeFragment);
		typeRow = $(lastInserted);
		typeChecked = parentSetChecked || subsetChecked || typeRow.find("input.type_cb").prop("checked");
		typeRow.find("input.type_cb").prop("checked", typeChecked);
	});

	$(subsetRow).attr("data-children-loaded", "1");
	annotationTypeTreeUpdateCounts();
}
 
/**
 *  loads rows with sets, subsets & types of annotations from templates
 *  and pure JSON data from ajax request
 */
function loadAnnotationTypesFromTemplates(corpus_id) {

	// loading all data from ajax service
	const serviceName = "annotation_type_tree";
	const getTypesSuccess = function(data) {
		var treeData = data;
		if (data && data.tree) {
			treeData = data.tree;
		}

		// create rows for sets, subsets and types
		if(!treeData){ return; }
		if(treeData.length==0){ return; }
		annotationTypeTreeData = treeData;
		// element, which holds all rows
		const container = $(".annotationTypesTree");
		// remove default content for empty set
		$(container).find("tr").remove();
		// array of setId => set_array, 
		$.each(treeData,function(setId,setArray){
			const setName = setArray.name;
			createSetRowFromTemplate(container,setId,setName);
		}); // each data

	};
        const getTypesError = function() {
                console.log("Error on ajax request for "+serviceName);
        };
        doAjaxSync( serviceName ,
		{ 'corpusId' : corpus_id },
                getTypesSuccess,
                getTypesError,
                null  // getTypesComplete
        );
 	
} // loadAnnotationTypesFromTemplates()

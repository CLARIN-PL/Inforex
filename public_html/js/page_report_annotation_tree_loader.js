/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var url = $.url(window.location.href);
var corpus_id = url.param("corpus");

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
                        setRow.querySelector("input").setAttribute("name","layerId-"+setId);
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
                        subsetRow.querySelector("tr").setAttribute("subsetid",subsetId);
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

} // createTypeRowFromTemplate()
 
/**
 *  loads rows with sets, subsets & types of annotations from templates
 *  and pure JSON data from ajax request
 */
function loadAnnotationTypesFromTemplates(corpus_id) {

	// loading all data from ajax service
	const serviceName = "annotation_type_tree";
	const getTypesSuccess = function(data) {

		// create rows for sets, subsets and types
		if(!data){ return; }
		if(data.length==0){ return; }
		// element, which holds all rows
		const container = $(".annotationTypesTree");
		// remove default content for empty set
		$(container).find("tr").remove();
		// array of setId => set_array, 
		$.each(data,function(setId,setArray){
			const setName = setArray.name;
			createSetRowFromTemplate(container,setId,setName); 
			$.each(setArray,function(subsetId,subsetArray){
				if(subsetId!='name'){
					const subsetName = subsetArray.name;
					createSubsetRowFromTemplate(container,subsetId,subsetName);
					$.each(subsetArray,function(typeId,typeName){
						if(typeId!='name'){
							createTypeRowFromTemplate(container,typeId,typeName);
						}; // typeId<>'name'	
					}); // each subsetArray
				}; // subsetId <> 'name'
			}); // each setArray
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


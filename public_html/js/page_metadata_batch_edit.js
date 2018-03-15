var url = $.url(window.location.href);
var corpus_id = url.param('corpus');
var metadata_regex = [];
var metadata_user_regex = [];

$(function() {
    getDocumentsWithMetadata();
    loadMetadataFromFilename();
});

function fieldTokenSelected(){
    var field = $(".field_select").val();
    var token = $(".token_select").val();

    if(field === "null" || token === "null"){
        return false;
    } else{
        $(".field_select option[value='"+field+"']").remove();
        $(".field_select").val("null");
        $(".token_select").val("null");
        return {
            'field': field,
            'token': token
        };
    }
}

function getMetadata(metadata){
    var metadata_str = "";
    metadata.forEach(function(value){
        metadata_str += value;
    });

    return metadata_str;
}


function loadMetadataFromFilename(){
    $(".continue_metadata").click(function(){
        var selection = fieldTokenSelected();
        if(selection !== false){
            var regex_user_friendly = "["+selection.field+"]["+selection.token+"]";
            metadata_user_regex.push(regex_user_friendly);

            var regex = "([^"+selection.token+"]+)["+selection.token+"]";
            metadata_regex.push(regex);

            $(".regex_user_friendly").val(getMetadata(metadata_user_regex));
        }
    });

    $(".back_metadata").click(function(){
        metadata_user_regex.pop();
        metadata_regex.pop();

        $(".regex_user_friendly").val(getMetadata(metadata_user_regex));
    });

}

/**
 * Returns an array of metadata columns to use as column names for HeadsOnTable.
 * @param $columns
 */
function getMetadataColumnNames(columns){
    console.log(columns);
    var columnOrder = [];
    var columnHeaders = [];

    //Returns true if string is not empty.
    var notEmptyValidator = /^(?!\s*$).+/;

    columns.forEach(function (value, i) {
        var field_name = value['field'];
        //Makes the report_id field read-only.
        if(field_name === "report_id"){
            var data = {data: field_name,
                        readOnly: true};
        } else{
            var data = {
                data: field_name
            };

            //Adds a dropdown with accepted values for enumeration
            if(value['type'] === 'enum'){
                data.type = 'dropdown';
                data.source = [];
                value['field_values'].forEach(function(val){
                    data.source.push(val);
                });
            }
            //Prevents empty values in NOT NULL cells.
            if(value['null'] === "No"){
                data.validator = notEmptyValidator;
                data.allowInvalid = false;
            }
        }
        columnOrder.push(data);
        columnHeaders.push(field_name);
    });
    var columnData = {
        'columnHeaders': columnHeaders,
        'columnOrder': columnOrder
    };
    console.log(columnData);
    return columnData;
}

/**
 * Retrieves the list of metadata with their metadata.
 */
function getDocumentsWithMetadata(){
    var data = {
        'corpus_id': corpus_id
    };

    var success = function(data) {
        var colData = getMetadataColumnNames(data.columns);
        console.log(data);
        console.log(colData);
        generateMetadataTable(data.documents, colData.columnHeaders, colData.columnOrder);
    };

    doAjax("metadata_batch_edit_get", data, success);
}

function generateMetadataTable(data, colHeaders, columnOrder){
    var container = $('#hot-container')[0];
    var searchFiled = $('#search_field')[0];

    var hot = new Handsontable(container, {
        data: data,
        rowHeaders: true,
        colHeaders: colHeaders,
        filters: true,
        columnSorting: true,
        sortIndicator: true,
        wordWrap: false,
        manualColumnResize: true,
        modifyColWidth: function(width, col){
            if(width > 500){
                return 200
            }
        },
        search: true,
        columns: columnOrder,
        afterChange: function (change, source) {
            if(change !== null){
                // /console.log(change);
                var changedDocs = {
                    'docs': [],
                    'corpus_id': corpus_id
                };
                change.forEach(function(value){
                    var row = {};
                    row.value = value[3];
                    row.report_id = data[value[0]].report_id;
                    row.field = value[1];
                    changedDocs.docs.push(row);
                });

                console.log(changedDocs);
                var success = function() {
                    document.body.style.cursor='default'
                };

                //doAjax("metadata_batch_edit_update", changedDocs, success);
                document.body.style.cursor='wait';
            }
        }
    });

    Handsontable.dom.addEvent(searchFiled, 'keyup', function (event) {
        var queryResult = hot.search.query(this.value);

        console.log(queryResult);
        hot.render();
    });
}
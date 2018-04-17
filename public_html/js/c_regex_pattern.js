class PatternEditor{

    constructor(patternBoxSelector, previewBoxSelector, resultsBoxSelector, fields, separators){
        this.pattern = this.getOrError(patternBoxSelector);
        this.preview = this.getOrError(previewBoxSelector);
        this.results = this.getOrError(resultsBoxSelector);
        this.fields = fields;
        this.separators = separators;
        this.fieldTypes = this.getFieldTypes();

        this.initPattern();
        this.initPreview();

        var th = this;
        this.pattern.on('click', 'span.glyphicon-trash', function(){
            $(this).closest("tr").remove();
            th.update();
        });

        this.pattern.find("tbody").sortable({
            update: function(event,ui){
                th.update();
            }
        });

        this.pattern.on('change', 'select.value', function(){
            var input = $(this).closest("tr").find("input.regex");
            var delimiter = $(this).closest("tr").find("select.token");
            var type = $(this).val();
            if ( type in th.fieldTypes ){
                var fieldType = th.fieldTypes[type];
                input.val(fieldType.regex);
                input.attr("disabled", "disabled");
                if ( fieldType.delimiter ){
                    delimiter.removeAttr("disabled");
                } else {
                    delimiter.attr("disabled", "disabled");
                    delimiter.val("");
                }
            } else {
                input.removeAttr("disabled");
                delimiter.attr("disabled", "disabled");
            }
        });

        this.pattern.on('change', 'select.token', function(){
            var delimiter = $(this).val();
            if (delimiter){
                $(this).closest("tr").find("input.regex").val("[^" + delimiter + "]+")
            }
        });

        this.pattern.on('change', 'select', function(){
            th.update();
        });

        this.pattern.on('change', 'input[type=checkbox]', function(){
            th.update();
        });

    }

    initPattern(){
        var html = '<table class="table table-striped pattern-editor-elements">'+
            '<thead><tr>'+
            '<th>Order</th>'+
            '<th>Field</th>'+
            '<th>Value</th>'+
            '<th>Delimiter</th>'+
            '<th>Pattern</th>'+
            '<!--<th>Transformation</th>-->'+
            '<th class="col-optional">Optional</th>'+
            '<th class="col-action">Action</th>'+
            '</tr>'    +
            '</thead><tbody></tbody></table>';
        this.pattern.html(html);
    }

    initPreview(){
        var html = '<table class="table table-striped pattern-editor-match">'+
            '<thead>'+
            '<tr>'+
            '<th>Filename</th> '  +
            '</tr>'+
            '</thead>'+
            '<tbody>'+
            '</tbody>'+
            '</table>';
        this.results.html(html);
    }

    getFieldTypes(){
        var fieldTypes = {};
        fieldTypes.delimiter = {name: "Any sequence to a delimiter", regex: ".+", delimiter: true};
        fieldTypes.alphanumeric = {name: "Alphanumeric", regex: "[a-zA-Z0-9]+", delimiter: false};
        fieldTypes.any = {name: "Any", regex: ".+", delimiter: false};
        fieldTypes.alphabet = {name: "Alphabet", regex: "[a-zA-Z]+", delimiter: false};
        fieldTypes.numeric = {name: "Numeric", regex: "[0-9]+", delimiter: false};
        return fieldTypes;
    }

    getOrError(selector){
        var o = $(selector);
        if (o.length == 0) { throw "Element not found for selector '" + selector + "'"; }
        if (o.length > 1) { throw "More than one element found for '" + selector + "'"; }
        return o;
    }

    setPreviewData(rows){
        this.results.find("tbody tr").remove();
        this.results.find("thead th.field").remove();
        var html = "";
        $.each(rows, function(index, value){
            html += "<tr><th>" + value + "</th></tr>";
        })
        this.results.find("tbody").html(html);
    }

    addPatternRow(field, fieldType, delimiter){
        var row = "<tr>";
        row += '<td class="col-action"><span class="glyphicon glyphicon-resize-vertical" aria-hidden="true"></span></td>';
        row += "<td>";
        row += '<select name="field_select" class="form-control field">';
        row += '<option value="">-select-</option>';
        row += '<option value="_ignore">Ignore sequence</option>';
        $.each(this.fields, function(index, value){
            row += '<option value="'+value.key+'">'+value.value+'</option>';
        })
        row += '</select>';
        row += "</td>";
        // ---------------
        row += "<td>";
        row += '<select name="field_select" class="form-control value">';
        $.each(this.fieldTypes, function(key, value){
            row += '<option value="'+key+'">'+value.name+'</option>';
        })
        row += '<option value="custom">Custom pattern</option>';
        row += '</select>';
        row += "</td>";
        // ---------------
        row += "<td>";
        row += '<select name="token_select" class="form-control token">';
        row += '<option value="">none</option>';
        $.each(this.separators, function(index, value){
            row += '<option value="'+value.key+'">'+value.value+'</option>';
        })
        row += '</select>';
        row += "</td>";
        // ---------------
        row += "<td>";
        row += '<input type="text" class="form-control regex" value=""/>';
        row += "</td>";
        // ---------------
        /*     row += "<td>";
         row += '<select name="token_select" class="form-control transformation">';
         row += '<option value="">-select-</option>';
         row += '</select>';
         row += '</td>';
         */
        // ---------------
        row += "<td class='col-optional'>";
        row += '<input type="checkbox" class="optional" value=""/>';
        row += "</td>";
        // ---------------
        row += '<td class="col-action"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></td>';
        // ---------------
        row += '</tr>';

        this.pattern.find("tbody").append(row);
        this.pattern.find("tbody tr:last select.field").val(field);
        this.pattern.find("tbody tr:last select.value").val(fieldType);
        this.pattern.find("tbody tr:last select.token").val(delimiter);
    }

    render(){
        this.update();
        this.pattern.find("select.value").trigger("change");
        this.pattern.find("select.token").trigger("change");
    }

    update(){
        var pattern = this.getPattern();
        this.updatePreview(pattern);
        this.updateResult(pattern);
    }

    getPattern(){
        var pattern = [];
        this.pattern.find("tbody tr").each(function(){
            var label = $(this).find("select.transformation option:selected").text();
            var regex = $(this).find("input.regex").val();
            var field = $(this).find("select.field option:selected").text();
            var fieldId = $(this).find("select.field option:selected").val();
            var delimiter = $(this).find("select.token option:selected").val();
            var optional = $(this).find("input.optional").is(':checked');
            var patternRegex = "(" + regex + ")" + (optional?"?":"");
            pattern.push({fieldId: fieldId, field: field, regex: patternRegex, delimiter: delimiter != ""  ? "[" + delimiter + "]" : ""});
        });
        return pattern;
    }

    updatePreview(pattern){
        var rowPatterns = [];
        var rowFields = [];
        $.each(pattern, function(i,v){
            rowPatterns.push("<td class='regex'>" + v.regex + "</td>")
            rowFields.push("<td class='field " + v.fieldId + "' label=''>" + v.field  + "</td>");
            if (v.delimiter){
                rowPatterns.push("<td class='delimiter'>" + v.delimiter + "</td>");
                rowFields.push("<td></td>");
            }
        });
        var html = '<table class="pattern-editor-preview">';
        html += "<tr>" + rowPatterns.join("") + "</tr>";
        html += "<tr>" + rowFields.join("") + "</tr>";
        html += "</table>";
        this.preview.html(html);
    }

    updateResult(pattern){
        var re = new RegExp($.map(pattern, function(v,i){return v.regex+v.delimiter}).join(""));
        var header = "<th>Filename</th>" + $.map(pattern, function(v,i){return "<th class='field'>"+v.field+"</th>"}).join();
        this.results.find("tr.error").removeClass("error");
        this.results.find("thead").html(header);
        this.results.find("tbody tr").each(function(){
            var filename = $(this).find("th").text();
            var m = re.exec(filename);
            var cols = Array(pattern.length).fill('');
            if (m !== null){
                for (var i=1; i<m.length; i++){
                    cols[i-1] = m[i] === undefined ? '' : m[i];
                }
            } else {
                $(this).addClass("error");
            }
            $(this).html("<th>"+filename+"</th>" + $.map(cols, function(value, index){return "<td>" + value + "</td>"}).join());
        });
    }

}
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

/**
 * @param selector String selector of panel with relation data
 * @param selectorContent String selector of panel with the document content
 */
function WidgetAnnotationRelations(selector, selectorContent) {
	this.box = $(selector);
	this.content =  $(selectorContent);
	var parent = this;
	$(this.box).find(".relation-cancel").click(function(){
        parent.box.find(".relation-cancel").hide();
        parent.box.find(".relation-types").show();
        parent.content.find("span.new-relation-target").removeClass("new-relation-target");
	});
}

/**
 *
 * @param annotationSpan
 */
WidgetAnnotationRelations.prototype.set = function(annotationSpan){
	if ( annotationSpan == null ){
		this.span = null;
		this.id = null;
	} else {
	    this.box.LoadingOverlay("show");
        this.box.LoadingOverlay("show");
		this.span = $(annotationSpan);
        this.id = $(annotationSpan).attr("id").replace("an", "");
        var parent = this;
        $(this.box).find(".relation-types li").remove();
        var annotation_mode = $.cookie("annotation_mode");
        doAjax("report_get_annotation_relation_types", {annotation_id: parent.id},
            function (data) {
                var list = parent.box.find(".relation-types");
                var setName = "";
                if(data.length === 0){
                    $(list).find("ul").append("<li class = 'li_error'>No relations available.</li>");
                } else{
                    $.each(data, function (index, item) {
                        if ( setName != item['set_name'] ){
                            if ( setName != "" ){
                                $(list).find("ul").append('<li class="divider"></li>');
                            }
                            $(list).find("ul").append('<li class="dropdown-header">' + item['set_name'] + '</li>');
                        }
                        $(list).find("ul").append('<li><a href="#" title="' + item['description'] + '" item-id="' + item['id'] + '">' + item['name'] + '</a></li>')
                        setName = item['set_name'];
                    });
                }
                $(list).find("a").click(function () {
                    $(list).hide();
                    parent.box.find(".relation-cancel").show();
                    parent.content.find("span.annotation").addClass("new-relation-target");
                    parent.relationTypeId = $(this).attr("item-id");
                });
                parent.box.LoadingOverlay("hide");
            },
            function (data) {
                parent.box.LoadingOverlay("hide");
            });
	console.log('widget ajax ENTER');
        doAjax("report_get_annotation_relations", {annotation_id: parent.id, annotation_mode: annotation_mode},
            function (data) {
		console.log('Jest OK.');
                var table = parent.box.find("table.relations tbody");
                $(table).find("tr").remove();
                $.each(data, function (index, item) {
                    //item['target_id']
                    var row = "<tr item-id='" + item['id'] + "'>";
                    row += "<td>" + item['id'] + "</td>";
                    row += "<td>" + item['name'] + "</td>";
                    row += "<td>" + item['text'] + "</td>";
                    row += '<td><a href="#" title="Delete relation" class="delete-relation" data-content="Do you want to delete the relation?">' +
                        '<i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                    row += "</tr>";
                    $(table).append(row)
                });

                $(table).find(".delete-relation").confirmation(
                    {   title: 'Delete relation?:::',
                        placement: "left",
                        onConfirm: function(){
                            var tr = $(this).closest("tr");
                            var relationId = tr.attr("item-id");
                            tr.find(".delete-relation").hide();
                            tr.find(".delete-relation").after("<img src='gfx/ajax.gif'/>");
                            doAjax("report_delete_annotation_relation", {relation_id : relationId},
                                function(){
                                    tr.fadeOut(400);
                                },
                                function(){
                                    tr.find(".delete-relation").show();
                                    tr.find(".delete-relation").next().remove();
                                });
                            $(this).closest("tr").fadeOut(400);
                        }
                    });
                parent.box.LoadingOverlay("hide");
            },
            function (data) {
		console.log('Qpa');
                parent.box.LoadingOverlay("hide");
            });
    }
};

/**
 * Check if the widget is in the mode of new relation creation.
 */
WidgetAnnotationRelations.prototype.isNewRelationMode = function(){
	return $(this.box).find(".relation-cancel").is(":visible");
};

/**
 *
 * @returns {*|jQuery}
 */
WidgetAnnotationRelations.prototype.createRelation = function(annotationSpan){
    var parent = this;
    var sourceId = this.span.attr("id").replace("an", "");
    var targetId = $(annotationSpan).attr("id").replace("an", "");
    var relationTypeId = this.relationTypeId;
    var workingMode = $.cookie("annotationMode");
    var params = {
        source_id : sourceId,
        target_id : targetId,
        relation_type_id : relationTypeId,
        working_mode: workingMode
    };
    console.log('WidgetAnnotationRelations.createRelation typu '+relationTypeId+' from '+sourceId+' to '+targetId+' mode='+workingMode );

    doAjax("report_add_annotation_relation", params,
        function(data){
            parent.box.find(".relation-cancel").hide();
            parent.box.find(".relation-types").show();
            parent.content.find("span.new-relation-target").removeClass("new-relation-target");

            var source = '<sup class="rel" title="region" sourcegroupid="0" target="284696" targetgroupid="0" targetsubgroupid="0" sourcesubgroupid="0">↷x</sup>';
            var target = '<sup class="relin" targetsubgroupid="0" targetgroupid="0" title="undefined region region">x</sup>';
            var after = parent.span;
            while ( after && after.next().prop("tagName") == "SUP" ){
                after = after.next();
            }
            if ( after ) {
                after.after(source);
            }
            var before = $(annotationSpan);
            if ( $(annotationSpan).prev().prop("tagName") == "SUP" ){
                // Copy annotation identifier
            } else {
                $(annotationSpan).before(target);
            }
        },
        function(data){
            parent.box.find(".relation-cancel").hide();
            parent.box.find(".relation-types").show();
            parent.content.find("span.new-relation-target").removeClass("new-relation-target");
        });
};

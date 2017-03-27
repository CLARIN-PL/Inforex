/**
 * Crates an object to control the widget for relation set selection.
 * @param cssSelector a css selector of the object
 * @param key a custom key to identify the context of selection, for example a corpus id
 * @constructor
 */
function WidgetRelationSetSelector(cssSelector, key){
	this.box = $(cssSelector);
	this.key = key + "_relation_sets";
}

/**
 * Loads the list of selected relation sets from the cookie.
 */
WidgetRelationSetSelector.prototype.load = function() {
	var idsStr = $.cookie(this.key);
	var ids = (idsStr == null ? [] : idsStr.split(","));
	var parent = this;
    $.each(ids, function(index,value){
        $(parent.box).find("input[value="+value+"]").attr("checked", true);
	});
};

/**
 * Saves the current selection of relation sets to the cookie.
 */
WidgetRelationSetSelector.prototype.save = function() {
	var ids = this.get();
	$.cookie(this.key, ids.join(","));
};

/**
 * Returns a list of selected relation sets.
 * @return Array a list of selected relation set identifiers
 */
WidgetRelationSetSelector.prototype.get = function() {
    var ids = new Array();
    $(this.box).find("input[type=checkbox]:checked").each(function(){
        ids.push($(this).val());
    });
	return ids;
};

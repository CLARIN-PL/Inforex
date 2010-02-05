/*
 * Obiekt, wewnątrz którego będzie poruszał się nawigator.
 */
function Navigator(obj){
	obj.children("p:first").prepend("<em>|</em>");
	this._marker = obj.children("em");
}

Navigator.prototype._marker = null;

Navigator.prototype.moveRight = function(){
	//var o = this._marker.next();
	
	//this.left = $(this._marker).context.previousSibling.previousSibling;
	var right = $(this._marker).context;
	
	alert(right.data);
};
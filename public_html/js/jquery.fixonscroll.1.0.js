/*
 jQuery FixOnScroll Plugin :: Version 1.0
 Copyright (c) 2009 Michal Marcinczuk (http://www.czuk.eu)
 Licensed under the MIT license

 Copyright (c) 2009 Michal Marcinczuk

 Permission is hereby granted, free of charge, to any person
 obtaining a copy of this software and associated documentation
 files (the "Software"), to deal in the Software without
 restriction, including without limitation the rights to use,
 copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the
 Software is furnished to do so, subject to the following
 conditions:

 The above copyright notice and this permission notice shall be
 included in all copies or substantial portions of the Software.

 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 OTHER DEALINGS IN THE SOFTWARE.

*/

(function($){
 $.fn.fixOnScroll = function(options) {

  var defaults = {
   position: "top",
  };
  var options = $.extend(defaults, options);
    
  return this.each(function() {
	  var startTopPos = $(this).position().top;
	  var startWidth = $(this).css("width"); 
	  var isFixed = false;
	  var obj = $(this);
	  var idr = obj.attr("id")+"_rep";
	  obj.after("<div id='"+idr+"'></div>");
	  obj.css("top", "0");
	  var rep = $("#"+idr);	  
	  
	  $(window).scroll(function(){
			var top = $(window).scrollTop();
			if (top>=startTopPos){
				if (!isFixed){
					var height = 
					rep.css("height", obj.outerHeight(true));
					var width = obj.width();
					obj.css("position","fixed");
					obj.css("width",""+width+"px");
					isFixed = true;
				}
			}else{
				if (isFixed){
					rep.css("height", "0");
					obj.css("position","");
					obj.css("width", startWidth);
					isFixed = false;
				}
			}
	  });
  });
 };
})(jQuery);




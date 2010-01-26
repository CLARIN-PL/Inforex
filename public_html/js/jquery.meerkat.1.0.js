/*
jQuery Meerkat Plugin :: Version 1.0
Copyright (c) 2009 Jarod Taylor (http://www.jarodtaylor.com)
Licensed under the MIT (license.txt)
*/

function meerkat(options) {
	
	this.settings = {
		showMeerkatOnLoad: 'false',
		close: 'none',
		dontShow: 'none',
		dontShowExpire: 0,
		removeCookie: 'none',
		meerkatPosition: 'bottom',
		animation: 'slide',
		animationSpeed: 'slow',
		height: 'auto',
		background: 'none'
	}

	if(options){
		jQuery.extend(this.settings, options);
	}
	
	var settings = this.settings;
	var cookieExpiration = settings.dontShowExpire;
	
	function createCookie(name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else { 
			var expires = "";
		}
		document.cookie = name+"="+value+expires+"; path=/";
	}
	
	function readCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}
	
	function eraseCookie(name) {
		createCookie(name,"",-1);
	}
	
	jQuery(settings.removeCookie).click(function(){ eraseCookie('meerkat')});
	
	if(readCookie('meerkat') != 'dontshow')
	{
		jQuery('html, body').css({'margin':'0', 'padding':'0', 'height':'100%'});
		jQuery('#meerkat').show().wrap('<div id="meerkat-wrap"><div id="meerkat-container">' + '</div></div>');
	
		jQuery('#meerkat-wrap').css({'position':'fixed', 'width':'100%', 'height': settings.height}).css(settings.meerkatPosition,"0");
		jQuery('#meerkat-container').css({'background': settings.background, 'height': settings.height});
		//Give the close and dontShow elements a cursor (there's no need to use a href)
		jQuery(settings.close+","+settings.dontShow).css({"cursor":"pointer"});
		
		
		if(jQuery.browser.msie && jQuery.browser.version <= 6){
			jQuery('html, body').css({'height':'100%', 'width':'100%', 'overflow':'hidden'});
			jQuery('#meerkat-wrap').css({'position':'absolute', 'bottom':'-1px'});				
			
			jQuery("body").children()
				.filter(function (index) {
					return jQuery(this).attr("id") != "meerkat-wrap";
				})
			.wrapAll('<div id="ie6-content-container">', '</div>');
			jQuery('#ie6-content-container').css({'position':'relative', 'overflow':'auto', 'width':'100%', 'height':'100%'});
			//Check if ie6-content-container has a scrollbar present. If it does we need to move the meerkat container over 17px
			var element = document.getElementById('ie6-content-container');
			if ((element.clientHeight < element.scrollHeight)&&(settings.height != 100+'%')) {
				jQuery('#meerkat-container').css({'margin-right':'17px'});		
			}
			var bodyStyle = document.body.currentStyle;	
			var bodyBgStyles = bodyStyle.backgroundColor +" "+ bodyStyle.backgroundImage +" "+ bodyStyle.backgroundRepeat +" "+ bodyStyle.backgroundAttachment +" "+ bodyStyle.backgroundPositionX +" "+ bodyStyle.backgroundPositionY;
			jQuery('body').css({'background-image' : 'none'});
			jQuery('#ie6-content-container').css({'background' : bodyBgStyles});
		}
		
		if((settings.animation == "slide")&&(settings.showMeerkatOnLoad != "true")){			
			jQuery('#meerkat-wrap').hide().slideDown(settings.animationSpeed);
			jQuery(settings.close).click(function(){
				jQuery("#meerkat-wrap").slideUp();							
			});
			
			jQuery(settings.dontShow).click(function () {
				createCookie('meerkat','dontshow', cookieExpiration);
				jQuery("#meerkat-wrap").slideUp();	
			});
		} else if((settings.animation == "fade")&&(settings.showMeerkatOnLoad != "true")) {
			jQuery('#meerkat-wrap').hide().fadeIn(settings.animationSpeed);
			jQuery(settings.close).click(function(){
				jQuery("#meerkat-wrap").fadeOut(settings.animationSpeed);								
			});
			
			jQuery(settings.dontShow).click(function () {			
				createCookie('meerkat','dontshow', cookieExpiration);
				jQuery("#meerkat-wrap").fadeOut();	
			});	
		} else if ((settings.showMeerkatOnLoad == "true")&&(settings.animation == "slide")){
			jQuery('#meerkat-wrap').show();
			jQuery(settings.close).click(function(){
				jQuery("#meerkat-wrap").slideUp();							
			});
			
			jQuery(settings.dontShow).click(function () {
				createCookie('meerkat','dontshow', cookieExpiration);
				jQuery("#meerkat-wrap").slideUp();	
			});
		} else if ((settings.showMeerkatOnLoad == "true")&&(settings.animation == "fade")){
			jQuery('#meerkat-wrap').show();
			jQuery(settings.close).click(function(){
				jQuery("#meerkat-wrap").fadeOut(settings.animationSpeed);								
			});
			
			jQuery(settings.dontShow).click(function () {			
				createCookie('meerkat','dontshow', cookieExpiration);
				jQuery("#meerkat-wrap").fadeOut();
			});
		}
	} else {
		jQuery("#meerkat").hide();
	}
}
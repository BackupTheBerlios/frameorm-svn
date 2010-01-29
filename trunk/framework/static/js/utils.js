function utils(){}
function $(id)
{
	return document.getElementById(id);
}

utils.queryString = function(key)
{
	q = window.location.search.substring(1);
	gy = q.split("&");
	for (i=0;i<gy.length;i++) 
	{
		ft = gy[i].split("=");
		if (ft[0] == key)
			return ft[1];
	}
}

utils.setOpacity = function(element, fOpacity)
{
	if(navigator.userAgent.indexOf('MSIE') != -1)
		element.style.filter = 'alpha(opacity=' + fOpacity * 100 + ')';
	else
		element.style.opacity = fOpacity;
}

utils.getOpacity = function(el) {
    if (navigator.userAgent.indexOf('MSIE') != -1) {
        var re = /alpha\(opacity=(\d+)\)/;
        var arrOpacity = re.exec(el.style.filter);
        if (arrOpacity.length > 1)
            return parseFloat(arrOpacity[1]) / 100;
        else
            return parseFloat(1);
    }
    else
        return parseFloat(el.style.opacity);
}

utils.getParentByName = function(oElement, sTarget)
{
	sTarget = sTarget.toUpperCase();
	var oParent = oElement.parentNode;
	while(oParent.tagName != sTarget)
		oParent = oParent.parentNode;
	return oParent;
}


utils.fadeOut = function(element, fOncomplete)
{
	var op = 1.0;
	var it = window.setInterval(function(){
		if (op > 0.0){	
			op = op-0.1;
			utils.setOpacity(element, op);
		}
		else{
			window.clearInterval(it);
			if(fOncomplete)
				fOncomplete(element);
		}
	},100);
}

utils.toggleObject = function(arrElements,state)
{
	for (var i=0; i<arrElements.length; i++)
	{
		var elem = arrElements[i];
		elem.style.display = state;
	}
}

utils.stopPropag = function(evt) {
	if (evt.stopPropagation) evt.stopPropagation();
	else if (window.event) window.event.cancelBubble = true;
}

utils.addEvent = function(el, type, proc) {
	if (el.addEventListener) {
		el.addEventListener(type.slice(2,type.length), proc, false);
		return true;
	} else if (el.attachEvent) {
		return el.attachEvent(type, proc);
	}
}

utils.removeEvent = function(el, type, proc) {
	if (el.removeEventListener) {
		el.removeEventListener(type.slice(2,type.length), proc, false);
		return true;
	} else if (el.detachEvent) {
		return el.detachEvent(type, proc);
	}
}

utils.sendEvent = function(el, module, type /*, args*/) {
	if (el.dispatchEvent) {
		if (!document.implementation.hasFeature(module,""))
			return false;
		var e = document.createEvent(module);
		e.initEvent(type.slice(2,type.length), true, false/*, args */);
		el.dispatchEvent(e);
		return true;
	} else if (el.fireEvent) {
		el.fireEvent(type);
		return true;
	}
}

utils.rollImage = function(oImage)
{
	var elements = oImage.src.split('/');
	var n;
	var path = '/images/';
	n = elements[4];
	if (oImage.src.indexOf('admin') != -1){
		path = '/framework/admin/imgs/';
		n = elements[6];
	}
	if(n.indexOf('over') == -1){
		var imgName = n.split('.');
		var fn = path+imgName[0]+"_over."+imgName[1];	
	}
	else{
		var imgName = n.split('.');
		var portion = imgName[0].split('_')[0];
		var fn = path+portion+"."+imgName[1];
	}
	oImage.src = fn;
}

utils.getTarget = function(evt)
{
	var oTarget = evt.target || evt.srcElement;
	if (oTarget.nodeType == 3) // defeat Safari bug
		oTarget = oTarget.parentNode;
	return oTarget;
}

utils.getCssStyle = function(oElement, style)
{
	var strValue = "";
    if(document.defaultView && document.defaultView.getComputedStyle){
        var css = document.defaultView.getComputedStyle(oElement, null);
        strValue = css ? css.getPropertyValue(style) : null;
    }
    else if(oElement.currentStyle){
        style = style.replace(/\-(\w)/g, function (strMatch, p1){
            return p1.toUpperCase();
        });
        strValue = oElement.currentStyle[style];
        strValue = (isNaN(parseInt(strValue)))?0:strValue;
    }
    return strValue;
}

utils.getObjectPosition = function(oElement, relativeTo)
{
	var curleft = curtop = 0;
	if (oElement.offsetParent) 
	{
		while (oElement.offsetParent)
		{
			curleft += oElement.offsetLeft;
			curtop += oElement.offsetTop;
			oElement = oElement.offsetParent;
			if(relativeTo && oElement == relativeTo)
				break;
		}
	}
	return {
		left : curleft,
		top : curtop
	};
}

utils._getPageSize = function()
{
	var xScroll, yScroll;
	
	if (window.innerHeight && window.scrollMaxY) {	
		xScroll = document.body.scrollWidth;
		yScroll = window.innerHeight + window.scrollMaxY;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		xScroll = document.body.scrollWidth;
		yScroll = document.body.scrollHeight;
	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		xScroll = document.body.offsetWidth;
		yScroll = document.body.offsetHeight;
	}
	
	var windowWidth, windowHeight;
	if (self.innerHeight) {	// all except Explorer
		windowWidth = self.innerWidth;
		windowHeight = self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
		windowWidth = document.documentElement.clientWidth;
		windowHeight = document.documentElement.clientHeight;
	} else if (document.body) { // other Explorers
		windowWidth = document.body.clientWidth;
		windowHeight = document.body.clientHeight;
	}	
	
	// for small pages with total height less then height of the viewport
	if(yScroll < windowHeight)
		pageHeight = windowHeight;
	else
		pageHeight = yScroll;

	// for small pages with total width less then width of the viewport
	if(xScroll < windowWidth)	
		pageWidth = windowWidth;
	else
		pageWidth = xScroll;

	return [pageWidth,pageHeight,windowWidth,windowHeight]; 
}

utils._toggleOverlay = function(state)
{
	var oOverlay = $('overlay');
	if(!oOverlay){
		oOverlay = this._createContainer();
		oOverlay.id = 'overlay';
		document.body.appendChild(oOverlay);
	}
	var selectState = (state=='block')?'none':'';
	var t = this._getPageSize();
	if (selectState == 'none')
	{
		oOverlay.style.width = t[0]+"px";
		oOverlay.style.height = t[1]+"px";
	}
	oOverlay.style.display = state;
	var oSelects = document.getElementsByTagName('select');
	this.toggleObject(oSelects, selectState);
}

utils.center = function(oElement)
{
	var page = this._getPageSize();
	var objWidth = parseInt(this.getCssStyle(oElement, 'width'));
	var objHeight = parseInt(this.getCssStyle(oElement, 'height'));
	oElement.style.left = page[0]/2 - objWidth/2 +"px";
	oElement.style.top = (page[3]/2 - objHeight/2)+document.documentElement.scrollTop +"px";
}

utils.closePopup = function(oButton)
{
	var oParent = oButton.parentNode;
	while (oParent.id != 'popupContainer')
		oParent = oParent.parentNode;
	document.body.removeChild(oParent);
	if (!document.getElementById('popupContainer'))
		this._toggleOverlay('none');
}

utils._createContainer = function()
{
	var oDiv = document.createElement('DIV');
	oDiv.style.position = 'absolute';
	return oDiv;
}

utils.popupTemplate = function(title, content, oDims, oncomplete)
{
	this._toggleOverlay('block');
	var oContainer = this._createContainer();
	oContainer.id = 'popupContainer';
	oContainer.style.width = oDims[0]+"px";
	oContainer.style.height = oDims[1]+"px";
	
	var st='<div class="title-container">' +
				'<span class="window-title">'+ title +'</span>' +
				'<img class="close-window" src="/framework/admin/imgs/close.png"/>' +
			'</div>' +
			'<div class="windowbody">' +
				content +
			'</div>';
		
	oContainer.innerHTML = st;
	var oClose = oContainer.getElementsByTagName('IMG')[0];
	utils.addEvent(oClose,'onmouseover',function(){
		utils.rollImage(oClose);
	});
	utils.addEvent(oClose,'onmouseout',function(){
		utils.rollImage(oClose);
	});
	utils.addEvent(oClose,'onclick',function(){
		utils.closePopup(oClose);
	});
	document.body.appendChild(oContainer);
	this.center(oContainer);
	if(oncomplete){
		oncomplete(oContainer);
	}
	new Dragable(oContainer, oContainer.firstChild);
}

utils.confirmationBox = function(message, fCallback)
{
	var oContainer = this._createContainer();
	oContainer.id = 'popupContainer';
	this._toggleOverlay('block');
	var st='<div>' +
				'<div class="title-container">' +
					'<span class="window-title">Επιβεβαίωση ενέργειας</span>' +
					'<img class="close-window" src="/framework/admin/imgs/close.png"/>' +
				'</div>' +
				'<div class="windowbody" style="background-color:white">' +
					'<img style="float:left;padding:10px" src="/framework/admin/imgs/warning.jpg"/>' +
					'<span style="width:200px;float:left;padding:10px;height:50px;">'+message+'</span>' +
				'</div><br clear="both"/>' +
				'<div style="width:100%;height:40px;position:relative;">' +
					'<div class="subsection-add" style="width:100px">' +
						'<img src="/framework/admin/imgs/ok.png" ' +
						'style="padding:6px;float:left"/>' +
						'<span id="confirmYes" style="float:left;height:100%;margin-top:5px;">' +
							'Συνέχεια</span>' +
					'</div>' +
				'</div>' + 
			'</div>';

	oContainer.innerHTML = st;
	var oClose = oContainer.getElementsByTagName('IMG')[0];
	utils.addEvent(oClose,'onmouseover',function(){
		utils.rollImage(oClose);
	});
	utils.addEvent(oClose,'onmouseout',function(){
		utils.rollImage(oClose);
	});
	utils.addEvent(oClose,'onclick',function(){
		utils.closePopup(oClose);
	});
	oContainer.style.width = "350px";
	oContainer.style.height = "150px";
	document.body.appendChild(oContainer);
	this.center(oContainer);
	var oButton = $('confirmYes');
	var fHandler = function(){
		utils.closePopup(oButton);
		fCallback();
	}
	this.addEvent(oButton,'onclick', fHandler)
	new Dragable(oContainer, oContainer.firstChild.firstChild);
}
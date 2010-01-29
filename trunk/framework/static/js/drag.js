function Dragable(el, dragHandler)
{
	this.element = el;
	this.root = (typeof(dragHandler) == 'undefined')?el:dragHandler;
	this.xDelta = 0;
	this.yDelta = 0;
	this.xStart = 0;
	this.yStart = 0;
	var zIndex = utils.getCssStyle(this.element, 'z-index');
	this.oldZindex = (isNaN(zIndex))?0:zIndex;
	this.init();
}

Dragable.prototype.init = function()
{
	var oDrag = this;
	this.root.onmouseover = function(){
		this.style.cursor = 'move';
	}
	this.root.onmousedown = function(evt){
		evt = evt || window.event;
		document.onselectstart = function(){
			return false;
		}
		
		oDrag.element.style.zIndex = '11111';
		oDrag.xStart = parseInt(evt.clientX);
		oDrag.yStart = parseInt(evt.clientY);
		oDrag.element.style.top = parseInt(utils.getCssStyle(oDrag.element,'top')) + 'px';
		oDrag.element.style.left = parseInt(utils.getCssStyle(oDrag.element,'left')) + 'px';
		document.onmouseup = function(){
			oDrag.element.style.zIndex = oDrag.oldZindex;
	    	document.onmouseup = null;
    		document.onmousemove = null;
    		document.onselectstart = null;
	    }
	    document.onmousemove = function(evt){
	    	evt = evt || window.event;
	    	oDrag.drag(evt);
	    }
    	return false;
	}
}

Dragable.prototype.drag = function(evt)
{
    this.xDelta = this.xStart - parseInt(evt.clientX);
    this.yDelta = this.yStart - parseInt(evt.clientY);
    this.xStart = parseInt(evt.clientX);
    this.yStart = parseInt(evt.clientY);
    this.element.style.top = (parseInt(this.element.style.top) - this.yDelta) + 'px';
    this.element.style.left = (parseInt(this.element.style.left) - this.xDelta) + 'px';
}

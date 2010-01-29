/*
 * Generic class for implementing auto completion
 * for any input textfield. 
 * @param {Object} params
 * element, The text input object.
 * onclick, function that handles the event that fires when a result is clicked.
 * args, object with information about the table and field to query.
 * action, The servlet to call asynchronously, 
 */
function AutoComplete(params)
{
	this.oElement = params.element;
	this.endpoint = params.endpoint;
	this.parent = params.parent || null;
	this._createResultDiv();
	var el = this;
	this.oElement.onkeyup = function(evt){
		evt = evt || event;
		el._captureKey(evt);
		if(this.value == '')
		{
			el._oldValue = '';
			el.hide();
			return;
		}
		if(this.value == el._oldValue || this.value == '')
			return;
		el._oldValue = this.value;
		el._fetchResults();
	}
	
	this.onResultClick = params.onclick;
	this.dataSet = null;
	this._oldValue = '';
}

AutoComplete__Close = function(el)
{
	el.hide();
	utils.removeEvent(document, 'onclick', AutoComplete__Close);
}

AutoComplete.prototype._getSelection = function(evt)
{
	var index = -1;
	for (var i=0; i < this.resultDiv.childNodes.length; i++)
	{
		var oDiv = this.resultDiv.childNodes[i];
		if(oDiv.className == 'auto-comp-res-selected')
			index = i;
		oDiv.className = 'auto-comp-res';
	}

	switch(evt.keyCode)
	{
		case 40:
			if(index+1 >= this.resultDiv.childNodes.length)
				return -1;
			return index;
		case 38:
			return (index == -1)?-1:index-1;
		case 13:
			return index;
	}
}

AutoComplete.prototype._captureKey = function(evt)
{
	switch(evt.keyCode)
	{
		case 27:
		case 39:
			this.hide();
			break;
		case 40:
			var index = this._getSelection(evt);
			var oDiv = this.resultDiv.childNodes[index+1];
			oDiv.className = 'auto-comp-res-selected';
			this.resultDiv.scrollTop = oDiv.offsetTop - (oDiv.style.height || 20);
			break;
		case 38:
			var index = this._getSelection(evt);
			if (index < 0)
				return;
			var oDiv = this.resultDiv.childNodes[index];
			oDiv.className = 'auto-comp-res-selected';
			this.resultDiv.scrollTop = oDiv.offsetTop - (oDiv.style.height || 20);
			break;
		case 13:
			var index = this._getSelection(evt);
			if (index == -1)
				return;
			var oDiv = this.resultDiv.childNodes[index];
			this.oElement.value = oDiv.attrs.name;
			this.hide();
			this.onResultClick(oDiv.attrs);
			break;
	}
}

AutoComplete.prototype._fetchResults = function()
{
	var ajax = new JsonRequest('POST', this.endpoint);
	var oAuto = this;
	ajax.displayOverlay = false;
	ajax.oncomplete = function(req)
	{
		oAuto.dataSet = req.response;
		oAuto.display();
	}
	ajax.message = this.oElement.value;
	ajax.sendRequest();	
}

AutoComplete.prototype.clear = function()
{
	while(this.resultDiv.firstChild)
		this.resultDiv.removeChild(this.resultDiv.firstChild);
}

AutoComplete.prototype.show = function()
{
	this.resultDiv.style.display = 'block';
	var el = this;
	var _f = function(){
		AutoComplete__Close(el);
	}
	utils.addEvent(document, 'onclick', _f);
}

AutoComplete.prototype.hide = function()
{
	this.resultDiv.style.display = 'none';
}

AutoComplete.prototype.display = function()
{
	this.clear();
	if(this.dataSet.length == 0)
	{
		this.hide();
		return;
	}
	for (var i=0; i<this.dataSet.length; i++)
	{
		var oDiv = document.createElement('DIV');
		var oValue = this.dataSet[i];
		var oText = document.createTextNode(oValue.name);
		var el = this;
		oDiv.appendChild(oText);
		oDiv.className = 'auto-comp-res';
		oDiv.onmouseover = function(){
			this.className = 'auto-comp-res-selected';
		}
		oDiv.onmouseout = function(){
			this.className = 'auto-comp-res';
		}
		oDiv.attrs = oValue;
		this.resultDiv.appendChild(oDiv);
	}
	this.show();
}

AutoComplete.prototype._createResultDiv = function()
{
	this.resultDiv = document.createElement('DIV');
	this.resultDiv.id = 'auto-complete-area';
	var pos = utils.getObjectPosition(this.oElement, this.parent);	
	this.resultDiv.style.left = pos.left+"px";
	this.resultDiv.style.top = (pos.top+21)+"px";
	this.resultDiv.style.width = utils.getCssStyle(this.oElement, 'width');
	this.oElement.parentNode.insertBefore(this.resultDiv, this.oElement.nextSibling);
	var el = this;
	this.resultDiv.onclick = function(evt){
		var evt = evt || window.event;
		var oDiv = utils.getTarget(evt);
		if (oDiv.attrs){
			el.oElement.value = oDiv.firstChild.nodeValue;
			el.hide();
			el.onResultClick(oDiv.attrs);
			utils.stopPropag(evt);
			oDiv = null;
		}
	}
}
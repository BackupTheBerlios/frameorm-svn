function getXmlHttp()
{
	var req;
	if(window.XMLHttpRequest)
		req = new XMLHttpRequest();
    else if(window.ActiveXObject)
		req = new ActiveXObject("Microsoft.XMLHTTP");
    return req;
}


function JsonRequest(method,sUrl) 
{
	this.url = sUrl;
	this.xmlhttp = getXmlHttp();
	this.method = method;
	this.oncomplete = null;
	this.message = null;
	
	this.callback_info = null;
	this.response = null;
}

JsonRequest.prototype.sendRequest = function()
{
	if (this.method == 'POST')
	{
		this.xmlhttp.open("POST", this.url, true);
		this.xmlhttp.setRequestHeader("Content-Type","application/json");
		this.xmlhttp.send(JSON.stringify(this.message));
	}
	else
	{
		this.xmlhttp.open("GET", this.url, true);
		this.xmlhttp.send(null);
	}
	var req = this;

	this.xmlhttp.onreadystatechange = function() 
	{
		if (req.xmlhttp.readyState==4) 
		{
			retVal = req.processResult();
			if (retVal!= null && req.oncomplete) 
			{
				req.response = retVal;
				req.oncomplete(req);
			}
		}
	}
}

JsonRequest.prototype.toUrl = function()
{
	var s;
	var st = [];
	for (var key in this.message)
	{
		if (this.message[key] instanceof Array)
		{
			var ar = this.message[key];
			for (var i=0; i<ar.length; i++)
				st.push(key+'[]='+ar[i]);
		}
		else
			st.push(key+'='+this.message[key]);
	}
	s = st.join('&');
	return s
}

JsonRequest.prototype.processResult = function() 
{
	if (this.xmlhttp.status == 200) 
	{
		if (this.method == 'GET')
			return this.xmlhttp.responseText;
		else
			return eval( "(" + this.xmlhttp.responseText + ")" );
	}
	else
	{
		if(this.xmlhttp.status == '401')
			alert('Unathorized');
	}
	return null;
}

function JsonProxy(method)
{
	this.base = JsonRequest;
	this.base(method, "/ajax_proxy.php");
	this.domain = null;
}

JsonProxy.prototype.sendRequest = function()
{
	this.message.domain = this.domain;
	JsonRequest.prototype.sendRequest.call(this);
}








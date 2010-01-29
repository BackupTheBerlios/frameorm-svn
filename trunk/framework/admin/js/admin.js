function admin(){}

admin.login = function(el)
{
	var oForm = utils.getParentByName(el, 'FORM');
	var data = form.getFormData(oForm);
	var ajax = new JsonRequest('POST', '/page/login');
	ajax.oncomplete = function(req)
	{
		if(!req.response.error)
			window.location.href = req.response.url;
	}
	ajax.message = data;
	ajax.sendRequest();
}

admin.logout = function(el)
{
	var ajax = new JsonRequest('POST', '/admin/logout');
	ajax.oncomplete = function(req)
	{
		window.location.reload();
	}
	ajax.message = null;
	ajax.sendRequest();
}
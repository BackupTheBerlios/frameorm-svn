function user(){};

user.create = function()
{
	var ajax = new JsonRequest('GET', '/userpage/userForm');
	ajax.oncomplete = function(req)
	{
		utils.popupTemplate('Προσθήκη',req.response,[500,300]);
	};
	ajax.sendRequest();	
}

user.edit = function(evt, oImage)
{
	evt = evt || window.event;
	var id = oImage.parentNode.parentNode.id;
	var ajax = new JsonRequest('GET', '/userpage/editForm/'+id);
	ajax.oncomplete = function(req)
	{
		utils.popupTemplate('Προσθήκη',req.response,[500,300]);
	};
	ajax.sendRequest();
	utils.stopPropag(evt);
}

user.save = function(el)
{
	var oForm = utils.getParentByName(el, 'FORM');
	var ajax = new JsonRequest('POST', oForm.action);
	ajax.oncomplete = function(req)
	{
		window.location.reload();
	};
	ajax.message = form.getFormData(oForm);
	ajax.sendRequest();	
}

user.deleteUser = function(evt, oImage)
{
	evt = evt || window.event;
	var id = oImage.parentNode.parentNode.id;
	var ajax = new JsonRequest('POST', '/userpage/delete/'+id);
	ajax.oncomplete = function(req)
	{
		var div = oImage.parentNode;
		var toDelete = div.parentNode;
		if(req.response){
			var oncomp = function(){
				toDelete.parentNode.removeChild(toDelete);
			}	
			utils.fadeOut(toDelete, oncomp);
		}
	}
	ajax.sendRequest();	
	utils.stopPropag(evt);
}
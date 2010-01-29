function group(){};

group.create = function()
{
	var ajax = new JsonRequest('GET', '/groupage/groupForm');
	ajax.oncomplete = function(req)
	{
		utils.popupTemplate('Προσθήκη',req.response,[500,150]);
	};
	ajax.sendRequest();	
}

group.edit = function(evt, oImage)
{
	evt = evt || window.event;
	var id = oImage.parentNode.parentNode.id;
	var ajax = new JsonRequest('GET', '/groupage/editForm/'+id);
	ajax.oncomplete = function(req)
	{
		utils.popupTemplate('Προσθήκη',req.response,[500,150]);
	};
	ajax.sendRequest();
	utils.stopPropag(evt);
}

group.save = function(el)
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

group.deleteGroup = function(evt, oImage)
{
	evt = evt || window.event;
	var id = oImage.parentNode.parentNode.id;
	var ajax = new JsonRequest('POST', '/groupage/delete/'+id);
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
admin.module = function(){};

admin.module.create = function()
{
	var ajax = new JsonRequest('GET', '/modulepage/moduleForm');
	ajax.oncomplete = function(req)
	{
		var oncomplete = function(container){
			var s = new Tab('module-container');
			s.activate(s.tabs[0]);
		}
		utils.popupTemplate('Προσθήκη',req.response,[500,300], oncomplete);
	};
	ajax.sendRequest();	
}

admin.module.remove = function(el)
{
	el.parentNode.parentNode.removeChild(el.parentNode);
}

admin.module.addUser = function()
{
	var inp = admin.module.__createContainer('users');
	var args = {
		element : inp,
		parent : $('popupContainer'),
		endpoint : '/userpage/get',
		onclick : function(attrs){
			inp.parentNode.getElementsByTagName('SELECT')[0].id = attrs.id;
		}
	}
	new AutoComplete(args);
}

admin.module.addGroup = function()
{
	var inp = admin.module.__createContainer('groups');
	var args = {
		element : inp,
		parent : $('popupContainer'),
		endpoint : '/groupage/get',
		onclick : function(attrs){
			inp.parentNode.getElementsByTagName('SELECT')[0].id = attrs.id;
		}
	}
	new AutoComplete(args);
}

admin.module.__createContainer = function(type)
{
	var container = $('for_permissions');
	var div = document.createElement('DIV');
	div.className = 'h30';
	
	var inp = document.createElement('INPUT');
	inp.className = 'fleft mright5';
	inp.style.width = '120px';
	inp.type = 'text';
	div.appendChild(inp);
	
	var sel = document.createElement('SELECT');
	sel.className = 'fleft w200';
	sel.name = type;
	form.addSelectOption(sel, 'Reader' , '1');
	form.addSelectOption(sel, 'Author' , '2');
	form.addSelectOption(sel, 'Administrator' , '4');
	div.appendChild(sel);
	
	var a = document.createElement('A');
	a.className = 'mleft20 button fleft';
	a.href="#";
	a.onclick = function(){
		admin.module.remove(this);
		this.blur();
	};
	var span = document.createElement('SPAN');
	span.innerHTML = 'remove';
	a.appendChild(span);
	
	div.appendChild(a);
	container.appendChild(div);
	return inp;
}

admin.module.edit = function(evt, oImage)
{
	evt = evt || window.event;
	var id = oImage.parentNode.parentNode.id;
	var ajax = new JsonRequest('GET', '/modulepage/editForm/'+id);
	ajax.oncomplete = function(req)
	{
		var oncomplete = function(container){
			var s = new Tab('module-container');
			s.activate(s.tabs[0]);
		}
		utils.popupTemplate('Επεξεργασία',req.response,[500,300], oncomplete);
	};
	ajax.sendRequest();
	utils.stopPropag(evt);
}

admin.module.save = function(el)
{
	var oForm = utils.getParentByName(el, 'FORM');
	var selects = oForm.getElementsByTagName('SELECT');
	var permissions = {
		users : {},
		groups : {}
	};
	if(selects.length > 0){
		for(var i=0; i<selects.length; i++){
			var sel = selects[i];
			var value = sel.options[sel.selectedIndex].value;
			permissions[sel.name][sel.id] = value;
		}
	}else{
		permissions = '';
	}
	var ajax = new JsonRequest('POST', oForm.action);
	ajax.oncomplete = function(req)
	{
		window.location.reload();
	};
	var data = {
		name : form.getFormData(oForm).name,
		permissions : permissions
	};
	ajax.message = data;
	ajax.sendRequest();	
}

admin.module.deleteModule = function(evt, oImage)
{
	evt = evt || window.event;
	var id = oImage.parentNode.parentNode.id;
	var ajax = new JsonRequest('POST', '/modulepage/delete/'+id);
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
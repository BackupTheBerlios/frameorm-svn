function form() {}

form.elementHasBrothers = function(oForm, oElem)
{
	for (var i=0; i <oForm.elements.length; i++)
	{
		elem = oForm.elements[i];
		if (elem != oElem && elem.type == oElem.type && elem.name == oElem.name)
			return true;
	}
	return false;
}

form.getElementByType = function(oForm, sType)
{
	for (var i=0; i <oForm.elements.length; i++)
	{
		var elem = oForm.elements[i];
		if (elem.type == sType)
			return elem;
	}
	return null;
}

form.setCombo = function(oCombo, val)
{
	var len = oCombo.options.length;
	for (var i=0; i<len; ++i) 
	{
		if (oCombo.options[i].value == val) 
		{
			oCombo.options[i].selected = true;
			return oCombo.options[i];
		}
	}
}

form.clearCombo = function(oCombo)
{
	var i;
	for (i = oCombo.length - 1; i>=0; i--) 
		oCombo.remove(i);
}

form.removeFromCombo = function(oCombo, val)
{
	var i;
	for (i = oCombo.length - 1; i>=0; i--) 
		if (oCombo.options[i].value == val)
			oCombo.remove(i);
}

form.setComboText = function(oCombo, val, txt)
{
	var option = form.setCombo(oCombo, val);
	option.text = txt;
}

form.addSelectOption = function(oCombo, text, value, isSelected) 
{
    if (oCombo != null && oCombo.options != null)
    {
        oCombo.options[oCombo.options.length] = 
            new Option(text, value, false, isSelected);
    }
}

form.getFormData = function(oForm)
{
	var oData = {};
	var checkBoxVal = {}
	var multiSelectVal = {};
	var elem;
	for (var i=0; i <oForm.elements.length; i++)
	{
		elem = oForm.elements[i];
		if (elem.type == 'button' || elem.type == 'submit')
			continue;
		switch(elem.type)
		{
			case 'select-one':
				if (elem.options.length > 0)
					oData[elem.name] = elem.options[elem.selectedIndex].value || '';
				break;
			case 'checkbox':
				if (elem.checked == true)
				{
					if(form.elementHasBrothers(oForm, elem))
					{
						if (typeof(checkBoxVal[elem.name]) == 'undefined')
							checkBoxVal[elem.name] = [];
						checkBoxVal[elem.name].push(elem.value);
						oData[elem.name] = checkBoxVal[elem.name];
					}
					else
						oData[elem.name] = elem.value || 'true';
				}
				break;
			case 'select-multiple':
				oData[elem.name] = form._getMultiSelectValues(multiSelectVal, elem);
				break;
			default:
				oData[elem.name] = elem.value;
				break;
		}
	}
	return oData;
}

form._getMultiSelectValues = function(oCon, oElement)
{
	if (typeof(oCon[oElement.name]) == 'undefined')
		oCon[oElement.name] = [];
	for (var i=0; i<oElement.options.length; i++)
	{
		var oOption = oElement.options[i];
		if (oOption.selected)
			oCon[oElement.name].push(oOption.value)
	}
	return oCon[oElement.name];
}

form.clearForm = function(oForm, skipHidden)
{
	var elem;
	for (var i=0; i <oForm.elements.length; i++)
	{
		elem = oForm.elements[i];
		if (elem.type == 'button' || elem.type == 'submit' || (skipHidden && elem.type=='hidden'))
			continue;
		switch(elem.type)
		{
			case 'select-one':
				if(elem.options.length > 0)
					elem.options[0].selected = true;
				break;
			case 'checkbox':
			case 'radio':
				var el = elem;
				elem.checked = false;
				break;
			case 'select-multiple':
				for (var j=0; j<elem.options.length; j++)
					elem.options[j].selected = false;
				break;
			default:
				elem.value = '';
				break;
		}
	}
}

form.populateForm = function(oForm, oData)
{
	for (var key in oData)
	{
		var elem = document.getElementsByName(key)[0];
		if(typeof(elem) == 'undefined')
			continue;
		if (elem.type == 'button' || elem.type == 'submit')
			continue;
		switch(elem.type)
		{
			case 'select-one':
				form.setCombo(elem, oData[key]);
				break;
			case 'checkbox':
				form._populateCheckBoxes(key,oData[key]);
				break;
			case 'select-multiple':
				for (var j=0; j<elem.options.length; j++)
					elem.options[j].selected = (oData[key].hasItem(elem.options[j].value));
				break;
			default:
			elem.value = oData[key];
			break;
		}
	}
}

form._populateCheckBoxes = function(sName, arrVal)
{
	var oChecks = document.getElementsByName(sName);
	for (var i=0; i<oChecks.length; i++)
		if(arrVal.hasItem(oChecks[i].value))
			oChecks[i].checked = true;
}

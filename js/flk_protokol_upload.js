function checkbox_submit(form, iName, iValue)
{
	var input = document.createElement('input');
	input.type = 'hidden';
	input.name = iName;
	input.value = iValue;
	document.forms[form].appendChild(input); 
	document.forms[form].submit();
}


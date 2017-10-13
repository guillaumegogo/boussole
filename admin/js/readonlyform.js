function addLoadEvent(func) 
{
	var oldonload = window.onload;
	if (typeof window.onload != 'function') 
	{
		window.onload = func;
	} 
	else 
	{
		window.onload = function() 
		{
			oldonload();
			func();
		}
	}
}

addLoadEvent(process_form);

function process_form()
{
	var root_form_ref 	= document.forms[0];
	process_elements_input(root_form_ref.getElementsByTagName("INPUT"));
	process_element_texarea(root_form_ref.getElementsByTagName("TEXTAREA"));
	process_element_select(root_form_ref.getElementsByTagName("SELECT"));
}

function process_elements_input(eles)
{
	for (var idx = 0; idx < eles.length; idx++) 
	{
		if (eles[idx].nodeType != 1)
			continue;

		var eles_type = eles[idx].getAttribute("type");
		if (eles_type == "text") 
		{
			var new_ele_ref 	= document.createTextNode(eles[idx].value);
			eles[idx].parentNode.insertBefore(new_ele_ref, eles[idx].nextSibling);
	
		}
	}
}

function process_element_texarea(eles)
{
	for (var idx = 0; idx < eles.length; idx++) 
	{
		var new_ele_ref 	= document.createElement('fieldset');
		var new_ele_ref1 	= document.createElement('span');
		new_ele_ref1.setAttribute("class", "print_disp_on");
		new_ele_ref.appendChild(new_ele_ref1);

		var eles_text_ref 	= document.createTextNode(eles[idx].value);
		new_ele_ref.appendChild(eles_text_ref);

		eles[idx].parentNode.insertBefore(new_ele_ref, eles[idx].nextSibling);
	}
}

function process_element_select(eles)
{
	for (var idx = 0; idx < eles.length; idx++) 
	{
		if (eles[idx].value && eles[idx].multiple == false)
		{
			var sIndex 		= eles[idx].selectedIndex;
			var select_text = eles[idx].options[sIndex].text;

			var new_ele_ref 	= document.createTextNode(select_text);
			eles[idx].parentNode.insertBefore(new_ele_ref, eles[idx].nextSibling);
		}
	}
}

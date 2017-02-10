/*
	JSCookTree v1.01.  (c) Copyright 2002 by Heng Yuan

	Permission is hereby granted, free of charge, to any person obtaining a
	copy of this software and associated documentation files (the "Software"),
	to deal in the Software without restriction, including without limitation
	the rights to use, copy, modify, merge, publish, distribute, sublicense,
	and/or sell copies of the Software, and to permit persons to whom the
	Software is furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included
	in all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	ITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
	FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
	DEALINGS IN THE SOFTWARE.
*/

// Globals

// store which menu item has been selected
var m_itemSelected = null;
// menu handlers
var m_itemHandlers = ' CLASS=MenuItem onMouseOver="itemMouseOver (this)" onMouseOut="itemMouseOut (this)" onMouseDown="itemMouseDown (this)" onMouseUp="itemMouseUp (this)" onSelect="return false"';
var m_buttonHandlers =  ' onMouseUp="itemMouseUp (this.parentNode.nextSibling)"';
// default node properties
var m_nodeProperties =
[
	// 0, HTML code for folders in closed and open form, respectively
	['', ''],
	// 1, HTML code for connectors for folders.  First one connects next, second one doesn't
	['', ''],
	// 2, HTML code for connectors for folders.  First one connects next, second one doesn't
	['', ''],
	// 3, HTML code for leaves
	'',
	// 4, HTML code for connectors for leaves.  First one connects next, second one doesn't
	['', ''],
	// 5, HTML code for spacers.  First one connects next, second one doesn't
	['&nbsp;&nbsp;&nbsp;&nbsp;', '&nbsp;&nbsp;&nbsp;&nbsp;']
];

// Menu Drawing Functions

function drawFolder (folder, connect)
{
	return '<SPAN' + m_buttonHandlers + '>' + connect[0] + folder[0] + '</SPAN>' +
					'<SPAN STYLE="display: none"' + m_buttonHandlers + '>' + connect[1] + folder[1] + '</SPAN>';
}

function drawNode (node, indent, hasNext)
{
	// [0] = node property
	// [1] = node text
	// [2] = url reference
	// [3] = target content
	// [4] and rest are child nodes

	var str = '';
	var hasChildren = (node.length > 4);

	str += '<DIV STYLE="white-space: nowrap">';
	str += indent;

	if (hasChildren)
	{
		// draw folder
		var connect;
		if (hasNext)
			connect = m_nodeProperties[1];
		else
			connect = m_nodeProperties[2];

		str += '<SPAN>';
		if (node[0] == null)
			str += drawFolder (m_nodeProperties[0], connect);
		else
			str += drawFolder (node[0], connect);
		str += '</SPAN>';
	}
	else
	{
		// draw leaf
		if (hasNext)
			str += m_nodeProperties[4][0];
		else
			str += m_nodeProperties[4][1];

		if (node[0] == null)
			str += m_nodeProperties[3];		// leaf node
		else
			str += node[0];
	}

	str += '<SPAN' + m_itemHandlers + '>';

	// check if there are any
	if (node[2] == null)
	{
		str += node[1];
	}
	else
	{
		str += '<A HREF="' + node[2] + '"';
		if (node[3] == null)
			str += '>';
		else
			str += ' TARGET="' + node[3] + '">';
		str += node[1] + '</A>';
	}
	str += '</SPAN>';

	// draw child nodes if there are any
	if (hasChildren)
	{
		str += '<DIV CLASS=SubMenu>';
		var i;

		for (i = 4; i < node.length; i++)
		{
			if (node[i] == undefined)		// this code is necessary for IE
				continue;

			var next = (i < (node.length - 1) && node[i + 1] != undefined)
			if (hasNext)
				str += drawNode (node[i], indent + m_nodeProperties[5][0], next);
			else
				str += drawNode (node[i], indent + m_nodeProperties[5][1], next);
		}
		str += '</DIV>';
	}
	str += '</DIV>';
	return str;
}

// Menu Mouse Handling Functions

function itemMouseOver (obj)
{
	obj.className = 'MenuItemHover';
}

function itemMouseOut (obj)
{
	if (obj == m_itemSelected)
		obj.className = 'MenuItemSelected';
	else
		obj.className = 'MenuItem';
}

function itemMouseDown (obj)
{
	obj.className = 'MenuItemActive';
}

function itemMouseUp (obj)
{
	if (obj.className == 'MenuItemActive')
	{
		// select object
		if (m_itemSelected != null)
			m_itemSelected.className = 'MenuItem';
		obj.className = 'MenuItemSelected';
		m_itemSelected = obj;
	}

	// show sub menus
	if (obj.nextSibling && obj.nextSibling.className == 'SubMenu')
	{
		var show;
		var submenu = obj.nextSibling.style;
		if (submenu.display == 'block')
		{
			submenu.display = 'none';
			show = false;
		}
		else
		{
			submenu.display = 'block';
			show = true;
		}

		var closedForm = obj.previousSibling.firstChild;
		var openForm = closedForm.nextSibling;

		if (show)
		{
			closedForm.style.display = 'none';
			openForm.style.display = 'inline';
		}
		else
		{
			closedForm.style.display = 'inline';
			openForm.style.display = 'none';
		}
	}
}

// menu start up call

function drawTree (id, title, menu, nodeProperties)
{
	var obj;
	if (document.all)
		obj = document.all[id];
	else
		obj = document.getElementById (id);

	if (nodeProperties != null)
		m_nodeProperties = nodeProperties;

	var str = (title != null) ? '<DIV STYLE="white-space: nowrap">' + title + "</DIV>" : '';
	var i;
	for (i = 0; i < menu.length; ++i)
	{
		if (menu[i] == undefined)
			continue;
		var next = (i < (menu.length - 1) && menu[i + 1] != undefined);
		str += drawNode (menu[i], '', next);
	}
	obj.innerHTML = str;
}

/* JSCookTree v1.01.  (c) Copyright 2002 by Heng Yuan */

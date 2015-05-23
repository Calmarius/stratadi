// MOVE THIS TO THE DEBUG.JS

function debug(objectStr)
{
	var executeString=function(str)
	{
		try
		{
			eval(str);
		}
		catch(e)
		{
			alert(e);
		}
	}
	var escapeHTML=function(str)
	{
		return str.replace(new RegExp('<','gi'),'&lt;').replace(new RegExp('>','gi'),'&gt;').replace(new RegExp('"','gi'),'&quot;').replace(new RegExp("'",'gi'),'&#39;');
	};
	var escapeQuotes=function(str)
	{
		return str.replace(new RegExp('"','gi'),'&quot;').replace(new RegExp("'",'gi'),"&#39;");
	}
	var createDebugDiv=function(iHTML)
	{
		var div=document.createElement('div');
		div.style.position='absolute';	
		div.style.left='10px';	
		div.style.top='10px';	
		div.style.maxHeight='80%';
		div.style.maxWidth='80%';
		div.style.backgroundColor='white';
		div.style.border='1px solid black';
		div.style.width='auto';
		div.style.height='auto';
		div.style.zIndex='100';
		div.style.padding='10px 30px 30px 10px';
		div.style.overflow='auto';
		div.innerHTML=
		'<div style="position: absolute; right:0; top:0;cursor:pointer" onclick="this.parentNode.parentNode.removeChild(this.parentNode)">X</div>'+
		iHTML+
		''
		;
		var evHandler=function(ev)
		{
			ev=ev || window.event;
			if (ev.stopPropagation) ev.stopPropagation();
			ev.cancelBubble=true;
		}
		div.onclick=evHandler;
		div.onmousedown=evHandler;
		div.onmousemove=evHandler;
		document.body.appendChild(div);
	};
	if (!objectStr) objectStr='window';
	var obj=eval('('+objectStr+')');
	var contentStr='';
	var remapped=new Array();
	for(var i in obj)
	{
		var valueStr;
		try
		{
			value=obj[i];
			var valueType=typeof(value);
			var escapedObjectString=escapeQuotes(objectStr);
			if (value==null) valueStr='null';
			else if ((valueType=='function') || (valueType=='object')) valueStr='<a href="javascript:void(debug(\''+escapedObjectString+'[&quot;'+i+'&quot;]\'))">View</a>';
			else valueStr=escapeHTML(value.toString());
		}
		catch(e)
		{
			valueStr=escapeHTML(e.toString());
		}
		remapped.push({'key':i,'value':valueStr});		
	}
	remapped.sort(function(a,b){return a.key>b.key;});
	for(var i in remapped)
	{
		var value=remapped[i];
		contentStr+=
		'<tr><td>'+value.key+'</td><td>'+value.value+'</td></tr>';
	}
	var rid=generateRandomId();
	var rid2=generateRandomId();
	var rid3=generateRandomId();
	var text=
	'<h1>Viewing: '+objectStr+'</h1>'+
	'<pre>'+escapeHTML(obj.toString())+'</pre>'+
	'<div><a href="javascript:void((function(){var div=_(\''+rid3+'\'); if (div.style.display==\'none\') {div.style.display=\'block\';} else {div.style.display=\'none\';}})())">View var, eval</a></div>'+
	'<div id="'+rid3+'" style="display:none">'+
		'<div><input type="text" id="'+rid+'"><input type="button" value="View" onclick="javascript:debug(_(\''+rid+'\').value)"></div>'+
		'<div><textarea rows="10" cols="30" id="'+rid2+'"></textarea><br><input type="button" value="Execute" onclick="javascript:{try{eval(_(\''+rid2+'\').value);}catch(e){alert(e);}}"></div>'+
	'</div>'+
	'<table>'+
	'<tr><th>PropertyName</th><th>Value</th></tr>'+
	contentStr+
	'</table>'+
	''
	;
	
	createDebugDiv(text);
}











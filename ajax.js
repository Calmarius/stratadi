if (typeof XMLHttpRequest == "undefined")
  XMLHttpRequest = function () {
    try { return new ActiveXObject("Msxml2.XMLHTTP.6.0"); }
      catch (e) {}
    try { return new ActiveXObject("Msxml2.XMLHTTP.3.0"); }
      catch (e) {}
    try { return new ActiveXObject("Msxml2.XMLHTTP"); }
      catch (e) {}
    //Microsoft.XMLHTTP points to Msxml2.XMLHTTP.3.0 and is redundant
    alert("This browser does not support XMLHttpRequest! Please enable ActiveX controls.");
  };
  
xmlHttp=new XMLHttpRequest();

/*centering the throbber */

function getScrollXY()
{
	var scrOfX = 0, scrOfY = 0;
	if( typeof( window.pageYOffset ) == 'number' )
	{
		scrOfY = window.pageYOffset;
		scrOfX = window.pageXOffset;
	}
	else
	if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) )
	{
		scrOfY = document.body.scrollTop;
		scrOfX = document.body.scrollLeft;
	}
	else
	if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) )
	{
		scrOfY = document.documentElement.scrollTop;
		scrOfX = document.documentElement.scrollLeft;
	}
	return [ scrOfX, scrOfY ];
}
	
function getWindowClientSize()
{
	var mw = 0, mh = 0;
	if( typeof( window.innerWidth ) == 'number' )
	{
		mw = window.innerWidth;
		mh = window.innerHeight;
	}
	else
	if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) )
	{
		mw = document.documentElement.clientWidth;
		mh = document.documentElement.clientHeight;
	}	
	else
	if( document.body && ( document.body.clientWidth || document.body.clientHeight ) )
	{
		mw = document.body.clientWidth;
		mh = document.body.clientHeight;
	}
	return [mw,mh];
}

function centerElement(id)
{
	st=getScrollXY();
	wt=getWindowClientSize()
	if (typeof(id)=="object") obj=id;
	else obj=document.getElementById(id);
	obj.style.visibility='hidden';
	var fn=
	(
		function(obj)
		{
			return function()
			{
				obj.style.visibility='visible';
				obj.style.top= ( parseInt( (wt[1]/2) - (parseInt(obj.offsetHeight)/2) ) + st[1])+"px";
				obj.style.left=( parseInt( (wt[0]/2) - (parseInt(obj.offsetWidth)/2) ) + st[0])+"px";
			}
		}
	)(obj);
	setTimeout(fn,100);
/*	var s="document.body.clientHeight:"+wt[1]+"\n";
	s+="document.body.clientWidth:"+wt[0]+"\n";
	s+="scrollTop:"+st[1]+"\n";
	s+="scrollLeft:"+st[0]+"\n";*/
}

//
// AJAX POST
//
function ajaxPost(url,paramstring,callBack)
{
	if ((xmlHttp.readyState!=0) && (xmlHttp.readyState!=4)) return false;
	xmlHttp.open("POST",url,true);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	if (!document.getElementById("throbberDiv"))
	{
                var elm=document.createElement("div");
                elm.style.width="200px";
                elm.style.height="100px";
                elm.style.border="1px green solid";
                elm.style.backgroundColor="black";
                elm.style.color="white";
                elm.style.position="absolute";
                elm.style.zIndex="50";
                elm.innerHTML='<p>Loading...</p><p style="text-align:center"><img alt="throbber" src="img/ajax-loader.gif"><br>Status: <span id="ar_dlstatus">No response</span></p>';
                elm.id="throbberDiv";
                centerElement(elm);
                document.getElementsByTagName("body")[0].appendChild(elm);
                var throbberCallback=function()
                {
                        if (xmlHttp.readyState==4)
                        {
                                var element=document.getElementById("throbberDiv");
                             
                                if (element)
                                {
                                        element.parentNode.removeChild(element);
                                }
                        }
                        else if (xmlHttp.readyState==3)
                        {
	                	document.getElementById('ar_dlstatus').innerHTML='Downloading data... '+(xmlHttp.responseText.length)+' bytes received.';
                        }
                        callBack();
                }
                xmlHttp.onreadystatechange = throbberCallback;
        }
        else
        {
                xmlHttp.onreadystatechange = callBack;
        }
	xmlHttp.send(paramstring);
	return true;
}

function ajaxPostXHR(xhr,url,paramstring,callBack)
{
	xhr.open("POST",url,true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhr.setRequestHeader("Content-length", paramstring.length);
	xhr.setRequestHeader("Connection", "close");
	var elmId="";
        for(var i=0;i<30;i++)
        {
                elmId="throbberdiv"+i;
                if (!document.getElementById(elmId)) break;
        }
        var elm=document.createElement("div");
        elm.style.width="200px";
        elm.style.height="100px";
        elm.style.border="1px green solid";
        elm.style.backgroundColor="black";
        elm.style.color="white";
        elm.style.position="absolute";
        elm.style.zIndex="50";
        elm.innerHTML='<p>Loading...</p><p style="text-align:center"><img alt="throbber" src="img/ajax-loader.gif"></p>';
        elm.id=elmId;
        centerElement(elm);
        document.getElementsByTagName("body")[0].appendChild(elm);
        var throbberCallback=function()
        {
                if (xhr.readyState==4)
                {
                        var element=document.getElementById(elmId);
                        if (element)
                                element.parentNode.removeChild(element);
                }
                callBack();
        }
        xhr.onreadystatechange = throbberCallback;
	xhr.send(paramstring);	
}

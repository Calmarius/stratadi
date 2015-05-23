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

function CenterElement(id)
{
	st=getScrollXY();
	wt=getWindowClientSize()
	if (typeof(id)=="object") obj=id;
	else obj=document.getElementById(id);
	obj.style.top= ( parseInt( (wt[1]/2) - (parseInt(obj.style.height)/2) ) + st[1])+"px";
	obj.style.left=( parseInt( (wt[0]/2) - (parseInt(obj.style.width)/2) ) + st[0])+"px";
/*	var s="document.body.clientHeight:"+wt[1]+"\n";
	s+="document.body.clientWidth:"+wt[0]+"\n";
	s+="scrollTop:"+st[1]+"\n";
	s+="scrollLeft:"+st[0]+"\n";*/
}

document.onmousemove = mouseMove;
document.onmouseup   = mouseUp;

var dragObject  = null;
var resizeObject  = null;
var md_prevMouse;
var md_mousePosition;

/*function mouseMove(ev){
	ev           = ev || window.event;
	var mousePos = mouseCoords(ev);
}*/

function mouseCoords(ev){
	if(ev.pageX || ev.pageY){
		return {x:ev.pageX, y:ev.pageY};
	}
	return {
		x:ev.clientX + document.body.scrollLeft - document.body.clientLeft,
		y:ev.clientY + document.body.scrollTop  - document.body.clientTop
	};
}

function getMouseOffset(target, ev){
	ev = ev || window.event;

	var docPos    = getPosition(target);
	var mousePos  = mouseCoords(ev);
	return {x:mousePos.x - docPos.x, y:mousePos.y - docPos.y};
}

function getPosition(e){
	var left = 0;
	var top  = 0;

	while (e.offsetParent){
		left += e.offsetLeft;
		top  += e.offsetTop;
		e     = e.offsetParent;
	}

	left += e.offsetLeft;
	top  += e.offsetTop;

	return {x:left, y:top};
}

function mouseMove(ev)
{
	ev           = ev || window.event;
	var mousePos = mouseCoords(ev);
	md_mousePosition=mousePos;
	if(dragObject){
		dragObject.style.top      = parseInt(dragObject.style.top)+mousePos.y - md_prevMouse.y + "px";
		dragObject.style.left     = parseInt(dragObject.style.left)+mousePos.x - md_prevMouse.x + "px";
		if (parseInt(dragObject.style.top)<0) dragObject.style.top="0px";
		if (parseInt(dragObject.style.left)<0) dragObject.style.left="0px";
      md_prevMouse=mousePos;
		return false;
	}
	if(resizeObject)
	{
		resizeObject.style.width      = parseInt(resizeObject.style.width)+mousePos.x - md_prevMouse.x + "px";
		resizeObject.style.height     = parseInt(resizeObject.style.height)+mousePos.y - md_prevMouse.y + "px";
      md_prevMouse=mousePos;
      resizeObject.onresize();
		return false;
	}
}

function mouseUp(){
	dragObject = null;
	resizeObject=null;
}

/*function makeDraggable(item){
	if(!item) return;
í	item.onmousedown = function(ev){
		dragObject  = this;
		mouseOffset = getMouseOffset(this, ev);
		return false;
	}
}*/

function makeDraggableParent(item){
	if(!item) return;
	item.onmousedown = function(ev){
      ev=ev || window.event;
		dragObject  = this.parentNode;
		md_prevMouse = mouseCoords(ev);
		return false;
	}
}

function makeResizableParent(item)
{
	if(!item) return;
	item.onmousedown = function(ev){
      ev=ev || window.event;
		var mouseOffset = getMouseOffset(this,ev);
		md_prevMouse=mouseCoords(ev);
		if ((mouseOffset.x+10>=parseInt(this.parentNode.style.width)) && (mouseOffset.y+10>=parseInt(this.style.height)))
		{
         resizeObject  = this.parentNode;
      }
		return false;
	}
}

// obj: abszolút pozicionált div
// tsize: titlebar mérete
// fsize alsóbar mérete
// hcolor: header color
// bwidth: border width
// bcolor: border color
// obj.heading=cím
// obj.content=tartalom
// obj.footer=alja
function makeItModal(obj,tsize,fsize,hcolor,bwidth,bcolor)
{
   if (!hcolor) hcolor="blue";
	obj.popup=function() {this.style.visibility="visible"; CenterElement(obj);}
	obj.hide=function() {this.style.visibility="hidden";}
	h=parseInt(obj.style.height);
	obj.style.overflow="visible";
	obj.style.borderWidth=bwidth+"px";
	obj.style.borderStyle="solid";
	obj.style.borderColor=bcolor;
	obj.onClose=function()
	{
	}
	obj.innerHTML=
      '<div style="background:'+hcolor+';top:0%;height:'+tsize+'px;cursor:move">'+
      '<table style="width:100%;border-style:none"><tbody><tr><td id="title" style="border-style:none"></td>'+
      '<td style="text-align:right;border-style:none">'+
      '<a style="cursor:default" '+
      'onclick="var tmp=this.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode; tmp.onClose(); tmp.parentNode.removeChild(tmp); ">'+
      '[x]</a></td></tr></tbody></table>'+
      '</div>'+
      '<div id="content" style="overflow:auto;height:'+(h-fsize-tsize-10)+'px"></div>'+
      '<div style="height:'+(fsize)+'px" id="footer"></div>'+
      '<div style="height:10px;background:black;opacity:0.5;filter:alpha(opacity=50);bottom:0px" id="footer"></div>';
	var subobjs=obj.getElementsByTagName("div");
	obj.headingdiv=subobjs[0];
	obj.heading=subobjs[0].getElementsByTagName("td")[0];
	obj.content=subobjs[1];
	obj.footer=subobjs[2];
	obj.resizebar=subobjs[3];
	obj.resizebar.style.position="relative";
	obj.resizebar.style.left="0px";
	obj.resizebar.style.top="0px";
	obj.resizebar.innerHTML='<img style="position:absolute; right:0; cursor:se-resize" alt="átmeretezés" src="resize.png">';
	obj.onresize=function()
	{
      h=parseInt(this.headingdiv.style.height)+parseInt(this.footer.style.height)+parseInt(this.resizebar.style.height);
      this.content.style.height=(parseInt(this.style.height)-h)+"px";
	}
	makeDraggableParent(obj.headingdiv);
	makeResizableParent(obj.resizebar);
}

function SelectedCells()
{
	this.cells=new Object();
	this.clearSelection=function()
	{
		this.cells=new Object();
	};
	this.selectCell=function(x,y,select)
	{
		if (select==null) select=true;
		if (select)
		{
			if (!this.cells[x]) this.cells[x]=new Object();
			if (!this.cells[x][y])
			{
				this.cells[x][y]=select;
			}
		}
		else
		{
			if (this.cells[x] && this.cells[x][y]) delete this.cells[x][y];
		}
	};
	this.isSelected=function(x,y)
	{
		return this.cells[x] && this.cells[x][y];
	}
	this.forAllSelected=function(callback)
	{
		for(x in this.cells)
		{
			for(y in this.cells[x])
			{
				callback(x,y);
			}
		}
	}
}

//-------------------------------

function DefaultMouseMode()
{
	this.onclick=function(e)
	{
		if (!this.canClick) return;
		var mc=mouseCoords(e);
		var cellCoords=coordToCell(mc.x,mc.y);
		selectedCells.clearSelection();
		selectedCells.selectCell(cellCoords.x,cellCoords.y);
		showCellInfo(cellCoords.x,cellCoords.y,mc,20);
		renderMap();
	};
	
	this.onmousedown=function(e)
	{
		var mc=mouseCoords(e);
		this.pMouseX=this.mouseX=mc.x; // prev mouse position
		this.pMouseY=this.mouseY=mc.y; // prev mouse position
		this.dx = 0; // displacement of the mouse cursor
		this.dy = 0; // displacement of the mouse cursor
		this.mousePress=true;
		this.canClick=true;
	}
	
	this.onmouseup=function(e)
	{
		this.mousePress=false;
	}
	
	this.onmousemove=function(e)
	{
		var mc=mouseCoords(e);
		if (this.mousePress)
		{
			centerX+=this.pMouseX-mc.x;
			centerY+=this.pMouseY-mc.y;
			this.dx += this.pMouseX-mc.x;
			this.dy += this.pMouseY-mc.y;
			if (Math.sqrt(this.dx*this.dx + this.dy*this.dy) > 10)
			{
    			this.canClick=false;
			}
			this.pMouseX=mc.x;
			this.pMouseY=mc.y;
			renderMap();
		}
	}
}

//--------------------------------------

function SelectionRect()
{
	this.active=false;
	this.startX=0;
	this.startY=0;
	this.endX=0;
	this.endY=0;
	
	this.start=function(x,y)
	{
		this.startX=x;
		this.startY=y;
		this.active=true;
	}
	
	this.drag=function(x,y)
	{
		this.endX=x;
		this.endY=y;
	}
	
	this.end=function()
	{
		if (!this.active) return;
		this.active=false;
		var leftX=this.startX<this.endX ? this.startX:this.endX;
		var topY=this.startY<this.endY ? this.startY:this.endY;
		var width=Math.abs(this.startX-this.endX);
		var height=Math.abs(this.startY-this.endY);
		if (this.onSelect) this.onSelect(leftX,topY,width,height);
	}
	
	this.draw=function(hdc)
	{
		if (!this.active) return;
		var leftX=this.startX<this.endX ? this.startX:this.endX;
		var topY=this.startY<this.endY ? this.startY:this.endY;
		var width=Math.abs(this.startX-this.endX);
		var height=Math.abs(this.startY-this.endY);

		hdc.lineWidth=1;
		hdc.strokeStyle="#000000";
		hdc.strokeRect(leftX+1,topY+1,width,height);
		hdc.strokeStyle="#FFFFFF";
		hdc.strokeRect(leftX,topY,width,height);
	}
}

//---------------------------------------

function SelectMouseMode()
{
	this.onclick=function(e)
	{
	};
	
	this.onmousedown=function(e)
	{
		mc=mouseCoords(e);
		selectionRect.start(mc.x,mc.y);
	}
	
	this.onmouseup=function(e)
	{
		selectionRect.end();
		renderMap();
	}
	
	this.onmousemove=function(e)
	{
		var mc=mouseCoords(e);
		selectionRect.drag(mc.x,mc.y);
		if (selectionRect.active) renderMap();
	}
}

//---------------------------------------

function ActionMouseMode()
{
	this.onclick=function(e)
	{
		var mc=mouseCoords(e);
		var cellCoords=coordToCell(mc.x,mc.y);
		cellAction(cellCoords.x,cellCoords.y,mc);
		renderMap();
	}

	this.onmousedown=function(e)
	{
	}
	
	this.onmouseup=function(e)
	{
	}
	
	this.onmousemove=function(e)
	{
	}
}







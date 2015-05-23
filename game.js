var gameCanvas=$('game');

gameCanvas.redraw=function()
{
	var gWidth=this.width;
	var gHeight=this.height;
	var hdc=this.getContext("2d");
	
	hdc.fillStyle="#000000";
	hdc.fillRect(0,0,gWidth,gHeight);
}

gameCanvas.redraw();




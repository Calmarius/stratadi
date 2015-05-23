var timers=[];

function hourString(secs)
{
	var hours=Math.floor(secs/3600);
	var minutes=Math.floor((secs/60)%60);
	var seconds=Math.floor(secs%60);
	if (minutes<10) minutes='0'+minutes;
	if (seconds<10) seconds='0'+seconds;
	var str=hours+':'+minutes+':'+seconds;
	return str;
}

function initializeTimers()
{
	var spans=document.getElementsByTagName('span');
	for(var i in spans)
	{
		var e=spans[i];
		var str=' '+e.className+' ';
		var countdown=str.match(' countdown ');
		var countup=str.match(' countup ');
		if (countdown || countup)
		{
			e.timerValue=parseInt(e.innerHTML);
			e.started=new Date();
			if (countdown)
			{
				e.onTimerHit=function()
				{
					var now=new Date();
					var secondsLeft=this.timerValue-(now.getTime()-this.started.getTime())/1000.0;
					if (secondsLeft<0) secondsLeft=0;
					this.innerHTML=hourString(secondsLeft);
				}
			}
			else if (countup)
			{
				e.onTimerHit=function()
				{
					var now=new Date();
					var secondsLeft=this.timerValue+(now.getTime()-this.started.getTime())/1000.0;
					this.innerHTML=hourString(secondsLeft);
				}
			}
			e.onTimerHit();
			timers.push(e);
		}
	}
}

function setupCounter(e,pace,decimals,padding)
{
	try
	{
		e.started=new Date();
		e.initialValue=parseFloat(e.innerHTML);
		if (isNaN(e.initialValue)) {e.innerHTML='initialValue is NAN: '+e.innerHTML; return;}
		e.countPace=pace;
		if (isNaN(e.countPace)) {e.innerHTML='countPace is NAN: '+pace; return;}
		e.decimals=Math.pow(10,decimals);
		if (isNaN(e.decimals)) {e.innerHTML='decimals is NAN: '+decimals; return;}
		e.onTimerHit=function()
		{
			var now=new Date();
			var tmp=this.initialValue+(now.getTime()-this.started.getTime())*this.countPace;
			this.innerHTML=padding ? tmp.toFixed(2) : Math.round(tmp*this.decimals)/this.decimals;
		}
		timers.push(e);
	} catch(x) {}
}

	setInterval(function()
	{
		for(var i in timers)
		{
			timers[i].onTimerHit();
		}
	},333);

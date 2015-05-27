<?php

header('Content-type: text/javascript; charset=utf-8');
require_once("presenterphps.php");
require_once("utils/gameutils.php");
ob_start();

$unitNames=array();
foreach($config['units'] as $key=>$value)
{
	$unitNames[$key]=$language[$value["languageEntry"]];
}

$buildingNames=array();
foreach($config['buildings'] as $key=>$value)
{
	$buildingNames[$key]=$language[$value["languageEntry"]];
}

$worldEventNames=array();
foreach($config['worldEvents'] as $key=>$value)
{
	$worldEventNames[$key]=$language[$value['langEntry']];
}

include("classes.js.php");

?>
var isTouchDevice = false;


if (!UPDATEREGIONSIZE)
{
	var UPDATEREGIONSIZE=30;
}
var AUTOVILLAGEUPDATEINTERVAL=600; // in seconds
var MAXUPDATEREGIONSIZE=200; // in cells;

var updateRegions=new Object();
var mergeExperimentRegions=new Object();

var mapElm=document.getElementById('maparea');
var elmWidth;
var elmHeight;
var leftPos;
var topPos;
var centerX=0;
var centerY=0;
var mouseX;
var mouseY;
if (!AVGIMAGECOUNT)
	var AVGIMAGECOUNT=300;
var tasklist=new Array();
var playerInfo;
var selectionRect=new SelectionRect();

//var cellSize=50;
var cellSizeX=50,cellSizeY=50;

var tileMatrix=new Array();
var matrixWidth;
var matrixHeight;
var mapCache=new Object();
var needUpdate=false;
var updateLeft,updateRight,updateTop,updateBottom
updateLeft=updateRight=updateTop=updateBottom=0;
var xmlHttp2=new XMLHttpRequest();
var xmlHttp3=new XMLHttpRequest();
var selectedCells=new SelectedCells();
var mouseMode=new DefaultMouseMode();
var villagesById=new Object();
var localWorldEventCache={};
var newWorldEventCount={};
var recentEventCount=0;

var mapImages=new Object();
mapImages.loading=document.getElementById('loading');
mapImages.towncell=document.getElementById('town');
mapImages.grass=document.getElementById('grass');
mapImages.town1=document.getElementById('town1');
mapImages.town2=document.getElementById('town2');
mapImages.town3=document.getElementById('town3');
mapImages.town4=document.getElementById('town4');
mapImages.town5=document.getElementById('town5');
mapImages.town6=document.getElementById('town6');
mapImages.town7=document.getElementById('town7');


/*var IMGXASPECT=1;
var IMGYASPECT=1;
var villageLevels=
[
	{minScore:0, image:mapImages.towncell}
];*/

var IMGXASPECT=100;
var IMGYASPECT=61;

var villageLevels=
[
        {minScore:0, image:mapImages.town1},
        {minScore:100, image:mapImages.town2},
        {minScore:1000, image:mapImages.town3},
        {minScore:10000, image:mapImages.town4},
        {minScore:100000, image:mapImages.town5},
        {minScore:1000000, image:mapImages.town6},
        {minScore:10000000, image:mapImages.town7}
];


if (!tribalMode)
	var tribalMode=false;

if (tribalMode)
{
	IMGXASPECT=53;
	IMGYASPECT=38;

	mapImages.tribalV1=new Image(); mapImages.tribalV1.src='http://hu8.klanhaboru.hu/graphic/map/v1.png';
	mapImages.tribalV2=new Image(); mapImages.tribalV2.src='http://hu8.klanhaboru.hu/graphic/map/v2.png';
	mapImages.tribalV3=new Image(); mapImages.tribalV3.src='http://hu8.klanhaboru.hu/graphic/map/v3.png';
	mapImages.tribalV4=new Image(); mapImages.tribalV4.src='http://hu8.klanhaboru.hu/graphic/map/v4.png';
	mapImages.tribalV5=new Image(); mapImages.tribalV5.src='http://hu8.klanhaboru.hu/graphic/map/v5.png';
	mapImages.tribalV6=new Image(); mapImages.tribalV6.src='http://hu8.klanhaboru.hu/graphic/map/v6.png';

	villageLevels=
	[
		{minScore:0, image:mapImages.tribalV1},
		{minScore:100, image:mapImages.tribalV2},
		{minScore:1000, image:mapImages.tribalV3},
		{minScore:10000, image:mapImages.tribalV4},
		{minScore:100000, image:mapImages.tribalV5},
		{minScore:1000000, image:mapImages.tribalV6}
	];
}



var counters=new Object();

var lastNbIndex=6;

var Colors={'own':'#0000FF','guild':'#0080FF','ally':'#FFFF00','neutral':'#FFFFFF','peace':'#008000','enemy':'#FF0000','':"black",'abandoned':'#808080'};
var DiplomacyName=new Object();
DiplomacyName.own='<?php echo $language["own"]?>';
DiplomacyName.guild='<?php echo $language["guildproperty"]?>';
DiplomacyName.ally='<?php echo $language["ally"]?>';
DiplomacyName.neutral='<?php echo $language["neutral"]?>';
DiplomacyName.peace='<?php echo $language["peace"]?>';
DiplomacyName.enemy='<?php echo $language["war"]?>';
DiplomacyName.black='<?php echo $language["unknown"]?>';

if (!playerVillages) playerVillages={};

UnitDescriptors=<?php echo json_encode($config["units"])?>;
UnitNames=<?php echo json_encode($unitNames)?>;
BuildingDescriptors=<?php echo json_encode($config["buildings"])?>;
BuildingNames=<?php echo json_encode($buildingNames)?>;
minimalArmyValueRate=<?php echo $config["minimalArmyValueRate"]; ?>;
WorldEventName=<?php echo json_encode($worldEventNames); ?>;

// performs deep copy
function clone(o)
{
	if (o.constructor==Array)
	{
		var newArr=[];
		for(var i=0;i<o.length;i++)
		{
			newArr.push(o[i]);
		}
		return newArr;
	}
	var newObj=new Object();
	for(var i in o)
	{
		var property=o[i];
		if (typeof(property)=='object') newObj[i]=clone(property);
		else newObj[i]=property;
	}	
	return newObj;
}

function _(id)
{
	return document.getElementById(id);
}

function spinCounters(interval)
{
	for(var i in counters)
	{
		var counter=counters[i];
		counter.value+=counter.pace*interval/3600000;
		var e=_(i);
		if (e)
		{
			var dec=Math.pow(10,counter.decimals);
			e.innerHTML=Math.floor(counter.value*dec)/dec;
		}
	}
}

function reloadImages()
{
	_('debugspan').innerHTML='';
	for(var i in mapImages)
	{
		var image=mapImages[i];
		var src=image.src;
		var uriPath=src.match(/nightimage.php\?img=[a-z0-9]+/gim);
		image.src=uriPath+'&rnd='+Math.random();
//		_('debugspan').innerHTML+='<span>'+image.src+'</span>';		
	}
}

function selectMouseMode(newMouseMode,radioId)
{
	mouseMode=newMouseMode;
	_(radioId).checked="checked";
}

function selectCells(x,y,width,height)
{
	var leftTop=coordToCell(x,y);
	var rightBottom=coordToCell(x+width,y+height);
//	selectedCells.clearSelection();
	for(var i=leftTop.x;i<=rightBottom.x;i++)
	{
		for(var j=leftTop.y;j<=rightBottom.y;j++)
		{
			if ((mapCache[i]) && (mapCache[i][j]) && (mapCache[i][j].villageInfo) && (mapCache[i][j].villageInfo.diplomaticStance) && (mapCache[i][j].villageInfo.diplomaticStance=='own'))
			{
				selectedCells.selectCell(i,j,!selectedCells.isSelected(i,j));
			}
		}
	}
}

selectionRect.onSelect=selectCells;

function openInWindow(url)
{
	var rId=generateRandomId();
	var separator=url.search(/\?/)<0 ? '?' : '&';
	var iHTML=
//	'<p><a href="javascript:void(_(\''+rId+'\').close())"><?php echo $language["close"];?></a></p>'+
	'<iframe src="'+(url+separator+'parentdivid='+rId)+'" style="width:740px; height:600px">'+'<?php echo xprintf($language["yourbrowsernotsupportiframe"],array("'+url+'"));?>'+'</iframe>'+
	''
	;
	var elm=genFloatingBox(iHTML,rId,0,0);	
	elm.style.width="800px";
	document.body.appendChild(elm);
	centerElement(elm);
	makeDraggable(elm);
	bringElementToFront(elm);
	constrainElementInside(elm,0);
/*	var winHandle=window.open(url,'guildwindow','width=740,height=600,scrollbars=1,toolbar=1');
	winHandle.focus();;*/
}

function dumpObject(obj,pre)
{
	if (!pre) pre='    ';
	if (pre.length>20) return "[Object]"; // 5
	var str="";
	str+=pre+"{\r\n";
	for(var i in obj)
	{
		var value="";
		if (typeof(obj[i])=="object") value=dumpObject(obj[i],pre+'    ');
		else value=obj[i];
		str+=pre+i+" => "+value+"\r\n";
	}
	str+=pre+"}\r\n";
	return str;
}

function genericAjaxEventHandler()
{
	if (xmlHttp.readyState==4)
	{
		if (xmlHttp.status>400)
		{
			alert("ERROR HTTP STATUS: "+xmlHttp.status);
			return false;
		}
		if (!xmlHttp.responseXML)
		{
			alert(xmlHttp.responseText);
			return false;
		}
		else
			return true;
	}
}

function setPercent(countControlId,percentControlId,maxAmount)
{
	var amount=parseInt(_(countControlId).value,10);
	if (isNaN(amount)) 
	{
		_(countControlId).value='0';
		amount=0;
	}
	if (maxAmount==0)
		_(percentControlId).innerHTML='0';
	else
		_(percentControlId).innerHTML=Math.floor(100*(amount/maxAmount)*1000)/1000;
}

function highlightVillage(villageId)
{
	if (villagesById[villageId])
	{
		var village=villagesById[villageId];
		initMap(parseInt(village.x,10),parseInt(village.y,10));
	}
}

function sendTroops(launcherVillages,destinationVillageId,action,operationName)
{
	var task=new Object();
	var heroMod=_('launchhero').checked ? 'WITHHERO':'WITHOUTHERO';
	task.command=['SENDTROOPS','FOR',action,'TO',destinationVillageId,'TARGET',_('catapulttarget').value,heroMod,'FROM'];
	task.villages=[];
	var destinationVillage=villagesById[destinationVillageId];
	for(var i in launcherVillages)
	{
		task.command.push(launcherVillages[i]);
		var village=villagesById[launcherVillages[i]];
		task.villages.push([village.x,village.y]);
	}
	task.command.push('AMOUNTS');
	var unitsToSend=[];
	var emptyWave=true;
	var armyValue=0;
	for(var i in UnitDescriptors)
	{
		var amount=parseFloat(_('amount_'+i).value);
		task.command.push(amount);		
		unitsToSend.push(Math.floor(amount));
		emptyWave&=amount<=0;
		armyValue+=UnitDescriptors[i].cost*amount;
	}
	if (emptyWave && (!_('launchhero').checked)) 
	{
		alert('<?php echo $language["cantlaunchemptywave"]; ?>');
		return false;
	}
	if ((action!='move') && (armyValue<minimalArmyValueRate*playerInfo['goldProduction']))
	{
		alert('<?php echo xprintf($language["pleasesendmoretroops"],array("'+Math.ceil(minimalArmyValueRate*playerInfo['goldProduction'])+'")); ?>');
		return false;
	}
	task.text='<?php echo xprintf($language["sendtroopscommand"],array("['+launcherVillages+']","['+unitsToSend+']","'+destinationVillage.villageName+'","'+operationName+'")); ?>';
	
	tasklist.push(task);
	return true;
	
}

function setArmyValue(armyInputIdPrefix,armyValueSpanId)
{

	var armyValue=0;
	var span=_(armyValueSpanId);
	for(var i in UnitDescriptors)
	{
		var descriptor=UnitDescriptors[i];
		var inputId=armyInputIdPrefix+i;
		var input=_(inputId);
		armyValue+=descriptor['cost']*parseInt(input.value,10);
	}
	span.innerHTML=armyValue;
}

if (!toggleElement)
{
	toggleElement=function(id,link)
	{
		var elm=document.getElementById(id);
		if (elm.style.display=='none')
		{
			elm.style.display='block';
			if (link)
			{
				link.prevText=link.innerHTML;
				link.innerHTML='[X]';
			}
		}
		else
		{
			elm.style.display='none';
			if (link && link.prevText)
			{
				link.innerHTML=link.prevText;
			}
		}
	};
}


function setupAction(action,launcherVillages,x,y,destinationVillageId,mp)
{
	if (prevCellInfo) 
	{
		prevCellInfo.close();
	}
	if (launcherVillages.length==0) return;
	if (action=='heromove')
	{
		var launcherVillage=villagesById[launcherVillages[0]];
		var task=new Object();
		task.command=['HEROMOVE',destinationVillageId];
		task.villages=[[launcherVillage.x[0],launcherVillage.y[0]]];
		task.text='<?php echo xprintf($language["heromovetask"],array("'+villagesById[launcherVillages[0]].villageName+'","'+villagesById[destinationVillageId].villageName+'")); ?>';
		tasklist.push(task);
	}
	else if (action=='settle')
	{
		var launcherVillage=villagesById[launcherVillages[0]];
		var diplomats=Math.floor(launcherVillage.diplomats[0]);
		if (diplomats<1) return;
		var xp=parseFloat(_('expansionpointindicator').innerHTML);
		if (xp<1) return;
		var task=new Object();
		task.text='<?php echo xprintf($language["settlevillagetask"],array("'+villagesById[launcherVillages[0]].villageName+'","'+x+'","'+y+'")); ?>';
		task.command=['SETTLEVILLAGE',launcherVillages[0],x,y];
		task.villages=[[launcherVillage.x[0],launcherVillage.y[0]]];
		tasklist.push(task);
		_('expansionpointindicator').innerHTML=xp-1;
	}
	else if ((action=='move') || (action=='attack') || (action=='recon') || (action=='raid'))
	{
		var operationname;
		if (action=='move') operationname='<?php echo $language[$config["operations"]["move"]["langName"]]; ?>';
		if (action=='attack') operationname='<?php echo $language[$config["operations"]["attack"]["langName"]]; ?>';
		if (action=='recon') operationname='<?php echo $language[$config["operations"]["recon"]["langName"]]; ?>';
		if (action=='raid') operationname='<?php echo $language[$config["operations"]["raid"]["langName"]]; ?>';
		
		var heroVillage=0;
		if (playerInfo && playerInfo.hero && playerInfo.hero[0] && playerInfo.hero[0].inVillage)
		{
			heroVillage=playerInfo.hero[0].inVillage;
		}
		var isHero=false;
		var unitAmounts=new Object();
		var unitNames=new Object();
		var unitKeys=new Array();
		for(var i=0;i<launcherVillages.length;i++)
		{
			var vId=launcherVillages[i];
			isHero|= vId==heroVillage;
			var village=villagesById[vId];
			for(j in UnitDescriptors)
			{
				var levelName=UnitDescriptors[j].countDbName;
				if (!unitAmounts[j]) unitAmounts[j]=0;
				unitAmounts[j]+=parseInt(village[levelName],10);
			}
		}
		
		var valueText='<p><?php echo xprintf($language["armyvaluetext"],array("'+Math.ceil(playerInfo.goldProduction*minimalArmyValueRate)+'","<span id=\"armyvalue\">0</span>")); ?></p>';
		var amountText='<table class="center">';
		amountText+='<tr>';
		for(var i in UnitDescriptors)
		{
			var desc=UnitDescriptors[i];
			amountText+='<td><img src="'+desc.image+'" alt="'+UnitNames[i]+'" title="'+UnitNames[i]+'" onmouseover="showTooltip(\''+UnitNames[i]+'\')" onmouseout="removeTooltip()"></td>';
		}
		var hName=playerInfo.hero[0].name;
		amountText+='<td>'+(playerInfo.hero[0].avatarLink ? '<img  style="width:50px; height:50px" src="'+playerInfo.hero[0].avatarLink+'" alt="'+hName+'" title="'+hName+'" onmouseover="showTooltip(\''+hName+'\')" onmouseout="removeTooltip()">':'')+'</td>';
		amountText+='</tr>';
		amountText+='<tr>';
		for(var i in UnitDescriptors)
		{
			var desc=UnitDescriptors[i];
			amountText+='<td><input type="text" style="width:5em" id="amount_'+i+'" onclick="this.select()" onkeyup="setArmyValue(\'amount_\',\'armyvalue\')" value="0"></td>';
		}
		amountText+='<td rowspan="2"><input '+(!isHero ? 'disabled="disabled"': '')+'type="checkbox" id="launchhero" onmouseover="showTooltip(\'<?php echo $language["launchheroifinvillage"];?>\')" onmouseout="removeTooltip()"></td>';
		amountText+='</tr>';
		amountText+='<tr>';
		for(var i in UnitDescriptors)
		{
			var desc=UnitDescriptors[i];
			amountText+='<td>'+'<a href="javascript:void((function(){_(\'amount_'+i+'\').value='+unitAmounts[i]+';setArmyValue(\'amount_\',\'armyvalue\')})())">('+unitAmounts[i]+')</a></td>';
		}
		amountText+='</tr>';
/*		for(var i in UnitDescriptors)
		{
			amountText+=
			'<tr>'+
			'<td>'+UnitNames[i]+'</td>'+
			'<td><input type="text" style="width:7em" id="amount_'+i+'" onclick="this.select()" onkeyup="setPercent(\'amount_'+i+'\',\'percent_'+i+'\','+unitAmounts[i]+'); setArmyValue(\'amount_\',\'armyvalue\')" value="0"></td>'+
			'<td>'+'<a href="javascript:void((function(){_(\'amount_'+i+'\').value='+unitAmounts[i]+';setPercent(\'amount_'+i+'\',\'percent_'+i+'\','+unitAmounts[i]+'); setArmyValue(\'amount_\',\'armyvalue\')})())">('+unitAmounts[i]+')</a></td>'+
			'<td><span id="percent_'+i+'">0</span>%</td>'+
			'</tr>' ;
			unitKeys.push("'"+i+"'");
		}*/
		amountText+='</table>';
		
		var catapultText='';
			catapultText='<p><?php echo $language["pleasechoosecatapulttarget"]; ?><?php echo generateBuildingSelector("catapulttarget","catapulttarget");?></p>';
		
		var rId=generateRandomId();
		var iHTML=
//		'<p><a href="javascript:void(_(\''+rId+'\').close())"><?php echo $language["close"];?></a></p>'+
		'<h3><?php echo $language["launchtroops"]; ?></h3>'+
		'<p style="width:400px"><?php echo $language["operation"]; ?>'+operationname+'</p>'+
		'<a href="javascript:void(toggleElement(\'sendtroopsinfo\'))"><?php echo $language["moreinfo"];?></a>'+
		'<div id="sendtroopsinfo" style="display:none">'+
			'<p style="width:400px"><?php echo $language["becarefulsendtroopsmultiplevillages"]; ?></p>'+
			'<p style="width:400px"><?php echo $language["troopnumberdescription"]; ?></p>'+
		'</div>'+
		valueText+
		amountText+
		'<p style="width:400px"></p>'+
		catapultText+
		'<p class="center"><a href="javascript:void((function(){if (sendTroops(['+launcherVillages+'],'+destinationVillageId+',\''+action+'\',\''+operationname+'\')) _(\''+rId+'\').close();})())"><?php echo $language["sendtroops"]; ?></a></p>'+
		'';
		var elm=genFloatingBox(iHTML,rId,mp.x,mp.y);
		document.body.appendChild(elm);
		makeDraggable(elm);
		bringElementToFront(elm);
		constrainElementInside(elm,20);
		prevCellInfo=elm;
		prevCellInfo.onClose=function()
		{
			prevCellInfo=null;
		};
	}
	else alert('Unknown or not implemented command! Please notify the administrator if you arrived here from a link.');
}

function cellAction(cellX,cellY,mp)
{
	var rId=generateRandomId();
	var village;
	if (mapCache[cellX] && mapCache[cellX][cellY])
	{
		village=mapCache[cellX][cellY].villageInfo;
	}
	
	
	
	var launcherVillagesText='<ul>';
	launcherVillagesIdArray=new Array();
	var myVillage=true;
	selectedCells.forAllSelected
	(
		function(x,y)
		{
			if (mapCache[x] && mapCache[x][y] && mapCache[x][y].villageInfo)
			{
				var village=mapCache[x][y].villageInfo;
				var isMyVillage=(village.diplomaticStance && (village.diplomaticStance=='own'));
				if (isMyVillage || (village.id[0]==playerInfo.hero[0].inVillage[0]))
				{
					// ez itt kopipesztes.
					launcherVillagesText+='<li><a href="javascript:void(showCellInfo('+x+','+y+',{\'x\':'+mp.x+',\'y\':'+mp.y+'}))"><?php echo xprintf($language["villageanduser"],array("'+village.villageName+'","'+village.userName+'")); ?></a></li>';
					launcherVillagesIdArray.push(village.id[0]);
					myVillage&=isMyVillage;
				}
			}
		}
	);
	launcherVillagesText+='</ul>';
	if (launcherVillagesIdArray.length==0) return;

	var possibleCommandText='<ul>';
	if (!village)
	{
		possibleCommandText+='<li><a href="javascript:void((function(){setupAction(\'settle\',['+launcherVillagesIdArray+'],'+cellX+','+cellY+',0,{\'x\':'+mp.x+',\'y\':'+mp.y+'});_(\''+rId+'\').close();})())"><?php echo $language["settlevillage"]; ?></a></li>';
	}
	else
	{
		if (launcherVillagesIdArray.length==1)
		{
			var senderVillage=villagesById[launcherVillagesIdArray[0]];
			if (senderVillage.id[0]==playerInfo.hero[0].inVillage[0])
			{
				possibleCommandText+='<li><a href="javascript:void((function(){setupAction(\'heromove\',['+launcherVillagesIdArray+'],'+cellX+','+cellY+','+village.id+',{\'x\':'+mp.x+',\'y\':'+mp.y+'});_(\''+rId+'\').close();})())"><?php echo $language["movehero"]; ?></a></li>';		
			}
		}
		if (myVillage)
		{
			if (village.diplomaticStance)
			{
				if ((village.diplomaticStance=='own') || (village.diplomaticStance=='guild') || (village.diplomaticStance=='ally') || (village.diplomaticStance=='neutral'))
				{
					possibleCommandText+='<li><a href="javascript:void((function(){setupAction(\'move\',['+launcherVillagesIdArray+'],'+cellX+','+cellY+','+village.id+',{\'x\':'+mp.x+',\'y\':'+mp.y+'});_(\''+rId+'\').close();})())"><?php echo $language["movetroops"]; ?></a></li>';
				}
				if ((village.diplomaticStance=='enemy') || (village.diplomaticStance=='neutral'))
				{
					possibleCommandText+='<li><a href="javascript:void((function(){setupAction(\'attack\',['+launcherVillagesIdArray+'],'+cellX+','+cellY+','+village.id+',{\'x\':'+mp.x+',\'y\':'+mp.y+'});_(\''+rId+'\').close();})())"><?php echo $language["attack"]; ?></a></li>';
					possibleCommandText+='<li><a href="javascript:void((function(){setupAction(\'raid\',['+launcherVillagesIdArray+'],'+cellX+','+cellY+','+village.id+',{\'x\':'+mp.x+',\'y\':'+mp.y+'});_(\''+rId+'\').close();})())"><?php echo $language["raid"]; ?></a></li>';
					possibleCommandText+='<li><a href="javascript:void((function(){setupAction(\'recon\',['+launcherVillagesIdArray+'],'+cellX+','+cellY+','+village.id+',{\'x\':'+mp.x+',\'y\':'+mp.y+'});_(\''+rId+'\').close();})())"><?php echo $language["recon"]; ?></a></li>';
				}
			}
		}
	}
	possibleCommandText+='</ul>';
	
	iHTML=
//		'<p><a href="javascript:void(_(\''+rId+'\').close())"><?echo $language["close"];?></a></p>'+
		'<p style="width:400px"><?echo $language["launchervillages"];?></p>'+launcherVillagesText+
		'<p style="width:400px"><?echo $language["targetcell"];?>['+cellX+';'+cellY+']</p>'+
		'<p style="width:400px"><?echo $language["targetvillage"];?>'+(village ?
			 '<a href="javascript:void(showCellInfo('+cellX+','+cellY+',{\'x\':'+mp.x+',\'y\':'+mp.y+'}))"><?php echo xprintf($language["villageanduser"],array("'+village.villageName+'","'+village.userName+'")); ?></a>' 
			 :'<?php echo $language["na"];?>')+'</p>'+
		'<?php echo $language["possiblecommands"]; ?>' +possibleCommandText+
		'';
	var elm=genFloatingBox(iHTML,rId,mp.x,mp.y);
	document.body.appendChild(elm);
	makeDraggable(elm);
	bringElementToFront(elm);
	constrainElementInside(elm,20);
}

function genOnUpdateVillage(x,y)
{
	return function()
	{
		if (xmlHttp.readyState==4)
		{
			if (xmlHttp.status>400)
			{
				alert("ERROR HTTP STATUS: "+xmlHttp.status);
				return;
			}
			if (xmlHttp.responseText!='') alert(xmlHttp.responseText);
			else
			{
				addUpdatable(x,y);
			}
		}
	}
}

function updateVillage(villageId,x,y)
{
	ajaxPost("dofetchvillage.php","id="+encodeURIComponent(villageId)+"&x="+x+"&y="+y,updateCallback);
}

function generateRandomId()
{
	var s="";
	for(var i=0;i<30;i++)
	{
		s+=String.fromCharCode('A'.charCodeAt(0)+Math.floor(Math.random()*26));
	}
	return s;
}

function commitTasks()
{
	var cmdStr="";
	for(var i=0;i<tasklist.length;i++)
	{
		var cmd=tasklist[i];
		for(var j in cmd.villages)
		{
			addUpdatable(cmd.villages[j][0],cmd.villages[j][1]);
		}
		for(var j=0;j<cmd.command.length;j++)
		{
//			cmdStr+='"'+cmd.command[j].toString().replace(new RegExp('"','g'),'\\"').replace(new RegExp('\\\\([^"])','g'),'\\\\$1')+'" ';
			if (j>0) cmdStr+=' ';
			cmdStr+=cmd.command[j].toString().replace(new RegExp('\\s','g'),'\\ ').replace(new RegExp('\\n','g'),'\\\n').replace(new RegExp('\\\\([^\\s\\n]|$)','g'),'\\\\$1');
		}
		cmdStr+="\n";
	}
	ajaxPost("dotasks.php","&tasks="+encodeURIComponent(cmdStr),genericAjaxEventHandler);
	clearTasks();
}

function clearTasks()
{
	for(var i in tasklist)
	{
		var undo=tasklist[i].undo;
		if (undo) undo();
	}
	tasklist=[];
	getPlayerInfo();
}

function upgradeBuilding(villageId,buildingName)
{
	var village=villagesById[villageId];
	var buildingDescriptor=BuildingDescriptors[buildingName];
	var level=parseInt(village[buildingDescriptor.buildingLevelDbName],10);
	var bpoints=parseFloat(village.buildPoints);
	var gold=counters['goldindicator'].value;
	var cost=eval('('+buildingDescriptor.jsCostFunction+')')(level);
	if (bpoints<1) return;
	if (gold<cost) return;
	counters['goldindicator'].value-=cost;
	village[buildingDescriptor.buildingLevelDbName]=level+1;
	village.buildPoints-=1;
	var task=new Object();
	task.text="<?php echo xprintf($language['updatebuildingtask'],array('"+village.villageName+"','"+BuildingNames[buildingName]+"'))?>";
	task.villages=[[village.x,village.y]];
	task.command=['UPGRADEBUILDING',villageId,buildingName];
	task.undo=function()
	{
		counters['goldindicator'].value+=cost;
		village[buildingDescriptor.buildingLevelDbName]=level;
		village.buildPoints+=1;		
	}
	tasklist.push(task);
	updateCurrentCellInfo();
}

function constrainElementInside(element,padding)
{
	if (!padding) padding=0;
	var cs=getWindowClientSize();
	var pageWidth=cs[0];
	var pageHeight=cs[1];
	var width=element.offsetWidth;
	var height=element.offsetHeight;
	var left=parseInt(element.style.left,10);
	var top=parseInt(element.style.top,10);
	var right=width+left;
	var bottom=height+parseInt(element.style.top,10);
	if ((width>pageWidth) || (height>pageHeight))
	{
		element.style.overflow="auto";
		if (width>pageWidth-2*padding) 
		{
			element.style.width=pageWidth-2*padding+"px";
			width=pageWidth-2*padding;
		}
		if (height>pageHeight-2*padding)
		{
			element.style.height=pageHeight-2*padding+"px";
			height=pageHeight-2*padding;
		}
	}
	if (left<padding) element.style.left=padding+"px";
	if (top<padding) element.style.top=padding+"px";
	if (right>pageWidth-padding) element.style.left=pageWidth-width-padding+"px";
	if (bottom>pageHeight-padding) element.style.top=pageHeight-height-padding+"px";
/*	if (bottom>cs[1]) elm.style.top=top-(bottom-cs[1]+20)+"px";
	if (right>cs[0]) elm.style.left=left-(right-cs[0]+20)+"px";*/
}

function preventBubble(element)
{
	function cancelBubble(e)
	{
		var ev=e || window.event;
		ev.cancelBubble=true;
	}
	element.onclick=cancelBubble;
	element.onmousedown=cancelBubble;
}

function doRenameVillage(villageId,x,y,oldVillageName,newVillageName)
{
	var task=new Object();
	task.villages=[[x,y]];
	task.text="<?php echo xprintf($language['renamevillagetask'],array('"+oldVillageName+"','"+villageId+"','"+newVillageName+"')); ?>";
	task.command=['RENAMEVILLAGE',villageId,newVillageName];
	task.undo=function()
	{
		villagesById[villageId].villageName=oldVillageName;
	}
	tasklist.push(task);
}

function renameVillage(villageId,x,y)
{
	var span=_('villagenamespan');
	if (!span) return;
	if (!span.firstChild || (span.firstChild.nodeType!=3)) return;
	var text=span.innerHTML;
	var pNode=span.parentNode;
	span.innerHTML='';
//	pNode.removeChild(span);
	var input=document.createElement('input');
	input.setAttribute('value',text);
	input.setAttribute('type','text');
	input.setAttribute('id','newvillageName');
	input.onmousemove=input.onmousedown=input.onmouseup=function(e){var ev=e||window.event; ev.cancelBubble=true;}
	input.onkeypress=
	(function(villageId,x,y,oldVillageName)
	{
		return function(e)
		{
			var ev=e || window.event;
			if ((e.keyCode==13) || (e.keyCode==27))
			{
				if (e.keyCode==13)
				{
					doRenameVillage(villageId,x,y,oldVillageName,this.value);
					villagesById[villageId].villageName=this.value;
				}
				var value=this.value;
				var pNode=this.parentNode;
/*				setTimeout((function(parent,child){return function(){parent.removeChild(child)}})(pNode,this),100);
				var span=document.createElement('span');
				span.id='villagenamespan';
				span.innerHTML=value;
				pNode.appendChild(span);*/
				pNode.innerHTML=value;
				
			}
			ev.cancelBubble=true;
		};
	})(villageId,x,y,text);
	span.appendChild(input);
	input.focus();
	
}

function doMassTraining()
{
	var select=_('mtVillageSelector');
	if (!select) return;
	var selectedVillageIds=[];
	for(var i=0;i<select.options.length;i++)
	{
		var option=select.options[i];
		if (option.selected) selectedVillageIds.push(option.value);
	}
	if (selectedVillageIds.length==0) 
	{
		alert('<?php echo $language["onevillagemustbeselected"]; ?>');
		return;
	}
	// calculate the gold needed
	var goldNeeded=0;
	var amounts=[];
	for(var i in UnitDescriptors)
	{
		var descriptor=UnitDescriptors[i];
		var input=_('amount_'+i);
		var amount=parseInt(input.value,10);
		goldNeeded+=amount*descriptor.cost;
		amounts.push(amount);
	}
	if (isNaN(goldNeeded))
	{
		alert('<?php echo $language["specifyvalidnumbers"]; ?>');
		return;
	}
	if (goldNeeded>counters['goldindicator'].value)
	{
		alert('<?php echo $language["notenoughgold"]; ?>');
		return;
	}
	// do the modification client side;
	counters['goldindicator'].value-=goldNeeded;	
	for(var i=0;i<selectedVillageIds.length;i++)
	{
		var village=villagesById[selectedVillageIds[i]];
		var ctr=0;
		for(var j in UnitDescriptors)
		{
			var amount=amounts[ctr++];
			var descriptor=UnitDescriptors[j];
			//village[descriptor.trainingDbName]=parseFloat(village[descriptor.trainingDbName])+amount;
		}
	}
	// set up the task
	var task={};
	task.villages=[];
	for(var i=0;i<selectedVillageIds.length;i++)
	{
		var village=villagesById[selectedVillageIds[i]];
		task.villages.push([village.x,village.y]);
	}
	task.text='<?php echo xprintf($language["masstrainingtask"],array("'+amounts+'","'+selectedVillageIds+'")); ?>';
	task.command=['MASSTRAINING'];
	for(var i=0;i<amounts.length;i++)
	{
		task.command.push(amounts[i]);
	}
	task.command.push('IN');
	for(var i=0;i<selectedVillageIds.length;i++)
	{
		task.command.push(selectedVillageIds[i]);
	}
	task.undo=
	(
		function(gold,villageIds,amounts)
		{
			return function()
			{
				counters['goldindicator'].value+=gold;
				for(var i=0;i<villageIds.length;i++)
				{
					var village=villagesById[villageIds[i]];
					var ctr=0;
					for(var j in UnitDescriptors)
					{
						var amount=amounts[ctr++];
						var descriptor=UnitDescriptors[j];
						//village[descriptor.trainingDbName]-=amount;
					}
				}
			}
		}
	)(goldNeeded,selectedVillageIds,amounts);
	// add the task to the tasklist
	tasklist.push(task);
	// close the mt window
	if (prevMtDivId) _(prevMtDivId).close();
}

function normalizeForGold(elm,unitPrice)
{
	var gold=counters['goldindicator'].value;
	var maxValue=gold/unitPrice;
	var numValue=parseInt(elm.value,10);
	if (isNaN(numValue)) numValue=0;
	if (numValue>maxValue) numValue=Math.floor(maxValue);
	elm.value=numValue;
}

function getOwnVillageOptions()
{
	var launcherVillagesText='';
	var anySelected=false;
	for(var i in villagesById)
	{
		var village=villagesById[i];
		if (village.diplomaticStance && (village.diplomaticStance=='own'))
		{
			var selected=selectedCells.isSelected(village.x,village.y);
			anySelected|=selected;
		}
	}
	for(var i in villagesById)
	{
		var village=villagesById[i];
		if (village.diplomaticStance && (village.diplomaticStance=='own'))
		{
			var selected=selectedCells.isSelected(village.x,village.y);
			anySelected|=selected;
			launcherVillagesText+='<option value="'+village.id+'" '+(selected || !anySelected ? 'selected="selected"':'')+'>'+village.villageName+'</option>';				
		}
	}
	return launcherVillagesText;
}

var mtPos;
var prevMtDivId;
function massTraining()
{
	if (prevMtDivId)
	{
		var e=_(prevMtDivId);
		if (e) e.close();
	}
	var launcherVillagesIdArray=new Array();
	var launcherVillagesText=getOwnVillageOptions();
	var selectedOwnVillageCount=0;
	var myVillage=true;
	
	var selId='mtVillageSelector';
	var unitTable='<table>';
	for(var i in UnitDescriptors)
	{
		var unitDescriptor=UnitDescriptors[i];
		unitTable+='<tr><td>'+UnitNames[i]+'</td><td><input type="text" style="width:7em" id="amount_'+i+'" onkeyup="normalizeForGold(this,'+unitDescriptor.cost+'); setArmyValue(\'amount_\',\'armycost\')" value="0"></td></tr>';
	}
	unitTable+=
	'<tr><td colspan="2"><input type="button" value="<?php echo $language["train"]; ?>" onclick="doMassTraining()"></td></tr>'+
	'</table>';
	
	var iHTML=
	'<h1><?php echo $language["masstraining"]; ?></h1>'+
	'<table>'+
		'<tr>'+
			'<td>'+
				'<p><?php echo $language["villagesloaded"]; ?></p>'+
				'<select multiple="multiple" size="15" id="'+selId+'">'+launcherVillagesText+'</select>'+
			'</td>'+
			'<td>'+
				'<p style="width:200px"><?php echo $language["everyvillageyouselectedwilltrain"];?></p>'+
				'<p><?php echo xprintf($language["totalcost"],array("<span id=\"armycost\"></span>"));?></p>'+
				unitTable+
				'<p style="width:200px"><?php echo $language["villagesselectedmap"];?></p>'+
			'</td>'+
		'</tr>'+
	'</table>'+
	'';
	if (!mtPos) mtPos={'x':mouseX,'y':mouseY};
	prevMtDivId=generateRandomId();
	var e=genFloatingBox(iHTML,prevMtDivId,mtPos.x,mtPos.y);
	e.onClose=function()
	{
		mtPos={'x':parseInt(this.style.left),'y':parseInt(this.style.top)};
	}
	document.body.appendChild(e);
	makeDraggable(e);
	bringElementToFront(e);
	var s=_(selId);
	s.onmousemove=function(ev)
	{
		ev=ev || window.event;
		ev.cancelBubble=true;
		if (ev.stopPropagation) ev.stopPropagation();
	};
	// select all when nothing is selected
/*	if (!anySelected)
	{
		var select=_(selId);
		for(var i=0;i<select.options.length;i++)
		{
			select.options[i].selected=true;
		}
	}*/
}

function doMassBuilding()
{
	var select=_('mbVillageSelector');
	var launcherIds=[];
	for(var i=0;i<select.options.length;i++)
	{
		var option=select.options[i];
		if (option.selected)
		launcherIds.push(option.value);
	}
	var levelLimit;
	if (_("maxlevelnolimit").checked) levelLimit=-1;
	if (_("maxlevellimit").checked) levelLimit=parseInt(_("maxlevelvalue").value,10);
	if (isNaN(levelLimit))
	{
		_("maxlevelvalue").focus();
		return;
	}
	var maxgold=parseInt(_("spendmaxgold").value,10);
	if (isNaN(maxgold))
	{
		_("spendmaxgold").focus();
		return;
	}
	var buildingType;
	var selectedBd;
	for(var i in BuildingDescriptors)
	{
		var bd=BuildingDescriptors[i];
		if (_("bldng"+i).checked)
		{
			buildingType=i;
			selectedBd=bd;
			break;
		}
	}
	if (!buildingType) return;
	var task={}
	task.villages=[];
	task.command=['MASSBUILD'];
	task.text='<?php echo xprintf($language["massbuildingtask"],array("'+launcherIds+'","'+(levelLimit>=0 ? levelLimit : '${language["nolevellimit"]}')+'","'+maxgold+'","'+BuildingNames[buildingType]+'"));?>';
	for(var i=0;i<launcherIds.length;i++)
	{
		task.command.push(launcherIds[i]);
		var village=villagesById[launcherIds[i]];
		task.villages.push([village.x,village.y]);
	}
	task.command.push('MAXLEVEL');
	task.command.push(levelLimit);
	task.command.push('MAXGOLD');
	task.command.push(maxgold);
	task.command.push('BUILDING');
	task.command.push(buildingType);
	tasklist.push(task);
	_(prevMbDivId).close();
	
	
	
}

var mbPos;
var prevMbDivId;
function massBuilding()
{
	if (prevMbDivId)
	{
		var e=_(prevMbDivId);
		if (e) e.close();
	}
	var launcherVillagesText=getOwnVillageOptions();
	var selId="mbVillageSelector";
	var buildingRadios='';
	for(var i in BuildingDescriptors)
	{
		var bd=BuildingDescriptors[i];
		buildingRadios+='<input type="radio" id="bldng'+i+'" name="buildingselector"><label for="bldng'+i+'">'+BuildingNames[i]+'</label><br>';
	}
	var iHTML=
	'<h1><?php echo $language["massbuilding"];?></h1>'+
	'<table>'+
	'<tr>'+
	'<td>'+
		'<p><?php echo $language["villagesloaded"]; ?></p>'+
		'<select multiple="multiple" id="'+selId+'" size="20">'+launcherVillagesText+'</select>'+
	'</td>'+
	'<td>'+
	'<div style="width:300px"><p><?php echo $language["massbuildinginfo"];?></p></div>'+
	'<fieldset>'+
	'<legend><?php echo $language["costraints"];?></legend>'+
	'<p>'+
	'<input type="radio" name="maxlevel" id="maxlevelnolimit" checked="checked"><label for="maxlevelnolimit"><?php echo $language["spendallbuildpoints"];?></label><br>'+
	'<input type="radio" name="maxlevel" id="maxlevellimit"><label for="maxlevellimit"><?php echo $language["buildtolevel"]; ?></label><input style="width:7em" type="text" id="maxlevelvalue" value="0"><br>'+
	'<?php echo $language["spendmaxgold"];?> <input style="width:7em" type="text" value="'+parseInt(counters['goldindicator'].value,10)+'" id="spendmaxgold">'+
	'</p>'+
	'</fieldset>'+
	'<fieldset>'+
	'<legend><?php echo $language["buildthisbuilding"];?></legend>'+
	'<p>'+
	buildingRadios+
	'</p>'+
	'</fieldset>'+
	'<p><input type="button" value="<?php echo $language["go"];?>" id="doMassBuilding"></p>'+
	'</td></tr>'+
	'</table>'+
	''
	;
	if (!mbPos) mbPos={'x':mouseX,'y':mouseY};
	prevMbDivId=generateRandomId();
	var elm=genFloatingBox(iHTML,prevMbDivId,mbPos.x,mbPos.y);
	document.body.appendChild(elm);
	constrainElementInside(elm);
	makeDraggable(elm);
	bringElementToFront(elm);
	_(selId).onmousemove=function(ev)
	{
		ev=ev || window.event;
		ev.cancelBubble=true;
		if (ev.stopPropagation) ev.stopPropagation();
	};
	_('doMassBuilding').onclick=doMassBuilding;
}

function doTrainUnits(villageId,unitName,amount)
{
	var intAmount=parseInt(amount,10);
	if (isNaN(intAmount)) return;
	var task=new Object();
	var village=villagesById[villageId];
	var cost=UnitDescriptors[unitName].cost;
	task.villages=[[village.x,village.y]];
	task.text="<?php echo xprintf($language['trainingtask'],array('"+village.villageName+"','"+intAmount+"','"+UnitNames[unitName]+"')); ?>";
	task.command=['TRAINUNITS',villageId,intAmount,unitName];
	task.undo=
	function()
	{
		villagesById[villageId][UnitDescriptors[unitName].trainingDbName]=parseFloat(villagesById[villageId][UnitDescriptors[unitName].trainingDbName])-intAmount;
		counters['goldindicator'].value+=intAmount*cost;
	};
//	alert(villagesById[villageId][UnitDescriptors[unitName].trainingDbName]);
	villagesById[villageId][UnitDescriptors[unitName].trainingDbName]=parseFloat(villagesById[villageId][UnitDescriptors[unitName].trainingDbName])+intAmount;
//	alert(villagesById[villageId][UnitDescriptors[unitName].trainingDbName]);
	tasklist.push(task);
	counters['goldindicator'].value-=intAmount*cost;
	updateCurrentCellInfo();
}

function showUnitTrainingInput(villageId,unitName,sId)
{
	var span=_(sId);
	while(span.childNodes.length>=1) {span.removeChild(span.firstChild);}
	var input=document.createElement('input');
	input.value="";
	input.type="text";
	input.style.width="5em";
	input.onmousemove=input.onmousedown=input.onmouseup=function(e){var ev=e || window.event; ev.cancelBubble=true; if (ev.stopPropagation) ev.stopPropagation(); }
	input.onkeyup=
	(
		function(villageId,unitName)
		{
			return function(e)
			{
				var ev=e || window.event;
				var value=parseInt(this.value,10);
				var cost=UnitDescriptors[unitName].cost;
				if (!isNaN(value))
				{
					var gold=parseInt(counters['goldindicator'].value,10);
					if (value<0) value=0;
					if (value>gold/cost) value=Math.floor(gold/cost);
					this.value=value;
				}
			};
		}
	)(villageId,unitName);
	input.onkeypress=
	(
		function(villageId,unitName)
		{
			return function(e)
			{
				var ev=e || window.event;
			
				if ((ev.keyCode==13) || (ev.keyCode==27))
				{
					if (ev.keyCode==13)
					{
						doTrainUnits(villageId,unitName,this.value);
					}
//					this.parentNode.removeChild(this);
				}
			};
		}
	)(villageId,unitName);
	span.appendChild(input);
}

function makeUndraggable(elm)
{
	elm.onmousedown=elm.onmouseup=elm.onmousemove=elm.onscroll=function(e)
	{
		var ev=e || window.event;
		ev.cancelBubble=true;
	};
}

function makeDraggable(elm)
{
    function mousedownDragHandler(e)
	{
		var ev=e || window.event;
		ev.cancelBubble=true;
		if (ev.stopPropagation()) ev.stopPropagation();
		this.grabbed=true;
	};
    
    function mouseUpOutCommon()
    {
        this.grabbed = false;
        this.mouX = null;
        this.mouY = null;
    }
    
    function mouseoutDragHandler(e)
	{
		var ev=e || window.event;
		var rtg=(e.relatedTarget) ? e.relatedTarget : e.toElement;
		while(rtg && rtg.parentNode)
		{
			if (rtg==this) return;
			rtg=rtg.parentNode;
		}
        this.mouseUpOutCommon();
	}
    
    function mouseupDragHandler(e)
	{
        this.mouseUpOutCommon();
        bringElementToFront(this);
	};
    
    function mousemoveDragHandler(e)
	{
		var ev=e || window.event;
		ev.cancelBubble=true;
		if (ev.stopPropagation()) ev.stopPropagation();
		if (!this.grabbed) return;
		var mc=mouseCoords(e);
		if (this.mouX || this.mouY)
		{
			var dx=this.mouX-mc.x;
			var dy=this.mouY-mc.y;
			this.style.left=parseInt(this.style.left,10)-dx+"px";
			this.style.top=parseInt(this.style.top,10)-dy+"px";
		}
		this.mouX=mc.x;
		this.mouY=mc.y;
	};
    
    function scrollDragHandler(e)
	{
		if (this.grabbed) this.grabbed=false;
	};
    
    elm.mouseUpOutCommon = mouseUpOutCommon;

	elm.onmousedown=mousedownDragHandler;
    elm.addEventListener('touchstart', mousedownDragHandler, false);
    
	elm.onmouseout=mouseoutDragHandler;
    elm.addEventListener('touchleave', mouseoutDragHandler, false);
    
	elm.onmouseup=mouseupDragHandler;
    elm.addEventListener('touchend', mouseupDragHandler, false);
    
	elm.onmousemove=mousemoveDragHandler;
    elm.addEventListener('touchmove', mousemoveDragHandler, false);
    
	elm.addEventListener('scroll', scrollDragHandler, true);
}

function genFloatingBox(iHTML,id,xPos,yPos)
{
	var elm=document.createElement('div');
	elm.id=id;
	elm.style.position="absolute";
//	elm.style.padding="10px";
	elm.style.left=xPos+"px";
	elm.style.top=yPos+"px";
	elm.style.width="auto";
	elm.style.height="auto";
	elm.className='gdbasestyle';
//	elm.style.backgroundColor="#808080";
	elm.style.color="black";
	elm.style.zIndex="20";
	elm.close=function()
	{
		if (this.onClose) this.onClose();
		this.parentNode.removeChild(this);
	}
//	elm.innerHTML='<div style="position:fixed"><a href="javascript:void(_(\''+id+'\').close())"><?php echo $language["close"];?></a></div><div style="height:15px">&nbsp;</div>'+iHTML;
	elm.innerHTML=
			'<div style="position:fixed;"><a href="javascript:void(_(\''+id+'\').close())"><?php echo $language["close"];?></a></div>'+
			iHTML;
			/*'<table style="border-collapse: collapse; width:100%; height:100%">'+
				'<tr><td class="corner" style="background-image: url(img/tl.png)"></td><td class="topbottom" style="background-image: url(img/t.png)"></td><td class="corner" style="background-image: url(img/tr.png)"></td></tr>'+
				'<tr><td class="leftright" style="background-image: url(img/l.png)"></td>'+
				'<td style="background-color: #808080; border:none; width:auto; height:auto">'+
					iHTML+
				'</td>'+
				'<td class="leftright" style="background-image: url(img/r.png)"></td></tr>'+
				'<tr><td class="corner" style="background-image: url(img/bl.png)"></td><td class="topbottom" style="background-image: url(img/b.png)"></td><td class="corner" style="background-image: url(img/br.png)"></td></tr>'+
			'</table>';*/
	
//	'<div>HEADER</div><div style="width:auto; height:auto">'+iHTML+'</div><div>FOOTER</div>';
	return elm;
}


var prevCellInfo;

function makeLoadingSquare(id,value)
{
	var cnv=_(id);
	if (!cnv) return;
	cnv.width=cnv.width; // clears the contents
	var ctx=cnv.getContext("2d");
	
	var angle=value*Math.PI*2;
	var sangle=-Math.PI*0.5
	
	ctx.fillStyle="rgba(0,255,0,0.2)";
	ctx.beginPath();
	ctx.moveTo(25,25);
	ctx.arc(25,25,40,sangle,sangle+angle,false);
	ctx.closePath();
	ctx.fill();
//	alert('X');
}

function showTooltip(str)
{
	var divElm=_('js_tooltip');
	if (!divElm)
	{
		var divElm=document.createElement("div");
		divElm.setAttribute('id','js_tooltip');
		document.body.appendChild(divElm);
	}
	divElm.style.position='absolute';
	divElm.style.left=mouseX+10+'px';
	divElm.style.top=mouseY+10+'px';
	divElm.style.padding='3px';
	divElm.style.width='auto';
	divElm.style.height='auto';
	divElm.style.visibility='visible';
	divElm.style.zIndex='1000';
	divElm.style.backgroundColor='white';
	divElm.style.border='1px solid black';
	divElm.innerHTML=str;
	divElm.onmouseout=removeTooltip;
}

function removeTooltip()
{
	var divElm=_('js_tooltip');
	if (!divElm) return;
	divElm.style.visibility='hidden';
}

function updateCurrentCellInfo()
{
	if (cellInfo.element/* && prevCellInfo.fldX && prevCellInfo.fldY && prevCellInfo.showPosition*/)
	{
		showCellInfo(cellInfo.fldX,cellInfo.fldY,false);
	}
}

var vsSelectedRow=0;
var villageListOrdering='id';
var villageListReverseOrdering=1; // 1 or -1;
function showVillageSummary(orderBy)
{
	var orderedVillages=[];
	var sumRow={};
	var loadedVillageCount=0;
	for(var i in playerVillages)
	{
		var village=playerVillages[i];
		village._vsLastUpdate=(parseInt(playerInfo.nowstamp,10)-parseInt(village.updateTimestamp,10));
		orderedVillages.push(playerVillages[i]);
		if (village.lastUpdate)
		{
			loadedVillageCount++;
			for(var j in UnitDescriptors)
			{
				var countDbName=UnitDescriptors[j].countDbName;
				var trainingDbName=UnitDescriptors[j].trainingDbName;
				if (!sumRow[countDbName]) sumRow[countDbName]=0;
				sumRow[countDbName]+=Math.floor(parseFloat(village[countDbName]));
				if (!sumRow[trainingDbName]) sumRow[trainingDbName]=0;
				sumRow[trainingDbName]+=Math.ceil(parseFloat(village[trainingDbName]));
			}
			for(var j in BuildingDescriptors)
			{
				var buildingLevelDbName=BuildingDescriptors[j].buildingLevelDbName;
				if (!sumRow[buildingLevelDbName]) sumRow[buildingLevelDbName]=0;
				sumRow[buildingLevelDbName]+=parseInt(village[buildingLevelDbName],10);
			}
		}
	}
	for(var j in BuildingDescriptors)
	{
		var buildingLevelDbName=BuildingDescriptors[j].buildingLevelDbName;
		if (!sumRow[buildingLevelDbName]) sumRow[buildingLevelDbName]=0;
		sumRow[buildingLevelDbName]/=loadedVillageCount;
		sumRow[buildingLevelDbName]=Math.round(sumRow[buildingLevelDbName]*100)/100;
	}
	if (!orderBy) orderBy=villageListOrdering;
	{
		orderedVillages.sort
		(
			function(a,b)
			{
				var x=a[orderBy];
				var y=b[orderBy];
				if (x==undefined) return 1;
				if (y==undefined) return -1;
				if ((!isNaN(parseFloat(x)) && isFinite(y)) && (!isNaN(parseFloat(x)) && isFinite(y)))
				{
					x=parseFloat(x);
					y=parseFloat(y);
				}

				if (x<y) return -1*villageListReverseOrdering;
				if (x==y) return 0;
				if (x>y) return 1*villageListReverseOrdering;
			}
		);
	}
	
	
	var rId=generateRandomId();
	var iHTML='';
	var orderPngName,orderPngTitle;
	if (villageListReverseOrdering==1)
	{
		orderPngName='img/descending.png';
		orderPngTitle='<?php echo $language["descendingordering"];?>';
	}
	else
	{
		orderPngName='img/ascending.png';
		orderPngTitle='<?php echo $language["ascendingordering"];?>';
	}
	iHTML+='<h1><?php echo $language["villagesummary"]; ?></h1>'+
	'<div class="center"><a href="javascript:void((function(){_(\''+rId+'\').close(); showVillageSummary();})())"><img src="img/refresh.png" alt="<?php echo $language["refresh"];?>" onmouseover="showTooltip(\'<?php echo $language["refresh"]; ?>\')" onmouseout="removeTooltip()" style="width:25px; height:25px"></a> '+
	'<a href="javascript:void((function(){_(\''+rId+'\').close(); villageListReverseOrdering=villageListReverseOrdering*-1; showVillageSummary();})())">'+
	'<img class="image16" src="'+orderPngName+'" alt="'+orderPngTitle+'" onmouseover="showTooltip(\''+orderPngTitle+'\')" onmouseout="removeTooltip()">'+
	'</a></div>';
	iHTML+='<table>';
	iHTML+='<tr>';
	iHTML+='<th><a href="javascript:void((function(){_(\''+rId+'\').close(); villageListOrdering=\'id\'; showVillageSummary();})())">#</a></th>'+
	'<th><a href="javascript:void((function(){_(\''+rId+'\').close(); villageListOrdering=\'x\'; showVillageSummary();})())"><?php echo $language["coordinates"]; ?></a></th>'+
	'<th><a href="javascript:void((function(){_(\''+rId+'\').close(); villageListOrdering=\'villageName\'; showVillageSummary();})())"><?php echo $language["name"]; ?></a></th>'+
	'<th><a href="javascript:void((function(){_(\''+rId+'\').close(); villageListOrdering=\'buildPoints\'; showVillageSummary();})())"><?php echo $language["buildpoints"]; ?></a></th>'+
	'<th><a href="javascript:void((function(){_(\''+rId+'\').close(); villageListOrdering=\'score\'; showVillageSummary();})())"><?php echo $language["score"]; ?></a></th>';
	var uCount=0;
	for(var i in UnitDescriptors)
	{
		var uDesc=UnitDescriptors[i];
		iHTML+='<th><a href="javascript:void((function(){_(\''+rId+'\').close(); villageListOrdering=\''+UnitDescriptors[i].countDbName+'\'; showVillageSummary();})())"><img src="'+uDesc['image']+'" alt="'+UnitNames[i]+'" onmouseover="showTooltip(\''+UnitNames[i]+'\')" onmouseout="removeTooltip()"  class="image16"></a></th>';
		uCount++;
	}
	var bCount=0;
	for(var i in BuildingDescriptors)
	{
		var bDesc=BuildingDescriptors[i];
		iHTML+='<th><a href="javascript:void((function(){_(\''+rId+'\').close(); villageListOrdering=\''+BuildingDescriptors[i].buildingLevelDbName+'\'; showVillageSummary();})())"><img src="'+bDesc['image']+'" alt="'+BuildingNames[i]+'" onmouseover="showTooltip(\''+BuildingNames[i]+'\')" onmouseout="removeTooltip()" class="image16"></a></th>';
		bCount++;
	}
	iHTML+='<th><a href="javascript:void((function(){_(\''+rId+'\').close(); villageListOrdering=\'_vsLastUpdate\'; showVillageSummary();})())"><?php echo $language["lastupdate"]; ?></a></th>';
	var infoColumnCount=4+uCount+bCount; // coord,bp,name + unit stuff + building stuff
	iHTML+='</tr>';
	for(var i in orderedVillages)
	{
		var village=orderedVillages[i];
		iHTML+='<tr id="vs'+village.id+'" '+(village.id==vsSelectedRow ? 'style="background-color: #0080FF"':'')+' onclick="if (_(\'vs\'+vsSelectedRow)) _(\'vs\'+vsSelectedRow).style.backgroundColor=\'\';  this.style.backgroundColor=\'#0080FF\'; vsSelectedRow='+village.id+';">';
		iHTML+='<td>'+village.id+'</td>';
		iHTML+=
			'<td><a href="javascript:void(initMap('+village.x+','+village.y+'))"><?php echo xprintf($language["coordinate"],array("'+village.x+'","'+village.y+'")); ?></a></td>';
		if (!village.lastUpdate)
		{
			iHTML+='<td colspan="'+infoColumnCount+'"><?php echo $language["notloaded"]; ?></td>';
		}
		else
		{
			iHTML+='<td><a href="javascript:void(showCellInfo('+village.x+','+village.y+',{\'x\': mouseX,\'y\':mouseY}))">'+(village.villageName==undefined ? '':village.villageName) +'</a></td>'+
				'<td>'+(village.buildPoints==undefined ? '':Math.floor(village.buildPoints*100)/100) +'</td><td>'+village.score+'</td>';
			for(var j in UnitDescriptors)
			{
				var uDesc=UnitDescriptors[j];
				var unitCount=village[uDesc.countDbName];
				var trainingCount=village[uDesc.trainingDbName];
				if (unitCount==undefined) unitCount='';
				else unitCount=Math.floor(unitCount);
				if (trainingCount==undefined) trainingCount='';
				else trainingCount=Math.ceil(trainingCount);
				iHTML+='<td>'+unitCount+' ('+trainingCount+')</td>';
			}
			for(var j in BuildingDescriptors)
			{
				var bDesc=BuildingDescriptors[j];
				var bLevel=village[bDesc.buildingLevelDbName];
				if (bLevel==undefined) bLevel='';
				iHTML+='<td>'+bLevel+'</td>';
			}
			iHTML+='<td><?php echo xprintf($language["lastupdatesecondstext"],array("'+village._vsLastUpdate+'"));?></td>';
		}
		iHTML+='</tr>';
	}	
	{
		var village=sumRow;
		iHTML+='<tr>';
		iHTML+='<td>'+loadedVillageCount+'</td>';
		iHTML+='<td colspan="4">&nbsp;</td>';
		for(var j in UnitDescriptors)
		{
			var uDesc=UnitDescriptors[j];
			var unitCount=village[uDesc.countDbName];
			var trainingCount=village[uDesc.trainingDbName];
			if (unitCount==undefined) unitCount='';
			if (trainingCount==undefined) trainingCount='';
			iHTML+='<td>'+unitCount+' ('+trainingCount+')</td>';
		}
		for(var j in BuildingDescriptors)
		{
			var bDesc=BuildingDescriptors[j];
			var bLevel=village[bDesc.buildingLevelDbName];
			if (bLevel==undefined) bLevel='';
			iHTML+='<td>'+bLevel+'</td>';
		}
		iHTML+='<td>&nbsp;</td>';
		iHTML+='</tr>';
	}	
	iHTML+='</table>';
	var elm=genFloatingBox(iHTML,rId,0,0);
//	elm.style.width="800px";
	document.body.appendChild(elm);
	makeDraggable(elm);
	bringElementToFront(elm);
	constrainElementInside(elm,20);
	centerElement(elm);
}


function showBuildingCosts(building)
{
	var descriptor=BuildingDescriptors[building];
	if (!descriptor) return;
	var costFn=eval('('+descriptor.jsCostFunction+')');
	var costLines='';
	for(var i=0;i<100;i++)
	{
		costLines+='<tr><td>'+(i+1)+'</td><td><?php echo xprintf($language["coststring"],array("'+Math.ceil(costFn(i))+'"))?></td></tr>';
	}
	var costTable=
	'<table>'+
		'<tr><th><?php echo $language["level"]; ?></th><th><?php echo $language["cost"]; ?></th></tr>'+
		costLines+
	'</table>'+
	''
	;
	var elm=genFloatingBox(costTable,generateRandomId(),mouseX,mouseY);
	makeDraggable(elm);
	bringElementToFront(elm);
	document.body.appendChild(elm);
	constrainElementInside(elm);
	
}

function buttonizeElement(id)
{
	var div=_(id);
	div.onmousedown=function(e)
	{
		div.style.borderLeft='2px solid black';
		div.style.borderTop='2px solid black';
		div.style.borderRight='2px solid white';
		div.style.borderBottom='2px solid white';
	};
	div.onmouseout=function(e)
	{
		var ev=e || window.event;
		var rtg=ev.relatedTarget ? ev.relatedTarget : ev.toElement;
		while(rtg.parentNode)
		{
			if (rtg==this) return;
			rtg=rtg.parentNode;
		}
		return this.onmouseup(e);
	}
	div.onmouseup=function(e)
	{
		div.style.borderLeft='2px solid white';
		div.style.borderTop='2px solid white';
		div.style.borderRight='2px solid black';
		div.style.borderBottom='2px solid black';
	};
	div.style.borderLeft='2px solid white';
	div.style.borderTop='2px solid white';
	div.style.borderRight='2px solid black';
	div.style.borderBottom='2px solid black';
}

function setSpareBuildPoints(spanId,villageId)
{
	var span=_(spanId);
	var village=villagesById[villageId];
	span.innerHTML='';
	var sbp=village.spareBuildPoints;
	var input=document.createElement('input');
	input.setAttribute('type','text');
	input.value=sbp;
	input.style.width="4em";
	input.onkeydown=function(ev)
	{
		var ev=ev || window.event;
		if ((ev.keyCode==13) || (ev.keyCode==27))
		{
			if ((ev.keyCode==13) && (!isNaN(parseInt(this.value,10))))
			{
				var village=villagesById[villageId];
				var oldSbp=village.spareBuildPoints;
				var sbp=parseInt(this.value,10);
				village.spareBuildPoints=sbp
				var task=
				{
					'villages':[[village.x,village.y]],
					'command':['SETSPAREBP',villageId,sbp],
					'text':'<?php echo xprintf($language["setsparebptask"],array("'+village.villageName+'","'+sbp+'"))?>',
					'undo':function()
					{
						village.spareBuildPoints=oldSbp;
						updateCurrentCellInfo();
					}
				};
				tasklist.push(task);
			}
			updateCurrentCellInfo();
		}
	};
	span.appendChild(input);
	input.focus();
}

function reduceBigNumber(number,minReduce)
{
	if (number>minReduce)
	{
		if (number<1000) return number;
		if (number<1e6) return Math.round(number/100.0)/10.0+"K";
		if (number<1e9) return Math.round(number/1e5)/10.0+"M";
		if (number<1e12) return Math.round(number/1e8)/10.0+"G";
		if (number<1e15) return Math.round(number/1e11)/10.0+"T";
		if (number<1e18) return Math.round(number/1e14)/10.0+"P";
		if (number<1e21) return Math.round(number/1e17)/10.0+"E";
		else return Math.round(number/1e20)/10.0+"Z";
	}
	return number;
}

var cellInfo=new Object();
var selectedPlayer=0;
var selectedGuild=0;
function showCellInfo(x,y,refreshIfUpdateNeeded)
{
	if (refreshIfUpdateNeeded===null) refreshIfUpdateNeeded=true;
	if (!cellInfo.showPosition && mouseX && mouseY) cellInfo.showPosition={'x':mouseX,'y':mouseY};
	if (cellInfo.element) 
	{
		cellInfo.element.close();
	}
	if (!mapCache[x] || !mapCache[x][y] || !mapCache[x][y].villageInfo) return;
	var e=mapCache[x][y].villageInfo;
	if (e.placeholder)
	{
		updateVillage(e.id,e.x,e.y);
		return;		
	}
	if (e && e.userId)
	{
		selectedPlayer=e.userId;
		selectedGuild=e.guildId;
	}
	var detailedInfo="";
	var rId=generateRandomId();
	var bpId=generateRandomId();
	var ownedVillage=false;
	
	var canvasIds=[];
	if (e.lastUpdate && !guestMode) // if we know the last update time then, we than it's an own village.
	{
		// check whether it needs update
		if ((playerInfo.nowstamp[0]-e.updateTimestamp[0]>AUTOVILLAGEUPDATEINTERVAL) && (refreshIfUpdateNeeded))
		{
			updateVillage(e.id,e.x,e.y);
		}
		//
		
		ownedVillage=true;
		var unitTrainingTable='<table class="villagestats"><tr>';
		var unitTrainingSpanIdsByUnitName={};
		for(var i in UnitDescriptors)
		{
			var descriptor=UnitDescriptors[i];
			var langEntry=descriptor['languageEntry'];
			var countDbName=descriptor['countDbName'];
			var trainingDbName=descriptor['trainingDbName'];
			var unitLangName=UnitNames[i];
			var sId=generateRandomId();
			var cid=generateRandomId();
			canvasIds[i]=cid;
			unitTrainingTable+=
			'<td>'+
				'<div style="width:50px; height:50px; background-image:url('+descriptor.image+'); position:relative; left:0; top:0">'+
					'<canvas id="'+cid+'" width="50" height="50" style="position:absolute; left:0; top:0; width:50px; height:50px; z-index:1"></canvas>'+
					'<span style="position:absolute; left:0; top:0; z-index: 3; color:white; font-weight:bold">'+reduceBigNumber(Math.floor(e[countDbName]),1000000)+'</span>'+
					'<span style="position:absolute; left:1px; top:1px; z-index: 2; color:black; font-weight:bold">'+reduceBigNumber(Math.floor(e[countDbName]),1000000)+'</span>'+
					'<span style="position:absolute; right:0; bottom:0; z-index: 2; color:black;">('+Math.ceil(e[trainingDbName])+')</span>'+
					'<span style="position:absolute; right:1px; bottom:1px; z-index: 2; color:white;">('+Math.ceil(e[trainingDbName])+')</span>'+
					'<div style="width:100%; height:100%; position:absolute; left:0; top:0; z-index:10" '+
						'onmouseover="showTooltip(\'<?php echo xprintf($language["unitinfotooltip"],array("'+unitLangName+'","'+Math.floor(e[countDbName])+'","'+Math.ceil(e[trainingDbName])+'","'+(Math.floor(100*(Math.ceil(e[trainingDbName])-e[trainingDbName])))+'","'+descriptor.cost+'"));?>\')"'+
						'onmouseout="removeTooltip()">'+
					'</div>'+
				'</div><br>'+
				' <span id="'+sId+'"></span><br>'+
//				' <a href="javascript:showUnitTrainingInput('+e.id+',\''+i+'\',\''+sId+'\')"><?php echo $language["train"]; ?></a>'+
			'</td>';
			unitTrainingSpanIdsByUnitName[i]=sId;
		}
		unitTrainingTable+='</tr></table>';
		var buildingTable='<table class="villagestats"><tr>';
		var buildingDivs={};
		for(var key in BuildingDescriptors)
		{
			var value=BuildingDescriptors[key];
			var langEntry=value['languageEntry'];
			var levelName=value['buildingLevelDbName'];
			var buildingName=BuildingNames[key];
			var sId=generateRandomId();
			buildingTable+=
					'<td>'+
						'<div id="'+sId+'" style="width: 50px; height:50px; background-image:url('+value["image"]+'); position:relative; left:0; top:0" title="'+buildingName+'" onclick="upgradeBuilding('+e.id+',\''+key+'\')">'+
							'<span style="position:absolute; left:1px; top:1px; z-index: 1; color:black; font-weight:bold">'+Math.floor(e[levelName])+'</span>'+
							'<span style="position:absolute; left:0; top:0; z-index: 2; color:white; font-weight:bold">'+Math.floor(e[levelName])+'</span>'+
							'<div style="width:100%; height:100%; position:absolute; left:0; top:0; z-index:3" '+
								'onmouseover="showTooltip(\'<?php echo xprintf($language["buildinginfotooltip"],array("'+buildingName+'","'+e[levelName]+'","'+Math.ceil((eval('('+value['jsCostFunction']+')'))(e[levelName]))+'"));?>\')"'+
								'onmouseout="removeTooltip()">'+
							'</div>'+
						'</div>'+
						'<p><a href="javascript:void(showBuildingCosts(\''+key+'\'))" onmouseover="showTooltip(\'<?php echo $language["showcost"]; ?>\')" onmouseout="removeTooltip()"><?php echo xprintf($language["coststring"],array("'+Math.ceil((eval('('+value['jsCostFunction']+')'))(e[levelName]))+'"))?></a></p>'+
//						'<a href="javascript:void(upgradeBuilding('+e.id+',\''+key+'\'))"><?php echo $language["upgrade"]; ?></a>'+
					'</td>';
			buildingDivs[key]=sId;
		}
		buildingTable+='</tr></table>';
		
		var sbpId=generateRandomId();
		detailedInfo+=
		'<table class="villagestats">'+
		'<tr>'+
			'<td><?php echo $language["buildpoints"]; ?></td>'+
			'<td><span id="'+bpId+'">'+Math.floor(e.buildPoints)+'</span>(<?php echo $language["next"]; ?>: '+(Math.floor(100*(e.buildPoints-Math.floor(e.buildPoints))))+'%)</td>'+
			'<td><dfn onmouseover="showTooltip(\'<?php echo $language["sparebptooltip"]?>\')"><?php echo $language["sparebp"];?></dfn><span id="'+sbpId+'"> <a href="javascript:setSpareBuildPoints(\''+sbpId+'\','+e.id+')">'+e.spareBuildPoints+'</a></span></td></tr>'+
		'</table>'+
		buildingTable+
		unitTrainingTable+
		'<table class="villagestats">'+
		'<tr><td><?php echo $language["lastupdate"]; ?></td><td>'+e.lastUpdate+'</td><td><a href="javascript:(function(){_(\''+rId+'\').close(); updateVillage('+e.id+','+e.x+','+e.y+'); })()"><img src="img/refresh.png" class="image16" onmouseover="showTooltip(\'<?php echo $language["updatenow"]; ?>\')" onmouseout="removeTooltip()"></img></a></td></tr>'+
		'</table>'+
		'<div style="float:left"><small><a href="javascript:void(openInWindow(\'abandonvillage.php?id='+e.id+'\'))"><?php echo $language["abandonvillage"]; ?></a></small></div>'+
		''
		;
	}
	var heroText='';
	if ((playerInfo) && (playerInfo.hero) && (playerInfo.hero[0].inVillage) && (e.id==playerInfo.hero[0].inVillage[0]))
	{
		heroText='<tr><td colspan="3"><a href="javascript:void(openInWindow(\'viewhero.php?id='+playerInfo.hero[0].id[0]+'\'))"><?php echo $language["yourheroisinthisvillage"];?></a></td></tr>';
	}
	if ((playerInfo) && (playerInfo.hero) && (!playerInfo.hero[0].name))
	{
		heroText='<tr><td colspan="3"><a href="javascript:void(openInWindow(\'docreatenewhero.php?at='+e.id[0]+'&rnd='+Math.random()+'\'))"><?php echo $language["createnewhero"];?></a></td></tr>';		
	}

	var renText='';
	if (ownedVillage)
	{
		renText='<p class="center"><a href="javascript:renameVillage('+e.id+','+e.x+','+e.y+')"><?php echo $language["rename"]?></a></p>';
	}
	var iHTML=
//	'<p><a href="javascript:void(0)" onclick="_(\''+rId+'\').close()"><?php echo $language["close"]; ?></a></p>'+
	'<h2 class="center"><span id="villagenamespan">'+e.villageName+'</span></h3>'+
	renText+
	'<p class="center"><?php echo xprintf($language["miscvillageinfotext"],array("'+e.score+'","'+e.x+'","'+e.y+'","'+e.id+'")); ?></p>'+
	'<table class="villagestats">'+
	(
		!e.lastUpdate ? 
		'<tr><td><?php echo $language["ownername"]; ?></td><td colspan="2"><a href="javascript:openInWindow(\'viewplayer.php?id='+e.userId+'\')">'+e.userName+'</a></td></tr>'+
		'<tr><td><?php echo $language["guildname"]; ?></td><td colspan="2"><a href="javascript:openInWindow(\'viewguild.php?id='+e.guildId+'\')">'+e.guildName+'</a></td></tr>'+
		'<tr><td colspan="3"><span style="color:'+Colors[e.diplomaticStance]+'">'+DiplomacyName[e.diplomaticStance]+'</span></td></tr>':''
	)
	+
	heroText+
	'</table>'+
	detailedInfo+
	""
	;
	var elm=genFloatingBox(iHTML,rId,cellInfo.showPosition.x,cellInfo.showPosition.y);
	document.body.appendChild(elm);
	constrainElementInside(elm,20);
	makeDraggable(elm);
	bringElementToFront(elm);
	elm.onClose=function()
	{
		cellInfo.showPosition={'x':parseInt(cellInfo.element.style.left,10),'y':parseInt(cellInfo.element.style.top,10)};
		cellInfo.element=null;
	};
	cellInfo.element=elm;
	cellInfo.fldX=x;
	cellInfo.fldY=y;
	for(var key in UnitDescriptors)
	{
		var value=UnitDescriptors[key];
		if (canvasIds[key])
		{
			trainingDbName=value.trainingDbName;
			makeLoadingSquare(canvasIds[key],(Math.ceil(e[trainingDbName])-e[trainingDbName]));				
		}
	}
	// create unit Traning inputs:
	for(var i in unitTrainingSpanIdsByUnitName)
	{
		showUnitTrainingInput(e.id,i,unitTrainingSpanIdsByUnitName[i]);
	}
	// buttonize building icons
	for(var i in buildingDivs)
	{
		buttonizeElement(buildingDivs[i]);
	}

}


function objectFromXMLNode(node)
{
	if (node.childNodes[0]) 
	{
		if (node.childNodes[0].nodeType==3)return node.childNodes[0].nodeValue; //if it's a text node
	}
	else
		return '';
	var retVal=new Object();
	for (var i=0;i<node.childNodes.length;i++)
	{
		var nodeName=node.childNodes[i].nodeName;
		var nodeValue=objectFromXMLNode(node.childNodes[i]);
		if (!retVal[nodeName]) retVal[nodeName]=[];
		retVal[nodeName].push(nodeValue);
	}
	return retVal;
}

function getVillageImage(score)
{
	var maxImage=null;
	var maxScore=-1;
	for(var i=0;i<villageLevels.length;i++)
	{
		var vi=villageLevels[i];
		if ((vi.minScore<=score) && (score>maxScore))
		{
			maxImage=vi.image;
		}
	}
	return maxImage;
}

function processUpdateXML(xml)
{
	for(var i=0;i<xml.documentElement.childNodes.length;i++)
	{
		var o=objectFromXMLNode(xml.documentElement.childNodes[i]);
		if (!mapCache[o.x]) mapCache[o.x]=new Array();
		if (!mapCache[o.x][o.y]) mapCache[o.x][o.y]=new Object();
		mapCache[o.x][o.y].villageInfo=o;
//		mapCache[o.x][o.y].diplomacyColor=Colors[getDiplomaticStance(mapCache[o.x][o.y])];
		getDiplomaticStance(mapCache[o.x][o.y]);
		mapCache[o.x][o.y].image=getVillageImage(o.score);
		villagesById[o.id[0]]=o;
		if (o.lastUpdate)
		{
			playerVillages[o.id[0]]=o;
		}
	}
}

function updateCallback()
{
	if (xmlHttp.readyState==4)
	{
		if (xmlHttp.responseXML)
		{
			processUpdateXML(xmlHttp.responseXML);
			renderMap();
			updateCurrentCellInfo();
		}
		else
			alert(xmlHttp.responseText);
	}
}

function calculateRbRectangles(region)
{
	if (region.rbCalculated) return;
	// find left, top and left-top adjacent region if they exist.
	var leftRegion=updateRegions[(region.left-UPDATEREGIONSIZE)+';'+region.top];
	var upRegion=updateRegions[region.left+';'+(region.top-UPDATEREGIONSIZE)];
	var luRegion=updateRegions[(region.left-UPDATEREGIONSIZE)+';'+(region.top-UPDATEREGIONSIZE)];
	// make sure their Rectangles are calculated;
	if (leftRegion && !leftRegion.rbCalculated) calculateRbRectangles(leftRegion);
	if (upRegion && !upRegion.rbCalculated) calculateRbRectangles(upRegion);
	if (luRegion && !luRegion.rbCalculated) calculateRbRectangles(luRegion);
	// get their rectangle definitions if they exist;
	var leftDefs=leftRegion ? leftRegion.rbDefinitions : [];
	var upDefs=upRegion ? upRegion.rbDefinitions : [];
	var luDefs=luRegion ? luRegion.rbDefinitions : [];
	// select the maximum heights and widths from the 3 advacent regions
	var leftMax=[0,0];
	var upMax=[0,0];
	var luMax=[0,0];
	for(var i=0;i<leftDefs.length;i++) // TODO összevonni.
	{
		if (leftDefs[i][0]>leftMax[0]) leftMax[0]=leftDefs[i][0];
		if (leftDefs[i][1]>leftMax[1]) leftMax[1]=leftDefs[i][1];
	}
	for(var i=0;i<upDefs.length;i++)
	{
		if (upDefs[i][0]>upMax[0]) upMax[0]=upDefs[i][0];
		if (upDefs[i][1]>upMax[1]) upMax[1]=upDefs[i][1];
	}
	for(var i=0;i<luDefs.length;i++)
	{
		if (luDefs[i][0]>luMax[0]) luMax[0]=luDefs[i][0];
		if (luDefs[i][1]>luMax[1]) luMax[1]=luDefs[i][1];
	}
	// Begin calculation
	// Extend left region if possible
	var possibleSizes=[];
	for(var i=0;i<leftDefs.length;i++)
	{
		var def=leftDefs[i];
		var newDef=clone(def);
		// it's sure it's horizontal dimension will be higher with one.
		newDef[0]+=1;
		// how about it's vertical? It's determined by the other 2's vertical info
		var less=(upMax[1]<luMax[1] ? upMax[1]:luMax[1])+1;
		if (less<newDef[1]) newDef[1]=less;
		// add this to the possible sizes
		var addIt=true;
		for(var j=0;j<possibleSizes.length;j++)
		{
			var size=possibleSizes[j];
			if ((size[0]>=newDef[0]) && (size[1]>=newDef[1]))
			{
				addIt=false;
			}
		}
		if (addIt)
		{
			possibleSizes.push(clone(newDef));
//			alert('In left extend we added: ['+newDef+']');
		}
	}
	// Extend up region if possible
	for(var i=0;i<upDefs.length;i++)
	{
		var def=upDefs[i];
		var newDef=clone(def);
		// it's sure it's vertical dimension will be higher with one.
		newDef[1]+=1;
		// how about it's horizontal? It's determined by the other 2's vertical info
		var less=(leftMax[0]<luMax[0] ? leftMax[0]:luMax[0])+1;
		if (less<newDef[0]) newDef[0]=less;
		// add this to the possible sizes
		var addIt=true;
		for(var j=0;j<possibleSizes.length;j++)
		{
			var size=possibleSizes[j];
			if ((size[0]>=newDef[0]) && (size[1]>=newDef[1]))
			{
				addIt=false;
			}
		}
		if (addIt)
		{
			possibleSizes.push(clone(newDef));
//			alert('In up extend we added: ['+newDef+']');
		}
	}
	if (possibleSizes.length==0) possibleSizes.push([1,1]);
	// Now we need to remove the implied cases. (x,y) implies all (<x,<y) cases
/*	var xCase={};
	for(var i=0;i<possibleSizes.length;i++)
	{
		var size=possibleSizes[i];
		if ((xCase[size[0]]==undefined) || (xCase[size[0]]<size[1])) xCase[size[0]]=size[1];
	}
	possibleSizes=[];
	for(var i in xCase)
	{
		possibleSizes.push([parseInt(i),xCase[i]]);
	}
	var yCase={};
	for(var i=0;i<possibleSizes.length;i++)
	{
		var size=possibleSizes[i];
		if ((yCase[size[1]]==undefined) || (yCase[size[1]]<size[0])) yCase[size[1]]=size[0];
	}
	possibleSizes=[];
	for(var i in yCase)
	{
		possibleSizes.push([yCase[i],parseInt(i)]);
	}*/
	
	region.rbDefinitions=clone(possibleSizes);
	
	region.rbCalculated=true;
}

function mergeRegions()
{
	for(var i in updateRegions)
	{
		var region=updateRegions[i];
		delete region.rbCalculated;
	}
	
	for(var i in updateRegions)
	{
		var region=updateRegions[i];
		calculateRbRectangles(region);
	}
}

function updateMapRegion()
{
/*	if (needUpdate)
	{*/
	// merge existing regions
	if (!dontMergeTiles)
		mergeRegions();
	// choose the biggest rectangle from them
	var regionKey=null;
	var maxArea=0;
	var maxRegionKey=null;
	var maxRbSize=[0,0];
	for(regionKey in updateRegions)
	{
		var region=updateRegions[regionKey];
		if (dontMergeTiles)
		{
			maxRegionKey=regionKey;
			maxRbSize=[1,1];
			break;
		}
		else if (region.rbCalculated)
		{
			for(var i in region.rbDefinitions)
			{
				var rbRectSize=region.rbDefinitions[i];
				var area=rbRectSize[0]*rbRectSize[1];
				if (area>maxArea)
				{
					maxArea=area;
					maxRegionKey=regionKey;
					maxRbSize=rbRectSize;
				}
			}
		}
	}
	if (!maxRegionKey) return true;
	var region=updateRegions[maxRegionKey];
	updateLeft=region.left-(maxRbSize[0]-1)*UPDATEREGIONSIZE;
	updateTop=region.top-(maxRbSize[1]-1)*UPDATEREGIONSIZE;
	updateRight=region.left+UPDATEREGIONSIZE;
	updateBottom=region.top+UPDATEREGIONSIZE;
	
/*	updateLeft=Math.floor(updateLeft/UPDATEREGIONSIZE)*UPDATEREGIONSIZE;
	updateRight=Math.ceil(updateRight/UPDATEREGIONSIZE)*UPDATEREGIONSIZE;
	updateTop=Math.floor(updateTop/UPDATEREGIONSIZE)*UPDATEREGIONSIZE;
	updateBottom=Math.ceil(updateBottom/UPDATEREGIONSIZE)*UPDATEREGIONSIZE;*/
	
	if (ajaxPost("areainfo.php?left="+encodeURIComponent(updateLeft)+"&right="+encodeURIComponent(updateRight)+"&top="+encodeURIComponent(updateTop)+"&bottom="+encodeURIComponent(updateBottom)+(slowNet ? "&placeholdersonly":"")," ",updateCallback))
	{
		for(var i=updateLeft;i<=updateRight;i++)
		{
			for(var j=updateTop;j<=updateBottom;j++)
			{
				if (!mapCache[i]) mapCache[i]=new Array();
				mapCache[i][j]={image:mapImages.grass};
			}
		}
		for(var i=0;i<maxRbSize[0];i++)
		{
			for(var j=0;j<maxRbSize[1];j++)
			{
				delete updateRegions[(region.left-i*UPDATEREGIONSIZE)+';'+(region.top-j*UPDATEREGIONSIZE)];
			}
		}
//		delete updateRegions[regionKey];
	}
	else
		return false;
		
//	}
	return true;
}

function updateData()
{
	if (!mapElm) return;
	elmWidth=mapElm.offsetWidth;
	elmHeight=mapElm.offsetHeight;
	leftPos=centerX-elmWidth*0.5;
	topPos=centerY-elmHeight*0.5;
}

function addUpdatable(x,y)
{
	if ((!mapCache[x]) || (!mapCache[x][y])) return; // only update cells we actually see.
	var left=parseInt(Math.floor(x/UPDATEREGIONSIZE)*UPDATEREGIONSIZE,10);
	var top=parseInt(Math.floor(y/UPDATEREGIONSIZE)*UPDATEREGIONSIZE,10);
	mergeExperimentRegions[left+';'+top]=updateRegions[left+';'+top]={'left':left,'top':top,'width':UPDATEREGIONSIZE,'height':UPDATEREGIONSIZE};
	
	if (!needUpdate)
	{
		updateLeft=updateRight=x;
		updateTop=updateBottom=y;
		needUpdate=true;
	}
	else
	{
		var uLeft=updateLeft;
		var uRight=updateRight;
		var uTop=updateTop;
		var uBottom=updateBottom;
		if (x<uLeft) uLeft=x;
		if (x>uRight) uRight=x;
		if (y<uTop) uTop=y;
		if (y>uBottom) uBottom=y;
		if ((Math.abs(uLeft-uRight)>MAXUPDATEREGIONSIZE) || (Math.abs(uTop-uBottom)>MAXUPDATEREGIONSIZE)) return;
		updateLeft=uLeft;
		updateRight=uRight;
		updateTop=uTop;
		updateBottom=uBottom;
	}
}

function getDiplomaticStance(cell)
{
	if (!cell) return "";
	if (!cell.villageInfo) return "";
	var vInfo=cell.villageInfo;
	if (guestMode)
	{
		vInfo.diplomaticStance='neutral';
		return 'neutral';
	}
	if (!playerInfo) return "";
	if (vInfo.diplomaticStance) return vInfo.diplomaticStance;
	var gId=vInfo.guildId[0];
	var owner=vInfo.userId[0];
	if (owner==playerInfo.id[0]) 
	{
		vInfo.diplomaticStance='own';
		return 'own';
	}
	if ((gId==playerInfo.guildId[0]) && (gId!=''))
	{
		vInfo.diplomaticStance='guild';
		return 'guild';
	}
	var dArrays=[playerInfo.diplomacy[0].allies,playerInfo.diplomacy[0].peace,playerInfo.diplomacy[0].enemies];
	var cArray=['ally','peace','enemy'];
	for(var j in dArrays)
	{
		var dArray=dArrays[j];
		if (dArray)
		{
			var array=dArray[0].guildId;
			for(var i in array)
			{
				if (array[i]==gId) 
				{
					vInfo.diplomaticStance=cArray[j];
					return cArray[j];
				}
			}
		}
	}
	vInfo.diplomaticStance='neutral';
	return 'neutral';
}

function createMapCell(x,y)
{
	if (!mapCache[x]) mapCache[x]=new Array();
	if (!mapCache[x][y]) mapCache[x][y]={image:mapImages.loading};
}

function isCellExist(x,y)
{
	return !!mapCache[x] && !!mapCache[x][y];
}

function oldRenderingMethod(highLightX,highLightY)
{
	if (!mapElm) return;
	var hdc=mapElm.getContext("2d");
	updateData();
	var rendercornerX=Math.floor(leftPos/cellSizeX)*cellSizeX-leftPos;
	var rendercornerY=Math.floor(topPos/cellSizeY)*cellSizeY-topPos;
//	alert('['+leftPos+';'+topPos+']');
	for(var i=0;i<matrixWidth;i++)
	{
		for(var j=0;j<matrixHeight;j++)
		{
			var realX=parseInt(Math.floor(leftPos/cellSizeX)+i,10);
			var realY=parseInt(Math.floor(topPos/cellSizeY)+j,10);
			if (!isCellExist(realX,realY))
			{
				createMapCell(realX,realY);
				addUpdatable(realX,realY);
			}
			var mcEntry=mapCache[realX][realY];
                        hdc.drawImage(mcEntry.image,i*cellSizeX+rendercornerX,j*cellSizeY+rendercornerY,cellSizeX,cellSizeY);
			if (selectedCells.isSelected(realX,realY))
			{
				hdc.strokeStyle=Colors[getDiplomaticStance(mapCache[realX][realY])];
				hdc.lineWidth="3";
				hdc.strokeRect(i*cellSizeX+rendercornerX,j*cellSizeY+rendercornerY,cellSizeX,cellSizeY);
			}
/*			if (guestMode)
			{
				if (mcEntry.villageInfo)
				{
					if (parseInt(mcEntry.villageInfo.userId)==parseInt(selectedPlayer))
					{
						hdc.fillStyle="#0000FF";
					}
					else if (parseInt(mcEntry.villageInfo.guildId)==parseInt(selectedGuild))
					{
						hdc.fillStyle="#0080FF";
					}
					else
					{
						hdc.fillStyle=Colors[mcEntry.villageInfo.diplomaticStance];
					}
					hdc.fillRect((i+0.75)*cellSize+rendercornerX,(j+0.75)*cellSize+rendercornerY,0.2*cellSize,0.2*cellSize);
				}
			}
			else*/ if (mcEntry.villageInfo && mcEntry.villageInfo.diplomaticStance)
			{
				var color;
				color=Colors[mcEntry.villageInfo.diplomaticStance];
				if (guestMode)
				{
					if (parseInt(mcEntry.villageInfo.userId)==parseInt(selectedPlayer))
					{
						color="#0000FF";
					}
					else if (parseInt(mcEntry.villageInfo.guildId)==parseInt(selectedGuild))
					{
						color="#0080FF";
					}
				}
				hdc.fillStyle=color;
				hdc.strokeStyle=color;
				hdc.lineWidth="2";
				if ((mcEntry.villageInfo.userId!='') && (mcEntry.villageInfo.userId!='0'))
				{
					if (parseInt(mcEntry.villageInfo.userId)==parseInt(selectedPlayer))
						hdc.fillRect((i+0.75)*cellSizeX+rendercornerX,(j+0.75)*cellSizeY+rendercornerY,0.2*cellSizeX,0.2*cellSizeY);
					else
						hdc.strokeRect((i+0.75)*cellSizeX+rendercornerX,(j+0.75)*cellSizeY+rendercornerY,0.2*cellSizeX,0.2*cellSizeY);	
				}
				
				if ((realX==highLightX) && (realY==highLightY))
				{
					hdc.strokeStyle='orange';
					hdc.strokeRect(i*cellSizeX+rendercornerX,j*cellSizeY+rendercornerY,cellSizeX,cellSizeY);
				}
				
			}
		}
	}
	selectionRect.draw(hdc);

}

function renderMap(highLightX,highLightY)
{
	<?php
		if (isset($_GET["axonmap"]))
		{
			?>
				newRenderingMethod(highLightX,highLightY);
			<?php
		}
		else
		{
			?>
				oldRenderingMethod(highLightX,highLightY);
			<?php
		}
	?>
}

function openActivityPlot()
{
	window.open('getactivityplot.php?width='+encodeURIComponent(_(widthId).value)+'&daybefore='+encodeURIComponent(_(daysAgoId).value));
}

function generateActivityPlotWindow()
{
	widthId=generateRandomId();
	daysAgoId=generateRandomId();
	var iHTML=
	'<h1><?php echo $language["generateactivityplot"];?></h1>'+
	'<div style="text-align:center">'+
	'<p><?php echo $language["plotwidth"];?><input id="'+widthId+'" type="text" value="1000"></p>'+
	'<p><?php echo $language["daysbefore"];?><input id="'+daysAgoId+'" type="text" value="0"></p>'+
	'<p><input type="button" value="<?php echo $language["generate"];?>" onclick="openActivityPlot()"></p>'+
	'</div>'+
	''
	;
	var elm=genFloatingBox(iHTML,generateRandomId(),0,0);
	document.body.appendChild(elm);
	constrainElementInside(elm,20);
	makeDraggable(elm);
	bringElementToFront(elm);
	centerElement(elm);
}

function showRecentWorldEventsType(type)
{
	var eventList='<table>';
	eventList+='<tr><th><?php echo $language["coordinates"]; ?></th><th><?php echo $language["player"]; ?></th><th><?php echo $language["guild"]; ?></th><th><?php echo $language["recipientplayer"]; ?></th><th><?php echo $language["eventtime"]; ?></th></tr>';
	var array=localWorldEventCache[type];
	array.sort(function(a,b){return b.timestamp-a.timestamp;});
	for(var i=0;i<array.length;i++)
	{
		var event=array[i];
		var village=null;
		if (mapCache[event.x] && mapCache[event.x][event.y] && mapCache[event.x][event.y].villageInfo) village=mapCache[event.x][event.y].villageInfo;
		if (!village) village=event.villageInfo;
		var pid='';
		var pname='';
		var gid='';
		var gname='';
		if (event.playerId!='')
		{
			pid=event.playerId;
			pname=event.playerName;
		}
		else if (event.guildId!='')
		{
			gid=event.guildId;
			gname=event.guildName;
		}
		else if (village)
		{
			pid=village.userId;
			pname=village.userName;
			gid=village.guildId;
			gname=village.guildName;
		}
		eventList+=
			'<tr>'+
			'<td>'+((event.playerId=='')  && (event.guildId=='') ? '<a href="javascript:void(initMap('+event.x+','+event.y+'))">'+(village ? village.villageName:'')+' ('+event.x+'|'+event.y+')</a>':'')+'</td>'+
			'<td>'+('<a href="javascript:void(openInWindow(\'viewplayer.php?id='+pid+'\'))">'+pname+'</a>')+'</td>'+
			'<td>'+('<a href="javascript:void(openInWindow(\'viewguild.php?id='+gid+'\'))">'+gname+'</a>')+'</td>'+
			'<td>'+(event.recipientId!='' ? '<a href="javascript:void(openInWindow(\'viewplayer.php?id='+event.recipientId+'\'))">'+event.recipientPlayer+'</a>':'<?php echo $language["public"];?>')+'</td>'+
			'<td>'+event.eventTime+'</td>'+
			'</tr>';
		
	}
	eventList+='</table>';
	var iHTML=
		'<h1><?php echo $language["recentevents"]; ?></h1>'+
		'<h2>'+WorldEventName[type]+'</h2>'+
		eventList
		;

	var elm=genFloatingBox(iHTML,generateRandomId(),0,0);
	document.body.appendChild(elm);
	makeDraggable(elm);
	centerElement(elm);
	constrainElementInside(elm);
	bringElementToFront(elm);
}

function showRecentWorldEvents()
{
	_('recentnotify').innerHTML='';
	var eventList='<ul>';
	for(var i in localWorldEventCache)
	{
		var newEvents=newWorldEventCount[i];
		eventList+='<li><a href="javascript:void(showRecentWorldEventsType(\''+i+'\'))">'+WorldEventName[i]+' ('+(newEvents ? newEvents:'')+')</a></li>';
		
	}
	eventList+='</ul>';
	
	var iHTML=
		'<h1><?php echo $language["recentevents"]; ?></h1>'+
		eventList
		
		;

	var elm=genFloatingBox(iHTML,generateRandomId(),0,0);
	document.body.appendChild(elm);
	makeDraggable(elm);
	centerElement(elm);
	recentEventCount=0;
	newWorldEventCount={};	
}

function processPlayerInfoXML(xml)
{
	var info=objectFromXMLNode(xml.documentElement);
	playerInfo=info;
	if (playerInfo.sessionover)
	{
		location.href='doreset.php';
		return;
	}
	if (info.newMessages>0)
		_('mailnotify').innerHTML='('+info.newMessages+')';
	else
		_('mailnotify').innerHTML='';
	if (info.newReports>0)
		_('reportnotify').innerHTML='('+info.newReports+')';
	else
		_('reportnotify').innerHTML='';
	var div=_('trdiv');
	var str=
	'<img class="image16" style="vertical-align:middle;" src="img/gold.png" alt="<?php echo $language["gold"]; ?>" title="<?php echo $language["gold"]; ?>"> <?php echo xprintf($language["goldtext"],array("<span id=\"goldindicator\"></span>","'+Math.round(info.goldProduction)+'")); ?>'+
	' | <img class="image16"  style="vertical-align:middle;" src="img/expansionpoints.png" alt="<?php echo $language["expansionpoints"]; ?>" title="<?php echo $language["expansionpoints"]; ?>"> <span id="expansionpointindicator">'+Math.floor(info.expansionPoints*100)/100+'</span>'+
	(parseFloat(info.nightBonus[0]) > 1 ? ' | <img class="image16"  style="vertical-align:middle; " src="img/nightbonus.png" alt="<?php echo $language["nightbonus"]; ?>" title="<?php echo $language["nightbonus"]; ?>"> <?php echo xprintf($language["nightbonustext"],array("'+Math.floor(info.nightBonus*100)/100+'")); ?>':'')+
	''
	;
	counters['goldindicator']={'pace':parseFloat(info.goldProduction[0]),'value':parseFloat(info.gold[0]),'decimals':0};
	div.innerHTML=str;
	var worldEvents=playerInfo.worldEvents[0].worldEvent;
	if (worldEvents)
	{
		for(var index=0;index<worldEvents.length;index++)
		{
			var event=worldEvents[index];
			if (event.type=='forcelogout')
			{
				location.href='doreset.php';
			}
			if (event.needFullRefresh[0]!='0')
			{
//				alert(event.needFullRefresh[0]);
				mapCache=new Object();
				villagesById=new Object();
			}
			else if (event.guildId[0]!='')
			{
				for(var j in villagesById)
				{
					var village=villagesById[j];
					if (village.guildId[0]==event.guildId[0])
					{
						addUpdatable(village.x[0],village.y[0]);
					}
				}				
			}
			else if (event.playerId[0]!='')
			{
				for(var j in villagesById)
				{
					var village=villagesById[j];
					if (village.userId[0]==event.playerId[0])
					{
						addUpdatable(village.x[0],village.y[0]);
					}
				}
			}
			else
			{
				var ex=event.x[0];
				var ey=event.y[0];
				if (mapCache[ex] && mapCache[ex][ey])
				{
					if (mapCache[ex][ey].villageInfo)
					{
						event.villageInfo=mapCache[ex][ey].villageInfo;
					}
					if (mapCache[ex][ey].villageInfo && (mapCache[ex][ey].villageInfo.diplomaticStance=='own'))
					{
						addUpdatable(ex,ey);
					}
					else
					{
						delete mapCache[ex][ey];
					}
				}
			}
			if (!localWorldEventCache[event.type]) localWorldEventCache[event.type]=[];
			localWorldEventCache[event.type].push(event);
			if (!newWorldEventCount[event.type]) newWorldEventCount[event.type]=0;
			newWorldEventCount[event.type]++;
			recentEventCount++;
		}
	}
	var nightBonusIndex=parseInt(playerInfo.nightBonusIndex[0],10);
	if (lastNbIndex!=nightBonusIndex)
	{
		reloadImages();
		setTimeout(function(){renderMap()},4000);
		lastNbIndex=nightBonusIndex;
	}
	
	var iHTML='';
/*	if (playerInfo.events && playerInfo.events[0].event)
	{
		var eventArray=playerInfo.events[0].event;
		for(var i=0;i<eventArray.length;i++)
		{
			var text=eventArray[i];
			iHTML+=text+'<br>';
		}
	}*/
	iHTML='<table>';
	if (playerInfo.eventSummary && playerInfo.eventSummary[0].event)
	{
		var eventArray=playerInfo.eventSummary[0].event;
		iHTML+='<tr><td><?php echo $language["eventtype"];?></td><td><?php echo $language["incoming"];?></td><td><?php echo $language["outgoing"];?></td></tr>';
		for(var i=0;i<eventArray.length;i++)
		{
			var gameEvent=eventArray[i];
			iHTML+='<tr>';
			var cd='';
			if (gameEvent.eventType=='incomingattack') cd='class="alert"';
			iHTML+='<td><span '+cd+'>'+gameEvent.eventName+'</span></td>'+
				'<td><a href="javascript:void(openInWindow(\'showevents.php?type='+gameEvent.eventType+'&category=incoming\'))">'+gameEvent.incoming+'</a></td>'+
				'<td><a href="javascript:void(openInWindow(\'showevents.php?type='+gameEvent.eventType+'&category=outgoing\'))">'+gameEvent.outgoing+'</a></td>';
			iHTML+='</tr>';
		}
	}
	else
	{
		iHTML+='<?php echo $language["noevents"];?>';
	}
	iHTML+='</table>';
	_('eventlist').innerHTML=iHTML;
	if (recentEventCount>0)
		_('recentnotify').innerHTML='('+recentEventCount+')';
	else
		_('recentnotify').innerHTML='';
	
}

function playerInfoCallback()
{
	var ok=genericAjaxEventHandler();
	if (ok)
	{
		processPlayerInfoXML(xmlHttp.responseXML);
	}
}

function getPlayerInfo()
{
	if (guestMode) return;
	if (!ajaxPost("dogetplayerinfo.php","",playerInfoCallback)) setTimeout(getPlayerInfo,Math.random()*1500+500);
}

function initMap(x,y)
{
	if (!mapElm) return;
	mapElm.width=mapElm.offsetWidth;
	mapElm.height=mapElm.offsetHeight;
	updateData();
	area=elmWidth*elmHeight;
	var cellSize=Math.sqrt(area/AVGIMAGECOUNT);
	var imageAspectRatio=Math.sqrt(IMGXASPECT/IMGYASPECT);
	cellSizeX=cellSizeY=cellSize;
	cellSizeX*=imageAspectRatio;
	cellSizeY/=imageAspectRatio;
	cellSizeX=Math.floor(cellSizeX);
	cellSizeY=Math.floor(cellSizeY);
	centerX=(x+0.5)*cellSizeX;
	centerY=(y+0.5)*cellSizeY;
	matrixWidth=parseInt(elmWidth/cellSizeX,10)+2;
	matrixHeight=parseInt(elmHeight/cellSizeY,10)+2;
	renderMap(x,y);
}

//setInterval(function(){centerX-=10; renderMap();},1000);

function mouseCoords(ev)
{
	var x,y,o;
    if (ev.touches && ev.touches[0])
    {
        x = ev.touches[0].pageX;
        y = ev.touches[0].pageY;
    }
    else if (ev.changedTouches && ev.changedTouches[0])
    {
        x = ev.changedTouches[0].pageX;
        y = ev.changedTouches[0].pageY;
    }
	else if (ev.pageX || ev.pageY)
	{ 
		x=ev.pageX;
		y=ev.pageY;
	} 
    else
    {
        x=ev.clientX + document.body.scrollLeft - document.body.clientLeft;
        y=ev.clientY + document.body.scrollTop  - document.body.clientTop; 
    }
	return {'x': x, 'y': y};
}

function cancelTask(taskNumber)
{
	var task=tasklist[taskNumber];
	if (task)
	{
		if (task.undo) task.undo();
	}
	tasklist.splice(taskNumber,1);
	
}

prevTasklistLength=0;
function updateTasksDiv()
{
	var tasklistChanged=prevTasklistLength!=tasklist.length;
	if (!tasklistChanged) return;
	tasklistChanged=false;
	var div=_("brdiv");
	var str='<ul>';
	for(var i=0;i<tasklist.length;i++)
	{
		str+='<li>'+tasklist[i].text+' <a href="javascript:void(cancelTask('+i+'))"><?php echo $language["cancel"]; ?></a></li>';
	}
	str+='</ul>';
	div.innerHTML=str;
	prevTasklistLength=tasklist.length;
}

function updateState()
{
	if (tasklist.length<1) getPlayerInfo();
}

function leftClickAction(mc)
{
}

window.onresize=function(e)
{
	initMap(centerX/cellSizeX,centerY/cellSizeY);
}

function coordToCell(mx,my)
{
	return {'x':Math.floor((leftPos+mx)/cellSizeX),'y':Math.floor((topPos+my)/cellSizeY)};
}

sctPrevX=0;
sctPrevY=0;

function showCellTooltip(x,y)
{
	if ((x==sctPrevX) && (y==sctPrevY)) return;
	sctPrevX=x;
	sctPrevY=y;
	if (!mapCache[x] || !mapCache[x][y] || !mapCache[x][y].villageInfo)
	{
		removeTooltip();
		return;
	}
	var village=mapCache[x][y].villageInfo;
	showTooltip('<?php echo xprintf($language["villagetooltip"],array("'+(village.villageName)+'","'+(village.userName)+'","'+(village.guildName)+'","'+(village.score)+'","'+village.x+'","'+village.y+'")); ?>');
	
}

function loadAllVillages()
{
	for(var i in playerVillages)
	{
		var village=playerVillages[i];
		createMapCell(village.x,village.y);
		addUpdatable(village.x,village.y);
	}
}

var befFrontElement=null;
function bringElementToFront(elm)
{
	elm.befPrevZ=elm.style.zIndex;
	elm.style.zIndex=200;
	if (befFrontElement)
	{
		befFrontElement.style.zIndex=elm.befPrevZ;
	}
	befFrontElement=elm;
}

/*
mapElm.onclick=function(e)
{
	var ev=e || window.event;
	mouseMode.onclick(ev);
}
*/


var mDown;	

function updateMousePos(ev)
{
    var mc = mouseCoords(ev);
   	mouseX=mc.x;
	mouseY=mc.y;
}

function mouseupEvHandler(e)
{
	var ev=e || window.event;
    updateMousePos(ev);
	mouseMode.onmouseup(ev);
	mouseMode.onclick(ev);
    ev.preventDefault();
	mDown=false;
}

function mousedownEvHandler(e)
{
	var ev=e || window.event;
	mouseMode.onmousedown(ev);
    ev.preventDefault();
	mDown=true;
}

function mouseleaveEvHandler(e)
{
	var ev=e || window.event;
	mouseMode.onmouseup(ev);
    ev.preventDefault();
	mDown=false;
}


mapElm.onmousedown=function(e)
{
    if (isTouchDevice) return;
    mousedownEvHandler(e);
}

mapElm.addEventListener('touchstart',function(e)
{
	var ev=e || window.event;
    mousedownEvHandler(e);
}, false);

mapElm.onmouseup=function(e)
{
    if (isTouchDevice) return;
    mouseupEvHandler(e);
}

mapElm.onmouseout=function(e)
{
    if (isTouchDevice) return;
    mouseleaveEvHandler(e);
}

mapElm.addEventListener('touchend',function(e)
{
    isTouchDevice = true;
    mouseupEvHandler(e);
}, false);

mapElm.addEventListener('touchleave',function(e)
{
    isTouchDevice = true;
    mouseleaveEvHandler(e);
}, false);

var updateOnMouseMove=false;

document.addEventListener('mousemove',function(e)
{
	if (updateOnMouseMove)
	{
		updateState();
		updateOnMouseMove=false;
	}
	var ev=e || window.event;
	ev.preventDefault();
    updateMousePos(ev);
},true);

mapElm.onmousemove=function(e)
{
    if (isTouchDevice) return;
	var ev=e || window.event;
	ev.preventDefault();
	var mc=mouseCoords(ev);
	cellCoord=coordToCell(mc.x,mc.y);
	_("cellX").innerHTML=cellCoord.x;
	_("cellY").innerHTML=cellCoord.y;
	if ((mouseMode.constructor!=SelectMouseMode) && !mDown)
		showCellTooltip(cellCoord.x,cellCoord.y);	
	if (mDown) removeTooltip();
	mouseMode.onmousemove(ev);
}

mapElm.addEventListener('touchmove', function(e)
{
    isTouchDevice = true;
	var ev=e || window.event;
	ev.preventDefault();
	var mc=mouseCoords(ev);
	cellCoord=coordToCell(mc.x,mc.y);
	_("cellX").innerHTML=cellCoord.x;
	_("cellY").innerHTML=cellCoord.y;
	mouseMode.onmousemove(ev);
}, false);

document.onkeydown=function(e)
{
	if (e.keyCode==16) selectMouseMode(new ActionMouseMode(),'actionmode');// SHIFT
	if (e.keyCode==17) selectMouseMode(new SelectMouseMode(),'selectmode'); // CTRL
};

document.onkeyup=function(e)
{
	selectMouseMode(new DefaultMouseMode(),'defaultmode');
};

setInterval(updateMapRegion,1000);
setInterval(updateTasksDiv,500);
setInterval(function(){updateOnMouseMove=true;},60000);
setInterval(function(){spinCounters(1000);},1000);

// automatically initiate the loading of the player's villages




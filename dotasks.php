<?php


$tasks=$_POST['tasks'];
if (get_magic_quotes_gpc()) $tasks=stripslashes($tasks);


error_reporting(E_ALL);

/*$str='" \" \" \" " "BAR"  "ZIG" "ZAG"';
preg_match_all('/"(\\.|[^\\"])*"/',$str,$arr);
print_r($arr);
die();*/


require_once('userworkerphps.php');
require_once("villageupdater.php");
bounceSessionOver();

if ($config['closed']) die($language['serverisclosed']);

$responses='';

updatePlayer($_SESSION['userId']);
$q=sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($_SESSION['userId']));
$r=doMySqlQuery($q);
if (mysql_num_rows($r)<1) die($language['sessionisover']);
$player=mysql_fetch_assoc($r);
$scoreChangedVillages=array();

$updatedVillages=array();

function fetchVillageRecord($villageId,$restrict=true)
{
	global $language;
	global $responses;
	$resFilter='';
	if ($restrict)
	{
		$resFilter=sqlPrintf("AND (ownerId='{1}')",array($_SESSION['userId']));
	}
	$q=sqlPrintf("SELECT * FROM wtfb2_villages WHERE (wtfb2_villages.id='{1}') $resFilter",array($villageId,));
	$r=doMySqlQuery($q);
	if (mysql_num_rows($r)<1)
	{
		$responses.=$language['villageisnotyours']."\n";
		return FALSE;
	}
	$a=mysql_fetch_assoc($r);
	return $a;
}

function upgradeBuilding($args)
{
	global $player;
	global $config;
	global $language;
	global $updatedVillages;
	global $responses;
	global $scoreChangedVillages;
	
	$villageId=$args[1];
	$buildingName=$args[2];
	
	if (!isset($updatedVillages[$villageId]))
	{
		updateVillage($villageId);
		$updatedVillages[$villageId]=true;
	}
	

	$village=fetchVillageRecord($villageId);
	if ($village===FALSE) return;
	if (!isset($config['buildings'][$buildingName]))
	{
		$responses.=(xprintf($language['buildingnotexist'],array($buildingName)))."\n";
		return;
	} 
	$building=$config['buildings'][$buildingName];
	if (!isset($building['buildingLevelDbName'])) die('Configuration fail: buildingLevelDbName not found.');
	$levName=$building['buildingLevelDbName'];
	if (!isset($building['costFunction'])) die('Configuration fail: cost function not found.');
	$costFn=$building['costFunction'];
	$cost=$costFn((int)$village[$levName]);
	if ($village['buildPoints']<1) 
	{
		$responses.=($language['notenoughbuildboints'])."\n";
		return;
	}
	if ($player['gold']<$cost) 
	{
		$responses.=($language['notenoughgold'])."\n";
		return;
	}
	$player['gold']-=$cost;
	$q=sqlPrintf("UPDATE wtfb2_villages SET {1}={1}+1, buildPoints=buildPoints-1 WHERE (id='{2}')",array($levName,$villageId));
	$r=doMySqlQuery($q,'jumpErrorPage');
	$scoreChangedVillages[$village['id']]=$village;
//	doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_worldevents (x,y,eventTime,type) VALUES ('{1}','{2}',NOW(),'scorechanged')",array($village['x'],$village['y'])));
	
}

function renameVillage($command)
{
	global $responses;
	$villageId=(int)$command[1];
	$newName=$command[2];
	if (strlen(trim($newName)) == 0) return;
	$q=sqlPrintf("UPDATE wtfb2_villages SET villageName='{1}' WHERE (id='{2}') AND (ownerId='{3}')",array($newName,$villageId,$_SESSION['userId']));
	$r=doMySqlQuery($q);
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_villages WHERE (id='{1}')",array($villageId)));
	if (mysql_num_rows($r)==0) return;
	//$responses.='X';
	$village=mysql_fetch_assoc($r);
	doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_worldevents (x,y,eventTime,type) VALUES ({1},{2},NOW(),'rename')",array($village['x'],$village['y'])));
}

function trainUnits($command)
{
	global $player;
	global $config;
	global $language;
	global $updatedVillages;
	global $responses;
	
	$villageId=(int)$command[1];
	$amount=(int)$command[2];
	$unitType=$command[3];
	if ($amount<0) $amount=0;
	
	if (!isset($updatedVillages[$villageId]))
	{
		updateVillage($villageId);
		$updatedVillages[$villageId]=true;
	}
	if (!isset($config['units'][$unitType])) 
	{
		$responses.=(xprintf($language['unitnotexist'],array($unitType)))."\n";
		return;
	}
	$unit=$config['units'][$unitType];
	if (!isset($unit['trainingDbName'])) die('Configuration failure: Unit.trainingDbName is not found.');
	$trDbName=$unit['trainingDbName'];
	$a=fetchVillageRecord($villageId);
	if ($a==FALSE) return;
	if ($player['gold']<$unit['cost']*$amount) return; //die($language['notenoughgold']);
	$q=sqlPrintf("UPDATE wtfb2_villages SET {1}={1}+{2} WHERE (id='{3}')",array($trDbName,$amount,$villageId));
	$r=doMySqlQuery($q);
	$player['gold']-=$unit['cost']*$amount;
}

function launchSettling($command)
{
		
	global $player;
	global $config;
	global $language;
	global $responses;

	$launcherVillageId=(int)$command[1];
	$targetX=(int)$command[2];
	$targetY=(int)$command[3];
	
	if (!isset($updatedVillages[$launcherVillageId]))
	{
		updateVillage($launcherVillageId);
		$updatedVillages[$launcherVillageId]=true;
	}

	// does player have enough expansion points?
	if ((double)$player['expansionPoints']<1)
	{
		$responses.=($language['notenoughexpansionpoints'])."\n";
		return;
	}
	
	// fetch village
	$r=doMySqlQuery(sqlPrintf("SELECT *  FROM wtfb2_villages WHERE (id='{1}') AND (ownerId='{2}')",array($launcherVillageId,$player['id'])));
	if (mysql_num_rows($r)==0)
	{
		$responses.=($language['invalidlaunchervillage'])."\n";
		return;
	}
	$launcherVillage=mysql_fetch_assoc($r);
	
	// enough settlers?
	if (!isset($config['settlerUnit'])) die('Configuration failure: no settler unit specified. Config.settlerUnit');
	$settlerType=$config['settlerUnit'];
	if (!isset($config['units'][$settlerType])) die('Configuration failure: settler unit not found. Config.units[Config.settlerUnit]');
	$settlerDescriptor=$config['units'][$settlerType];
	if (!isset($settlerDescriptor['countDbName'])) die('Configuration failure: the database column name not found. Config.units[Config.settlerUnit].countDbName');
	$amountName=$settlerDescriptor['countDbName'];
	$amount=$launcherVillage[$amountName];
	if ($amount<1)
	{
		$responses.=($language['notenoughsettlerunits'])."\n";
		return;
	}
	
	// anybody settled there?
	
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_villages WHERE (x='{1}') AND (y='{2}')",array($targetX,$targetY)));
	if(mysql_num_rows($r)>0)
	{
		$responses.=($language["someonesettledthere"])."\n";
		return;
	}
	
	// so all is ok, set the event
	
	//* calculate the distance
	$dx=$launcherVillage['x']-$targetX;
	$dy=$launcherVillage['y']-$targetY;
	$travelDistance=sqrt($dx*$dx+$dy*$dy);
	$travelTime=$travelDistance/(int)$settlerDescriptor['speed']*3600/$config['serverSpeed'];
	$moddedTime=randomizeTime($travelTime);
	
	// decrease the expansionPoints
	$player['expansionPoints']=(double)$player['expansionPoints']-1;
/*	print_r($player);
	die();*/
	doMySqlQuery(
		sqlPrintf(
			"
				INSERT INTO wtfb2_events (eventType,estimatedTime,happensAt,launchedAt,launcherVillage,targetX,targetY,{1})
				VALUES
				('settle',TIMESTAMPADD(SECOND,{2},NOW()),TIMESTAMPADD(SECOND,{3},NOW()),NOW(),{4},{5},{6},1)
			",array($amountName,$travelTime,$moddedTime,$launcherVillageId,$targetX,$targetY)
		)
	);
	
	
	// take the settler
	doMySqlQuery(sqlPrintf("UPDATE wtfb2_villages SET {1}={1}-1 WHERE (id={2})",array($amountName,$launcherVillageId)));
}

function sendTroops($command)
{
	global $config;
	global $language;
	global $responses;
	global $player;
	$levelNames=array();
	$costs=array();
	
	foreach($config['units'] as $key=>$value)
	{
		$levelNames[]=$value['countDbName'];
		$costs[]=$value['cost'];
	}

	$n=count($command);
	$ctr=1;
	if ($command[$ctr++]!='FOR') die('Syntax error, the programmer proably did something wrong... FOR missing.');
	$action=$command[$ctr++];
	if ($command[$ctr++]!='TO') die('Syntax error, the programmer proably did something wrong... TO missing.'); 	
	$destinationVillageId=(int)$command[$ctr++];
	if ($command[$ctr++]!='TARGET') die('Syntax error, the programmer proably did something wrong... TARGET missing.'); 	
	$catapultTarget=$command[$ctr++];
	$heroModStr=$command[$ctr++];
	$withHero=false;
	if ($heroModStr=='WITHHERO') $withHero=true;
	else if ($heroModStr=='WITHOUTHERO') $withHero=false;
	else die('Syntax error, the programmer proably did something wrong... WITHHERO or WITHOUTHERO missing.');
	if ($command[$ctr++]!='FROM') die('Syntax error, the programmer proably did something wrong... FROM missing.'); 
	$destinationVillage=fetchVillageRecord($destinationVillageId,false);
	if ($destinationVillage==FALSE) return;
	$ctr=6;
	$currentToken;
	$senderVillages=array();
	for(;$ctr<$n;$ctr++)
	{
		$currentToken=$command[$ctr];
		if ($currentToken=='AMOUNTS') break;
		$senderVillages[]=(int)$currentToken;
	}
	if ($ctr==$n) die('Syntax error,  the programmer proably did something wrong... AMOUNTS missing.');
	$ctr++; // skip the amounts
	$amounts=array();	
	$index=0;
	$armyCost=0;
	for(;$ctr<$n;$ctr++)
	{
		$currentToken=$command[$ctr];
		if ($currentToken=='') continue;
		$amount=floor((double)$currentToken);
		if ($amount<0) return; // negative amount is invalid
		$amounts[$levelNames[$index]]=$amount;
		$armyCost+=$amount*$costs[$index];
		$index++;
	}
	// check against the age interaction limit.
	if (($action=='move') && ($player['id']!=$destinationVillage['ownerId']))
	{
		$r=doMySqlQuery(sqlPrintf("SELECT TIMESTAMPDIFF(SECOND,'{1}',NOW()) AS senderPlayTime,TIMESTAMPDIFF(SECOND,regDate,NOW()) AS recipientPlayTime FROM wtfb2_users WHERE (id='{2}')",array($player['regDate'],$destinationVillage['ownerId'])));
		if (mysql_num_rows($r)>0)
		{
			$a=mysql_fetch_assoc($r);
			$spt=$a['senderPlayTime'];
			$rpt=$a['recipientPlayTime'];
			if ($spt<$rpt)
			{
				$s=$spt; $spt=$rpt; $rpt=$s;
			}
			if ($spt/$rpt>(double)$config['ageInteractionLimit'])
			{
				$responses.=xprintf($language['youcantsendtroopstothisuser'],array($config['ageInteractionLimit']));
				return;
			}
		}
	}
	// deputy mode can't send troops away
	if (isset($_SESSION['asdeputy']))
	{
		if ($action!='move')
		{
			$responses.=$language['youcantattackasdeputy'];
			return;
		}
		else
		{
			if ($destinationVillage['ownerId']!=$_SESSION['userId'])
			{
				$responses.=$language['youcantgiveawayasdeputy'];
				return;				
			}
		}
	}
	$unitAmounts=array();
	$index=0;
	$speed=100;
	$queryPart=array();
/*	foreach($config['units'] as $key=>$value)
	{
		$amount=$amounts[$value['countDbName']];
		$unitAmounts[]=$language[$value['languageEntry']].': '.$amount;
		$queryPart[]="SIGN(FLOOR(${value['countDbName']})*SIGN($amount))*1/${value['speed']}";
	}
	$speedModPart="GREATEST(".implode(',',$queryPart).") AS timeMod";*/
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_heroes WHERE (ownerId='{1}')",array($player['id'])));
	$hero;
	if (mysql_num_rows($r)==0) $withHero=false;
	else $hero=mysql_fetch_assoc($r);

	if (count($senderVillages)==0)
	{
		$responses.=$language['invalidlaunchervillage'];
		return;
	}

	$r=doMySqlQuery(
		sqlPrintf(
		"
			SELECT *,SQRT(POW({1}-x,2)+POW({2}-y,2)) AS distance
			FROM wtfb2_villages
			WHERE (id IN (".implode(',',$senderVillages).")) AND (ownerId={3})
		",array($destinationVillage['x'],$destinationVillage['y'],$player['id'])
		)
	);
	$sum=array();
	$rows=array();
	$orgRows=array();
	while($row=mysql_fetch_assoc($r))
	{	
		$orgRows[]=$row;
		foreach($levelNames as $key=>$countName)
		{
			if (!isset($sum[$countName])) $sum[$countName]=0;
			$row[$countName]=floor((double)$row[$countName]);
			$sum[$countName]+=floor((double)$row[$countName]);
		}
		$rows[]=$row;
	}
/*	if ($player['id']==4)
	{
		ob_start();
		print_r($orgRows);
		logText(ob_get_contents());
		ob_end_clean();
	}*/
	foreach($levelNames as $key2=>$countName)
	{
		if ($sum[$countName]<$amounts[$countName])
		{
			$responses.=$language["notenougthtroops"]."\n";
			return;
		}
	}
/*	if ($player['id']==4)
	{
		ob_start();
		print_r($rows);
		logText(ob_get_contents());
		ob_end_clean();
	}*/
//	echo implode(',',$sum)."\n\n";
//	echo implode(',',$amounts)."\n\n";
	$modSum=array();
	$pluses=array();
	foreach($rows as $key=>$row)
	{		
//		$modamounts[$row['id']]=array();
//		echo '[';
		foreach($levelNames as $key2=>$countName)
		{
			if ($sum[$countName]!=0)
			{
				if ($player['id']==4)
				{
					logText($rows[$key]['id'].": ".$rows[$key][$countName]."=>");
				}
				$multiplied=$rows[$key][$countName]*$amounts[$countName];
				if (!isset($modSum[$countName])) $modSum[$countName]=0;
				if (!isset($pluses[$countName])) $pluses[$countName]=0;
				$modSum[$countName]+=$multiplied%$sum[$countName];
				if ($modSum[$countName]>=$sum[$countName])
				{
					$modSum[$countName]-=$sum[$countName];
					$pluses[$countName]++;
				}
				$toTake=floor($multiplied/$sum[$countName])+$pluses[$countName];
				if ($toTake<=$rows[$key][$countName])
				{
					$rows[$key][$countName]=$toTake;
					$pluses[$countName]=0;
				}
			}
//			echo $rows[$key][$countName].' '.$countName;
		}
//		echo "]\n";
	}
/*	if ($player['id']==4)
	{
		ob_start();
		print_r($rows);
		logText(ob_get_contents());
		ob_end_clean();
		logText("\n\n============================\n\n");
	}*/
	$toInsertEvent=array();
	foreach($rows as $key=>$row)
	{
		$countDec=array();
		$timeFactor=0;
		$unitVector=array();
		if (($withHero) && ($hero['inVillage']==$row['id']))
		{
			$timeFactor=1/(double)$config['heroSpeed'];
		}
		foreach($config['units'] as $key2=>$value)
		{
			$countName=$value['countDbName'];
			$amount=$row[$countName];
			if ($amount>0)
			{
				$tf=1.0/(double)$value['speed'];
				if ($timeFactor<$tf) $timeFactor=$tf;
			}
			$countDec[]=sqlPrintf("{1}={1}-{2}",array($countName,$amount));
			$unitVector[]=$amount;
		}
		// check troop cost
		$armyCost=0;
		foreach($unitVector as $key=>$value)
		{
                        $armyCost+=$value*$costs[$key];
		}
                if (($armyCost<$config['minimalArmyValueRate']*$player['goldProduction']) && ($action!='move') && ($action!='heromove'))
                {
                        echo $action.'\n';
                        $responses.=xprintf($language['pleasesendmoretroops'],array($config['minimalArmyValueRate']*$player['goldProduction']))."\n";
                        continue;
                }
		
		if ($timeFactor==0) continue;
		doMySqlQuery(sqlPrintf("UPDATE wtfb2_villages SET ".implode(',',$countDec)." WHERE (id='{1}') AND (ownerId='{2}')",array($row['id'],$player['id'])));
		$secs=3600*$row['distance']*$timeFactor/$config['serverSpeed']; // less seconds travel time 
		$realSecs=randomizeTime($secs);
		$sendHeroId='0';
		if (($withHero) && ($hero['inVillage']==$row['id']))
		{
			doMySqlQuery(sqlPrintf("UPDATE wtfb2_heroes SET inVillage=0 WHERE (id='{1}')",array($hero['id']))); // hogy ne legyen ott a hős.
			$sendHeroId=$hero['id'];
		}
		$toInsertEvent[]=
		sqlPrintf(
			"('{1}',TIMESTAMPADD(SECOND,{2},NOW()),TIMESTAMPADD(SECOND,{3},NOW()),NOW(),{4},{5},".implode(',',$unitVector).",'{6}','{7}')",
			array($action,$secs,$realSecs,$row['id'],$destinationVillageId,$catapultTarget,$sendHeroId)
		);
	}
	if (count($toInsertEvent)>0)
		doMySqlQuery("INSERT INTO wtfb2_events (eventType,estimatedTime,happensAt,launchedAt,launcherVillage,destinationVillage,".implode(',',$levelNames).",catapultTarget,heroId) VALUES ".implode(',',$toInsertEvent)."\n");
}

function getTimeToFinish(&$v)
{
	return (double)$v['enqueued']/(double)$v['rate'];
}

function enqueue(&$v,$amount)
{
	$v['enqueued']+=$amount;
	if (!isset($v['newTraining'])) $v['newTraining']=0;
	$v['newTraining']+=$amount;
}

function compareVillage(&$v1,&$v2)
{
	return (getTimeToFinish($v1))-(getTimeToFinish($v2));
}

function planMassTraining(&$villages,$amountToTrain) // not taking database village record!
{
	
	usort($villages,'compareVillage');

	$n=count($villages);
	for($j=0;$j<$n;$j++)
	{
		if ($j<$n-1)
		{
			$sumAmount=0;
			for($k=0;$k<=$j;$k++)
			{
				$diff=getTimeToFinish($villages[$j+1])-getTimeToFinish($villages[$k]);
				if ($diff<=0) continue;
				$amount=$diff*$villages[$k]['rate'];
				$sumAmount+=$amount;
			}
			if ($sumAmount==0) continue;
			$krate=(double)$amountToTrain/(double)$sumAmount;
			if ($krate>1) $krate=1;
			for($k=0;$k<=$j;$k++)
			{
				$diff=getTimeToFinish($villages[$j+1])-getTimeToFinish($villages[$k]);
				if ($diff<=0) continue;
				$amount=$diff*$villages[$k]['rate']*$krate;
				$amountToTrain-=$amount;
				enqueue($villages[$k],$amount);
//				echo "ENQUEUING $amount, DIFF WAS $diff, KRATE WAS $krate, RATE WAS ".$villages[$k]['rate']."\n";
			}
/*			echo "/\\/\\/\\/\\/\\/\\/\\/\\/\\/\\/\\/\\/\n";
			print_r($villages);
			echo "$amountToTrain\n";
			echo "/\\/\\/\\/\\/\\/\\/\\/\\/\\/\\/\\/\\/\n";*/
			if ($krate<1)
			{
//				echo "WE R READY GETTIN OUT!";
				break;
			}
		}
		else
		{
//			echo 'FINAL CREATION';
			$sr=0;
			for($k=0;$k<$n;$k++)
			{
				$sr+=$villages[$k]['rate'];
			}
			$unitPerTimeUnit=$amountToTrain/$sr;
			for($k=0;$k<$n;$k++)
			{
				$amount=$unitPerTimeUnit*$villages[$k]['rate'];
				enqueue($villages[$k],$amount);	
				$amountToTrain-=$amount;
			}
		}
	}

	$frac=0;
	for($i=0;$i<$n;$i++)
	{
		if (!isset($villages[$i]['newTraining'])) 
		{
			$villages[$i]['newTraining']=0;
			continue;
		}
		$onumber=$villages[$i]['newTraining'];
		$number=floor($onumber);
		$f=$onumber-$number;
		$frac+=$f;
		$ffrac=floor($frac+5e-6);
		$villages[$i]['newTraining']=$number+$ffrac;
		$frac-=$ffrac;
	}
}

function massTraining($command)
{
	global $config;
	global $language;
	global $responses;
	global $player;
/*	if ($player['permission']!='admin')
	{
		$responses.="Sajna még szar.... Rakd be kézzel a kiképzendőt, vagy ne rakj be egységet addig. :(\n";
		return;
	}*/

	$ctr=1;
	$n=count($command);
	// read the unit amounts
	$amounts=array();
	for(;$ctr<$n;$ctr++)
	{
		$token=$command[$ctr];
		if ($token=='IN') break;
		$amount=(int)$token;
		if ($amount<0) return; // negative value is invalid.
		$amounts[]=$amount;
	}
	if ($ctr==$n)
	{
		$responses.='Syntax error: IN missing.'."\n";
		return;
	}
	$ctr++; // skip IN
	// read villages
	$villages=array();
	for(;$ctr<$n;$ctr++)
	{
		$token=$command[$ctr];
		$villages[]=(int)$token;
	}
	if (count($villages)<1) return; // no village selected is a NOP.
	// calculate the gold needed
	$gold=0;
	$i=0;
	foreach($config['units'] as $key=>$value)
	{
		$gold+=$value['cost']*$amounts[$i++];
	}
	if ($gold>$player['gold'])
	{
		$responses.=$language['notenoughgold']."\n";
		return;
	}
	// 
	foreach($villages as $key=>$value)
	{
		updateVillage($value);
	}
	// Now load the villages
	$q=sqlPrintf("SELECT * FROM wtfb2_villages WHERE (id IN (".implode(',',$villages).")) AND (ownerId='{1}')",array($player['id']));
	$r=doMySqlQuery($q);
	$villageRecords=array();
	while($row=mysql_fetch_assoc($r))
	{
		$villageRecords[]=$row;
	}
	// Now calculate how many units needed
	$actr=0;
//	print_r($villageRecords);
//	$f=fopen('mtsuck','w+t');
	foreach($config['units'] as $key=>$unitDescriptor)
	{
		$amount=$amounts[$actr++];
		if ($amount==0) continue;
		$mtVillages=array();
		$trainerBuilding=$config['buildings'][$unitDescriptor['trainedAt']];
		$trainingDbName=$unitDescriptor['trainingDbName'];
		$blDbName=$trainerBuilding['buildingLevelDbName'];
		$timeReductionFn=$trainerBuilding['timeReductionFunction'];
		foreach($villageRecords as $key2=>$village)
		{
			$mtVillage=array();
			$mtVillage['id']=$village['id'];
			$mtVillage['rate']=1/$timeReductionFn($village[$blDbName]);
			$mtVillage['enqueued']=(double)$village[$trainingDbName];
			$mtVillages[]=$mtVillage;
		}
/*		ob_start();
		usort($mtVillages,'compareVillage');
		print_r($mtVillages);
		echo "-----------\n";*/
		planMassTraining($mtVillages,$amount);
/*		print_r($mtVillages);
		echo "=====\n";
		$contents=ob_get_contents();
		fwrite($f,$contents);
		ob_end_clean();*/
		// organize them by id
		$byId=array();
		foreach($mtVillages as $key=>$value)
		{
			$byId[$value['id']]=$value;
		}
		foreach($villageRecords as $key2=>$village)
		{
			$mtVillage=$byId[$village['id']];
			$villageRecords[$key2][$trainingDbName]=(double)$villageRecords[$key2][$trainingDbName]+$mtVillage['newTraining'];
		}
	}
/*	fclose($f);
	die();*/
/*	print_r($villageRecords);
	die();*/
	// prepare writing back.
	foreach($villageRecords as $key=>$village)
	{
		$updateStr=array();
		foreach($village as $key2=>$col)
		{
			$updateStr[]=$key2.'=\''.mysql_real_escape_string($col)."'";
		}
		$q=sqlPrintf("UPDATE wtfb2_villages SET ".implode(',',$updateStr)." WHERE (id='{1}') AND (ownerId='{2}')",array($village['id'],$player['id']));
		doMySqlQuery($q);
	}
	$player['gold']-=$gold;
	
	// Now set up the query
/*	$trainingPart=array();
	$i=0;
	foreach($config['units'] as $key=>$value)
	{
		$trainingPart[]=sqlPrintf("{1}={1}+{2}",array($value['trainingDbName'],$amounts[$i++]));
	}
	
	$q=sqlPrintf("UPDATE wtfb2_villages SET ".implode(',',$trainingPart)." WHERE (id IN (".implode(',',$villages).")) AND (ownerId='{1}')",array($player['id'])); // set up training
	doMySqlQuery($q);
	$player['gold']-=$gold; // take the gold*/
	
}

function moveHero($command)
{
	global $config;
	global $language;
	global $responses;
	global $player;
	
	$destinationVillageId=(int)$command[1];
	
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_heroes WHERE (ownerId='{1}')",array($player['id'])),'jumpErrorPage');
	if (mysql_num_rows($r)==0)
	{
		$responses.=($language['heronotexist']);
		return;
	}
	$hero=mysql_fetch_assoc($r);
	
	$fromVillage=fetchVillageRecord($hero['inVillage'],false);
	$toVillage=fetchVillageRecord($destinationVillageId,false);
	if (($fromVillage===FALSE) || ($toVillage===FALSE)) return;
	
	$dx=(int)$fromVillage['x']-(int)$toVillage['x'];
	$dy=(int)$fromVillage['y']-(int)$toVillage['y'];
	$distance=sqrt($dx*$dx+$dy*$dy);
	$travelTime=$distance*3600/(double)$config['heroSpeed']/$config['serverSpeed'];
	$realTravelTime=randomizeTime($travelTime);
	
	doMySqlQuery(
		sqlPrintf(
			"
				INSERT INTO wtfb2_events (eventType,launchedAt,estimatedTime,happensAt,launcherVillage,destinationVillage,heroId)
				VALUES
				 ('heromove',NOW(),TIMESTAMPADD(SECOND,{1},NOW()),TIMESTAMPADD(SECOND,{2},NOW()),{3},{4},{5})
			",array($travelTime,$realTravelTime,$fromVillage['id'],$toVillage['id'],$hero['id'])
		)
	);
	doMySqlQuery(sqlPrintf("UPDATE wtfb2_heroes SET inVillage=0 WHERE ('{1}'=id)",array($hero['id'])));
}

function setSpareBuildPoints($command)
{
	global $config;
	global $language;
	global $responses;
	global $player;
	
	$villageId=$command[1];
	$newValue=(int)$command[2];
	doMySqlQuery(sqlPrintf("UPDATE wtfb2_villages SET spareBuildPoints='{2}' WHERE (id='{1}') AND (ownerId='{3}')",array($villageId,$newValue,$player['id'])));	
}

function doMassBuilding(&$villageArray,$buildingId,$maxGold,$maxLevel)
{
	global $config;
	$bd=$config['buildings'][$buildingId];
	$levelName=$bd['buildingLevelDbName'];
	$costFn=$bd['costFunction'];
	$n=count($villageArray);
	
	
	$tmpLevels=array();
	for($i=0;$i<$n;$i++)
	{
		$tmpLevels[$i]=$villageArray[$i][$levelName];
	}
	
	
	$spent=0;
	$built=false;
	for($i=0;$i<$n;$i++)
	{
		$iLevel=$tmpLevels[$i];
	
		if (($iLevel>=$maxLevel) && ($maxLevel>0))
		{
//			echo "X";
			return $spent;
		}
	
		$bLevel=$villageArray[$i][$levelName];
		$uCost=$costFn($bLevel);
		$bp=$villageArray[$i]['buildPoints'];
	
		if ($bp>=$villageArray[$i]['spareBuildPoints']+1) 
		{
			if ($spent+$uCost>$maxGold)
			{
//				echo "Y";
				return $spent;
			}		
			$villageArray[$i][$levelName]++;
			$villageArray[$i]['buildPoints']--;
			$spent+=$uCost;
			$built=true;
		}
	
		$tmpLevels[$i]++;
	
	
		if ($i+1<$n)
		{
			if ($tmpLevels[$i+1]>$iLevel)
			{
				$i=-1;
				$built=false;
			}
		}
		else
		{
			if ((($iLevel<$maxLevel) || ($maxLevel==-1)) && ($built))
			{
				$i=-1;
				$built=false;
			}
		}
		
/*		echo "tmpArray:\n";
		print_r($tmpLevels);
		echo "levelArray:\n";
		print_r($levelsArray);
		echo "bpArray:\n";
		print_r($bpArray);
		echo "-----------\n";
		$maxIter--;*/
//		if ($maxIter<=0) return $spent;
	}
	return $spent;
}


function massBuild($command)
{
	global $config;
	global $language;
	global $responses;
	global $player;
	global $scoreChangedVillages;
	
	$ctr=1;
	$n=count($command);
	$villageIds=array();
	for(;$ctr<$n;$ctr++)
	{
		$token=$command[$ctr];
		if ($token=='MAXLEVEL') break;
		$villageIds[]=(int)$token;
	}
	if ($ctr==$n)
	{
		$responses.='Syntax error MAXLEVEL missing'."\n";
		return;
	}
	$ctr++;
	$maxLevel=(int)$command[$ctr++];
	if ($command[$ctr++]!='MAXGOLD')
	{
		$responses.='Syntax error MAXGOLD missing!'."\n";
		return;
	}
	$maxGold=(double)$command[$ctr++];
	if ($command[$ctr++]!='BUILDING')
	{
		$responses.='Syntax error BUILDING missing!'."\n";
		return;
	}
	$building=$command[$ctr++];
	if (!isset($config['buildings'][$building]))
	{
		$responses.='Unknown building!'."\n";
		return;
	}
	$bd=$config['buildings'][$building];
	$bldn=$bd['buildingLevelDbName'];
	if ($maxGold>$player['gold']) $maxGold=$player['gold'];
	foreach($villageIds as $key=>$value)
	{
		updateVillage($value);
	}
	
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_villages WHERE (id IN (".implode(',',$villageIds).")) AND (ownerId='{1}') ".($maxLevel>0 ? 'AND ({2}<{3})':'')."ORDER BY {2}",array($player['id'],$bldn,$maxLevel)));
	$villages=array();
	while($a=mysql_fetch_assoc($r))
	{
		$villages[]=$a;
	}
	
	$oldVillages=$villages;
	$spent=doMassBuilding($villages,$building,$maxGold,$maxLevel);
	$player['gold']-=$spent;
	$escapedVillages=array();
	foreach($villages as $key=>$village)
	{
		$tmp=array();
		foreach($village as $column=>$value)
		{
			$tmp[]=sqlPrintf("{1}='{2}'",array($column,$value));
		}
		doMySqlQuery(sqlPrintf("UPDATE wtfb2_villages SET ".implode(', ',$tmp)." WHERE (id='{1}')",array($village['id'])));
		if ($oldVillages[$key][$bldn]!=$village[$bldn])
			$scoreChangedVillages[$village['id']]=$village;
	}
	
	
}

function mergeEscaped($pre,$separatorWas)
{
	$after=array();
	$arrayIndex=0;
	foreach($pre as $key=>$value)
	{
		$len=strlen($value);
		$slashCount=0;
		for($i=$len-1;$i>=0;$i--)
		{
			if ($value[$i]=="\\" )
			{
				$slashCount++;
			}
			else break;
		}
		if (($slashCount%2)==1)
			$value=substr($value,0,$len-1);
		if (!isset($after[$arrayIndex])) $after[$arrayIndex]=$value; else $after[$arrayIndex].=$separatorWas.$value;
		if (($slashCount%2)==0) $arrayIndex++;
		
	}
	return $after;
}

$tasklines=mergeEscaped(explode("\n",$tasks),"\n");


/*print_r($tasks);
echo "\r\n\r\n";
print_r($tasklines);
echo "\r\n\r\n";*/
foreach($tasklines as $key=>$commandLine)
{
//	preg_match_all('/"(\\.|[^\\"])*"/',$commandLine,$arguments); //explode(' ',preg_replace('/\s\s+/',' ',$commandLine));
	$arguments=mergeEscaped(explode(' ',$commandLine),' ');
	foreach($arguments as $key=>$value) $arguments[$key]=str_replace('\\\\','\\',$value);
/*	echo "\r\n\r\n";
	print_r($arguments);
	die();*/
	foreach($arguments as $key=>$value)
	{
		$arguments[$key]=mysql_real_escape_string($value);
	}
	$commandName=$arguments[0];
	if ($commandName=='UPGRADEBUILDING') upgradeBuilding($arguments);
	else if ($commandName=='RENAMEVILLAGE') renameVillage($arguments);
	else if ($commandName=='TRAINUNITS') trainUnits($arguments);
	else if ($commandName=='SETTLEVILLAGE') launchSettling($arguments);
	else if ($commandName=='SENDTROOPS') sendTroops($arguments);
	else if ($commandName=='HEROMOVE') moveHero($arguments);
	else if ($commandName=='MASSTRAINING') massTraining($arguments);
	else if ($commandName=='SETSPAREBP') setSpareBuildPoints($arguments);
	else if ($commandName=='MASSBUILD') massBuild($arguments);
	else if ($commandName=='');
	else $responses.=('unknown command: "'.$commandName.'"')."\n";
}

$setString='';
$first=true ;
foreach($player as $key=>$value)
{
	if (!$first) $setString.=', ';
	$first=false;
	if ($value=='')
		$setString.=$key."=NULL";
	else
		$setString.=$key."='".mysql_real_escape_string($value)."'";
}

$q=sqlPrintf("UPDATE wtfb2_users SET $setString WHERE (id={1})",array($_SESSION['userId']));
$r=doMySqlQuery($q);
recalculatePlayerInfo($_SESSION['userId']);
if ($responses!='')
{
	die($responses);
}

foreach ($scoreChangedVillages as $key=>$village)
{
	doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_worldevents (x,y,eventTime,type) VALUES ('{1}','{2}',NOW(),'scorechanged')",array($village['x'],$village['y'])));
}

header('content-type: application/xml; charset=utf-8');
echo <<< X
<?xml version="1.0" encoding="utf-8"?>
<x>
	<y/>
</x>
X;

?>


























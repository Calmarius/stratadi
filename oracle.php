<?php

require_once("userworkerphps.php");
require_once("inbuiltpie.php");
$public=isset($_GET['public']);
if (!$public)
	bounceNoAdmin();
$r=doMySqlQuery("SELECT TIMESTAMPDIFF(SECOND,lastOracleTime,NOW()) AS secondsElapsed FROM `wtfb2_worldupdate` WHERE 1");
$a=mysql_fetch_assoc($r);
$secondsElapsed=(int)$a['secondsElapsed'];
if ($public)
{
	if ($secondsElapsed<86400*7/$config['serverSpeed'])
	{
		readfile("oraclecache.htm");
	}
}

$hidden=$public || isset($_GET['hidden']);

$goldTop=doMySqlQuery("SELECT goldProduction,userName,id FROM wtfb2_users ORDER BY goldProduction DESC LIMIT 0,10");
$countDbNames=array();
$eventCountDbNames=array();
$sumCountDbNames=array();
foreach($config['units'] as $key=>$value)
{
	$cdbName=$value['countDbName'];
	$countDbNames[]=$cdbName;
	$sumCountDbNames[]="SUM($cdbName)";
	$sumEventCountDbNames[]="SUM(e.$cdbName)";
	$eventCountDbNames[]="e.$cdbName";
}

$sumQueryPart=implode('+',$sumCountDbNames);
$troopQueryPart=implode(',',$countDbNames);
$eventQueryPart=implode(',',$eventCountDbNames);
$sumEventQueryPart=implode('+',$sumEventCountDbNames);


$q=<<< X
SELECT $sumQueryPart AS army,u.userName
FROM
(
	(
		SELECT $troopQueryPart,ownerId
		FROM wtfb2_villages v
	)
	UNION
	(
		SELECT  $eventQueryPart,v.ownerId
		FROM wtfb2_events e
		INNER JOIN wtfb2_villages v ON ( ((v.id=e.launcherVillage) AND (e.eventType<>'return')) OR ((v.id=e.destinationVillage) AND (e.eventType='return')) )
	)
) tmp
INNER JOIN wtfb2_users u ON (ownerId=u.id)
GROUP BY u.id
ORDER BY army DESC
LIMIT 0,10
X
;
$q=
"
SELECT SUM(army) AS army,u.userName,u.id FROM
(
	(
		SELECT $sumQueryPart AS army,ownerId
		FROM wtfb2_villages v
		GROUP BY ownerId
	)
UNION
	(
		SELECT  $sumEventQueryPart AS army,v.ownerId
		FROM wtfb2_events e
		INNER JOIN wtfb2_villages v ON (v.id=e.launcherVillage)
		WHERE (e.eventType<>'return')
		GROUP BY v.ownerId
	)
UNION
	(
		SELECT  $sumEventQueryPart AS army,v.ownerId
		FROM wtfb2_events e
		INNER JOIN wtfb2_villages v ON (v.id=e.destinationVillage)
		WHERE (e.eventType='return')
		GROUP BY v.ownerId
	)
) tmp
INNER JOIN wtfb2_users u ON (ownerId=u.id)
GROUP BY ownerId
ORDER BY army DESC
LIMIT 0,10
";

$armyTop=doMySqlQuery($q);
$townHallTop=doMySqlQuery(<<< X
SELECT AVG(townHallLevel) AS thLevel,userName,u.id
FROM wtfb2_villages v
JOIN wtfb2_users u ON (u.id=v.ownerId)
GROUP BY u.id 
ORDER BY thLevel DESC
LIMIT 0,10
X
);

$playerTop=doMySqlQuery(<<< X
SELECT totalScore,userName,id
FROM wtfb2_users
ORDER BY totalScore DESC
LIMIT 0,10
X
);

$heroTop=doMySqlQuery
(
"
	SELECT h.*,".xprintf($config['experienceFunctionMySql'],array('offense'))."+".xprintf($config['experienceFunctionMySql'],array('defense'))."+1 AS level, u.userName
	FROM wtfb2_heroes h
	JOIN wtfb2_users u ON (h.ownerId=u.id)
	ORDER BY level DESC
	LIMIT 0,10
"
);

$offenseTop=doMySqlQuery
(
"
	SELECT *
	FROM wtfb2_users
	ORDER BY attackKills DESC
	LIMIT 0,10
"
);

$defenseTop=doMySqlQuery
(
"
	SELECT *
	FROM wtfb2_users
	ORDER BY defenseKills DESC
	LIMIT 0,10
"
);

$buildingsQuery=array();
foreach($config['buildings'] as $key=>$value)
{
	$buildingsQuery[]="MAX(${value['buildingLevelDbName']}) AS ${value['buildingLevelDbName']}";
}
$bQuery=implode(',',$buildingsQuery);

$theBiggest=doMySqlQuery(<<< X
SELECT $bQuery FROM wtfb2_villages
X
);

$paramArray=array();
$paramArray['goldTop']=array();
$paramArray['armyTop']=array();
$paramArray['townHallTop']=array();
$paramArray['playerTop']=array();
$paramArray['heroTop']=array();
$paramArray['offenseTop']=array();
$paramArray['defenseTop']=array();
$paramArray['theHighest']=mysql_fetch_assoc($theBiggest);
$paramArray['hidden']=$hidden;
$paramArray['inbuiltpieimg']=$public ? 'inbuiltpie.png?'.time():'showipie.php';

while($row=mysql_fetch_assoc($goldTop)) $paramArray['goldTop'][]=$row;
while($row=mysql_fetch_assoc($armyTop)) $paramArray['armyTop'][]=$row;
while($row=mysql_fetch_assoc($townHallTop)) $paramArray['townHallTop'][]=$row;
while($row=mysql_fetch_assoc($playerTop)) $paramArray['playerTop'][]=$row;
while($row=mysql_fetch_assoc($heroTop)) $paramArray['heroTop'][]=$row;
while($row=mysql_fetch_assoc($offenseTop)) $paramArray['offenseTop'][]=$row;
while($row=mysql_fetch_assoc($defenseTop)) $paramArray['defenseTop'][]=$row;

$tmp=new Template('templates/oraclepub.php',$paramArray);
$page=new Template('templates/basiclayout.php',array('title'=>'Oracle','content'=>$tmp->getContents()));

if (!$public)
{
	$page->render();
}
else
{
	if ($secondsElapsed>=86400*7/$config['serverSpeed'])
	{
		// render pie
		imagepng(getInbuiltPie(),"inbuiltpie.png");
		// render page
		ob_start();
		$page->render();
		$str=ob_get_contents();
		$f=fopen("oraclecache.htm","w+t");
		fwrite($f,$str);
		fclose($f);
		ob_end_flush();
		doMySqlQuery("UPDATE wtfb2_worldupdate SET lastOracleTime=CURDATE()");
	}
}

?>


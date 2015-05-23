<?php

require_once('userworkerphps.php');
require_once('villageupdater.php');

if (!isset($_SESSION['userId'])) jumpTo('login.php');

if ($_SESSION['permission']=='inactive') jumpTo('activate.php');

if (villageCount($_SESSION['userId'])>0) jumpTo('game.php');

function settleFirstVillage($userId)
{
	global $config;
	global $language;
	$runSeconds=-secondsToStart();
	if ($runSeconds<0) $runSeconds=0;
	$minRadius=sqrt(($runSeconds/86400*$config[newVillageAreaIncreasePerDay])/3.1415926536);
	$maxRadius=$config['newVillageAreaRadius']+$minRadius;
//	logText("Settling happened: runSeconds: $runSeconds, minRadius: $minRadius, maxRadius: $maxRadius");
	for($i=0;$i<$config['maxSettleAttempts'];$i++)
	{
		$radius=rand($minRadius,$maxRadius);
		$angle=rand(0,359);
		$rad=deg2rad($angle);
		$x=(int)(cos($rad)*$radius);
		$y=(int)(sin($rad)*$radius);
		$q=sqlPrintf("SELECT * FROM wtfb2_villages WHERE (x='{1}') AND (y='{2}')",array($x,$y));
		$r=doMySqlQuery($q,'jumpErrorPage');
		if (mysql_num_rows($r)==0)
		{
			$q=sqlPrintf("INSERT INTO wtfb2_villages (ownerId,villageName,x,y,lastUpdate) VALUES ('{1}','{2}','{3}','{4}',NOW())",array($userId,$language['newvillage'],$x,$y));
			$r=doMySqlQuery($q,'jumpErrorPage');
			$insertId=mysql_insert_id();
			doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_worldevents (x,y,eventTime,type) VALUES ({1},{2},NOW(),'settle')",array($x,$y)));
			doMySqlQuery(sqlPrintf("UPDATE wtfb2_heroes SET inVillage='{1}',offense=0,defense=0 WHERE (ownerId='{2}')",array($insertId,$userId)));
			doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET expansionPoints=0,gold={1},lastUpdate=NOW(),regDate=NOW() WHERE (id='{2}')",array($config['startGold'],$userId))); // reset player.
			doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_worldevents (x,y,eventTime,type) VALUES ('{1}','{2}',NOW(),'settle')",array($x,$y)));
			updateAllVillages($userId);
			updatePlayer($userId);
			jumpTo("game.php?x=$x&y=$y");
		}
		
	}
	jumpErrorPage($language['toomanyvillagescantspawn']);
	
}

settleFirstVillage($_SESSION['userId']);

?>
